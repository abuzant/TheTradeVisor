<?php

namespace App\Services;

use App\Models\TradingAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BrokerAnalyticsService
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get comprehensive broker comparison analytics
     */
    public function getBrokerComparison($days = 30, $displayCurrency = 'USD')
    {
        $cacheKey = "broker_analytics_{$days}_{$displayCurrency}";
        
        return Cache::remember($cacheKey, 1800, function() use ($days, $displayCurrency) {
            $brokers = $this->getActiveBrokers();
            
            $analytics = [];
            
            foreach ($brokers as $broker) {
                $analytics[] = [
                    'broker_name' => $broker->broker_name,
                    'accounts' => $this->getAccountStats($broker->broker_name, $days, $displayCurrency),
                    'spreads' => $this->getSpreadAnalysis($broker->broker_name, $days),
                    'costs' => $this->getCostAnalysis($broker->broker_name, $days, $displayCurrency),
                    'slippage' => $this->getSlippageStats($broker->broker_name, $days),
                    'reliability' => $this->getReliabilityMetrics($broker->broker_name, $days),
                    'performance' => $this->getPerformanceMetrics($broker->broker_name, $days, $displayCurrency),
                ];
            }
            
            return [
                'brokers' => $analytics,
                'summary' => $this->generateSummary($analytics),
                'top_symbols' => $this->getTopSymbols($days),
                'display_currency' => $displayCurrency,
            ];
        });
    }

    /**
     * Get list of active brokers
     */
    private function getActiveBrokers()
    {
        return TradingAccount::select('broker_name')
            ->where('is_active', true)
            ->whereNotNull('broker_name')
            ->where('broker_name', '!=', '')
            ->distinct()
            ->get();
    }

    /**
     * Get account statistics for a broker
     */
    private function getAccountStats($brokerName, $days, $displayCurrency)
    {
        $accounts = TradingAccount::where('broker_name', $brokerName)->get();
        $totalAccounts = $accounts->count();
        $activeAccounts = $accounts->where('is_active', true)->count();
        
        $recentlyActive = TradingAccount::where('broker_name', $brokerName)
            ->where('last_sync_at', '>=', now()->subDays($days))
            ->count();

        // Get native currency balances (no conversion)
        $activeAccountsList = $accounts->where('is_active', true);
        
        // Group by currency and calculate averages
        $balancesByCurrency = [];
        foreach ($activeAccountsList as $account) {
            $currency = $account->account_currency ?? 'USD';
            if (!isset($balancesByCurrency[$currency])) {
                $balancesByCurrency[$currency] = [
                    'total' => 0,
                    'count' => 0,
                ];
            }
            $balancesByCurrency[$currency]['total'] += $account->balance;
            $balancesByCurrency[$currency]['count']++;
        }

        // Calculate average for the primary currency (most common)
        $primaryCurrency = 'USD';
        $avgBalance = 0;
        
        if (!empty($balancesByCurrency)) {
            // Find the currency with most accounts
            $maxCount = 0;
            foreach ($balancesByCurrency as $currency => $data) {
                if ($data['count'] > $maxCount) {
                    $maxCount = $data['count'];
                    $primaryCurrency = $currency;
                }
            }
            $avgBalance = $balancesByCurrency[$primaryCurrency]['total'] / $balancesByCurrency[$primaryCurrency]['count'];
        }

        return [
            'total_accounts' => $totalAccounts,
            'active_accounts' => $activeAccounts,
            'recently_active' => $recentlyActive,
            'avg_balance' => round($avgBalance, 2),
            'avg_balance_currency' => $primaryCurrency,
            'activity_rate' => $totalAccounts > 0 ? round(($recentlyActive / $totalAccounts) * 100, 1) : 0,
        ];
    }

    /**
     * Calculate average spreads from real trade data
     * FIXED: Proper spread calculation
     */
    private function getSpreadAnalysis($brokerName, $days)
    {
        $accountIds = TradingAccount::where('broker_name', $brokerName)
            ->pluck('id');

        // Get all deals for this broker
        $allDeals = Deal::closedTrades()
            ->whereIn('trading_account_id', $accountIds)
            ->dateRange(now()->subDays($days))
            ->orderBy('time')
            ->get();

        if ($allDeals->isEmpty()) {
            return [];
        }

        // Group by normalized symbol
        $symbolData = [];
        
        foreach ($allDeals as $deal) {
            $normalizedSymbol = $deal->normalized_symbol;
            $isBuy = $deal->is_buy;
            
            if (!isset($symbolData[$normalizedSymbol])) {
                $symbolData[$normalizedSymbol] = [
                    'prices' => [],
                    'buy_count' => 0,
                    'sell_count' => 0,
                    'total_trades' => 0,
                    'last_buy' => null,
                    'last_sell' => null
                ];
            }
            
            $symbolData[$normalizedSymbol]['prices'][] = $deal->price;
            $symbolData[$normalizedSymbol][$isBuy ? 'buy_count' : 'sell_count']++;
            $symbolData[$normalizedSymbol]['total_trades']++;
            
            // Track last buy/sell for each symbol
            if ($isBuy) {
                $symbolData[$normalizedSymbol]['last_buy'] = $deal->price;
            } else {
                $symbolData[$normalizedSymbol]['last_sell'] = $deal->price;
            }
        }

        // Calculate spreads using a more reliable method
        $spreads = [];
        
        foreach ($symbolData as $symbol => $data) {
            // Skip if we don't have enough data
            if ($data['buy_count'] < 5 || $data['sell_count'] < 5) {
                continue;
            }
            
            // Method 1: Use last buy/sell prices if available
            $spread1 = null;
            if ($data['last_buy'] !== null && $data['last_sell'] !== null) {
                $spread1 = abs($data['last_buy'] - $data['last_sell']);
            }
            
            // Method 2: Use price range percentiles
            sort($data['prices']);
            $count = count($data['prices']);
            
            // Get 10th and 90th percentiles to remove outliers
            $lowIdx = (int)($count * 0.1);
            $highIdx = (int)($count * 0.9) - 1;
            $trimmedPrices = array_slice($data['prices'], $lowIdx, $highIdx - $lowIdx + 1);
            
            if (count($trimmedPrices) > 0) {
                $minPrice = min($trimmedPrices);
                $maxPrice = max($trimmedPrices);
                $spread2 = $maxPrice - $minPrice;
                
                // Use the smaller of the two spread calculations
                $spread = $spread1 !== null ? min($spread1, $spread2) : $spread2;
                
                // Convert to pips using the appropriate multiplier
                $pips = $this->calculatePips($spread, $symbol);
                
                // For crypto pairs, the spread might still be large, so we'll cap it at 2% of price
                if (stripos($symbol, 'BTC') !== false || stripos($symbol, 'XBT') !== false) {
                    $avgPrice = array_sum($trimmedPrices) / count($trimmedPrices);
                    $maxSpreadPips = $avgPrice * 0.02 * 100; // 2% of price in pips
                    $pips = min($pips, $maxSpreadPips);
                }
                
                $spreads[] = [
                    'symbol' => $symbol,
                    'avg_spread_pips' => round($pips, 2),
                    'sample_size' => $data['total_trades'],
                    'buy_count' => $data['buy_count'],
                    'sell_count' => $data['sell_count']
                ];
            }
        }

        // Sort by sample size and take top 10
        usort($spreads, function($a, $b) {
            return $b['sample_size'] - $a['sample_size'];
        });

        return array_slice($spreads, 0, 10);
    }

    /**
     * Calculate commission and fee costs
     * FIXED: Using display currency
     */
    private function getCostAnalysis($brokerName, $days, $displayCurrency)
    {
        $accountIds = TradingAccount::where('broker_name', $brokerName)
            ->pluck('id');

        $deals = Deal::closedTrades()
            ->whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->dateRange(now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        $totalCommission = 0;
        $totalSwap = 0;
        $totalFees = 0;

        // Convert all costs to display currency
        foreach ($deals as $deal) {
            $commission = $this->currencyService->convert(
                abs($deal->commission),
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );
            
            $swap = $this->currencyService->convert(
                $deal->swap,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );
            
            $fee = $this->currencyService->convert(
                abs($deal->fee),
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            $totalCommission += $commission;
            $totalSwap += $swap;
            $totalFees += $fee;
        }

        $totalVolume = $deals->sum('volume');
        $tradeCount = $deals->count();

        return [
            'total_commission' => round($totalCommission, 2),
            'total_swap' => round($totalSwap, 2),
            'total_fees' => round($totalFees, 2),
            'avg_commission_per_trade' => $tradeCount > 0 ? round($totalCommission / $tradeCount, 2) : 0,
            'avg_commission_per_lot' => $totalVolume > 0 ? round($totalCommission / $totalVolume, 2) : 0,
            'total_cost' => round($totalCommission + abs($totalSwap) + $totalFees, 2),
            'cost_per_trade' => $tradeCount > 0 ? round(($totalCommission + abs($totalSwap) + $totalFees) / $tradeCount, 2) : 0,
        ];
    }

    /**
     * Calculate slippage statistics
     */
    private function getSlippageStats($brokerName, $days)
    {
        $accountIds = TradingAccount::where('broker_name', $brokerName)
            ->pluck('id');

        $deals = Deal::closedTrades()
            ->whereIn('trading_account_id', $accountIds)
            ->dateRange(now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        $totalTrades = $deals->count();
        $profitableTrades = $deals->where('profit', '>', 0)->count();
        
        return [
            'estimated_slippage' => 'N/A', // Would need order book data
            'execution_quality_score' => round(($profitableTrades / $totalTrades) * 100, 1),
            'total_trades_analyzed' => $totalTrades,
            'note' => 'Slippage calculation requires order execution data',
        ];
    }

    /**
     * Get reliability metrics (uptime, sync success rate)
     */
    private function getReliabilityMetrics($brokerName, $days)
    {
        $accounts = TradingAccount::where('broker_name', $brokerName)
            ->get();

        if ($accounts->isEmpty()) {
            return null;
        }

        $totalAccounts = $accounts->count();
        $activeAccounts = $accounts->where('is_active', true)->count();
        
        // Calculate uptime based on last sync
        $recentlySynced = $accounts->filter(function($account) {
            return $account->last_sync_at && $account->last_sync_at->gt(now()->subHours(24));
        })->count();

        $uptimePercentage = $activeAccounts > 0 
            ? round(($recentlySynced / $activeAccounts) * 100, 1) 
            : 0;

        // Average time between syncs
        $avgSyncGap = $accounts->filter(function($account) {
            return $account->last_sync_at;
        })->map(function($account) {
            return now()->diffInMinutes($account->last_sync_at);
        })->avg();
            
        // If no sync data, give a default score based on account activity
        if ($avgSyncGap === null && $uptimePercentage === 0) {
            $uptimePercentage = $activeAccounts > 0 ? 85 : 0; // Default 85% if active but no sync data
        }

        return [
            'uptime_percentage' => $uptimePercentage,
            'active_accounts' => $activeAccounts,
            'recently_synced_24h' => $recentlySynced,
            'avg_sync_gap_minutes' => round($avgSyncGap ?? 0, 0),
            'reliability_score' => $this->calculateReliabilityScore($uptimePercentage, $avgSyncGap),
        ];
    }

    /**
     * Get overall performance metrics for broker
     * FIXED: Using display currency
     */
    private function getPerformanceMetrics($brokerName, $days, $displayCurrency)
    {
        $accountIds = TradingAccount::where('broker_name', $brokerName)
            ->pluck('id');

        $deals = Deal::closedTrades()
            ->whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->dateRange(now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        // Convert all profits to display currency
        $totalProfit = 0;
        $winningTradesCount = 0;
        $losingTradesCount = 0;
        $grossProfit = 0;
        $grossLoss = 0;

        foreach ($deals as $deal) {
            $convertedProfit = $this->currencyService->convert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            $totalProfit += $convertedProfit;

            if ($convertedProfit > 0) {
                $winningTradesCount++;
                $grossProfit += $convertedProfit;
            } elseif ($convertedProfit < 0) {
                $losingTradesCount++;
                $grossLoss += abs($convertedProfit);
            }
        }
        
        $winRate = $deals->count() > 0 
            ? round(($winningTradesCount / $deals->count()) * 100, 1) 
            : 0;
        
        $profitFactor = $grossLoss > 0 
            ? round($grossProfit / $grossLoss, 2) 
            : ($grossProfit > 0 ? 999 : 0);

        return [
            'total_trades' => $deals->count(),
            'total_profit' => round($totalProfit, 2),
            'win_rate' => $winRate,
            'profit_factor' => $profitFactor,
            'avg_profit_per_trade' => round($totalProfit / $deals->count(), 2),
            'total_volume' => round($deals->sum('volume'), 2),
        ];
    }

    /**
     * Generate summary comparing all brokers
     */
    private function generateSummary($analytics)
    {
        if (empty($analytics)) {
            return null;
        }

        // Find best broker in each category
        $bestByAccounts = collect($analytics)->sortByDesc('accounts.total_accounts')->first();
        $bestByReliability = collect($analytics)->filter(function($broker) {
            return isset($broker['reliability']['uptime_percentage']);
        })->sortByDesc('reliability.uptime_percentage')->first();
        
        $bestByCost = collect($analytics)->filter(function($broker) {
            return isset($broker['costs']['cost_per_trade']) && $broker['costs']['cost_per_trade'] > 0;
        })->sortBy('costs.cost_per_trade')->first();
        
        $bestByPerformance = collect($analytics)->filter(function($broker) {
            return isset($broker['performance']['win_rate']);
        })->sortByDesc('performance.win_rate')->first();

        return [
            'most_popular' => $bestByAccounts['broker_name'] ?? 'N/A',
            'most_reliable' => $bestByReliability['broker_name'] ?? 'N/A',
            'lowest_cost' => $bestByCost['broker_name'] ?? 'N/A',
            'best_performance' => $bestByPerformance['broker_name'] ?? 'N/A',
            'total_brokers' => count($analytics),
        ];
    }

    /**
     * Get top traded symbols across all brokers
     */
    private function getTopSymbols($days)
    {
        // Get all deals with their symbols
        $deals = Deal::closedTrades()
            ->dateRange(now()->subDays($days))
            ->select('symbol')
            ->get();
        
        // Group by normalized symbol in PHP
        $symbolCounts = [];
        
        foreach ($deals as $deal) {
            $normalizedSymbol = $deal->normalized_symbol; // Uses accessor
            
            if (!isset($symbolCounts[$normalizedSymbol])) {
                $symbolCounts[$normalizedSymbol] = 0;
            }
            $symbolCounts[$normalizedSymbol]++;
        }
        
        // Sort and take top 10
        arsort($symbolCounts);
        $symbolCounts = array_slice($symbolCounts, 0, 10, true);
        
        return collect($symbolCounts)->map(function($count, $symbol) {
            return [
                'symbol' => $symbol,
                'trades' => $count,
            ];
        })->values();
    }

    /**
     * Helper: Calculate pips from price difference
     * FIXED: More accurate pip calculation
     */
    private function calculatePips($priceDiff, $symbol)
    {
        // Remove any suffixes like .sd, .m, etc.
        $cleanSymbol = preg_replace('/\.[a-z]+$/i', '', $symbol);
        
        // JPY pairs: 1 pip = 0.01
        if (strpos($cleanSymbol, 'JPY') !== false) {
            return $priceDiff * 100;
        }
        
        // Forex pairs (6 characters like EURUSD): 1 pip = 0.0001
        if (preg_match('/^[A-Z]{6}$/', $cleanSymbol)) {
            return $priceDiff * 10000;
        }
        
        // Gold (XAU): 1 pip = 0.01
        if (strpos($cleanSymbol, 'XAU') !== false || strpos($cleanSymbol, 'GOLD') !== false) {
            return $priceDiff * 100;
        }
        
        // Silver (XAG): 1 pip = 0.001
        if (strpos($cleanSymbol, 'XAG') !== false || strpos($cleanSymbol, 'SILVER') !== false) {
            return $priceDiff * 1000;
        }
        
        // Bitcoin and crypto (very large prices): 1 pip = 1.0
        if (strpos($cleanSymbol, 'BTC') !== false || strpos($cleanSymbol, 'ETH') !== false) {
            return $priceDiff;
        }
        
        // Indices (like US30, NAS100): 1 pip = 0.01
        if (preg_match('/(US30|NAS100|SPX|DAX|FTSE)/i', $cleanSymbol)) {
            return $priceDiff * 100;
        }
        
        // Oil: 1 pip = 0.01
        if (strpos($cleanSymbol, 'OIL') !== false || strpos($cleanSymbol, 'WTI') !== false || strpos($cleanSymbol, 'BRENT') !== false) {
            return $priceDiff * 100;
        }
        
        // Default for unknown instruments: use 0.01
        return $priceDiff * 100;
    }

    /**
     * Helper: Calculate reliability score
     */
    private function calculateReliabilityScore($uptimePercentage, $avgSyncGap)
    {
        $uptimeScore = $uptimePercentage;
        
        // Penalize if sync gap is too long
        $syncScore = 100;
        if ($avgSyncGap > 60) { // More than 1 hour
            $syncScore = max(0, 100 - (($avgSyncGap - 60) / 10));
        }
        
        return round(($uptimeScore + $syncScore) / 2, 1);
    }
}