<?php

namespace App\Services;

use App\Models\TradingAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BrokerAnalyticsService
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Safely convert an amount and track skipped conversions.
     */
    private function convertAmountSafely(
        float|int|string $amount,
        ?string $fromCurrency,
        string $toCurrency,
        array &$stats,
        string $context
    ): ?float {
        $stats['attempted'] = ($stats['attempted'] ?? 0) + 1;

        $converted = $this->currencyService->safeConvert($amount, $fromCurrency, $toCurrency);

        if ($converted === null) {
            $stats['skipped'] = ($stats['skipped'] ?? 0) + 1;
            Log::warning('Broker analytics conversion skipped', [
                'context' => $context,
                'amount' => $amount,
                'from' => $fromCurrency,
                'to' => $toCurrency,
            ]);

            return null;
        }

        $stats['success'] = ($stats['success'] ?? 0) + 1;

        return $converted;
    }

    private function buildCacheKey(string $prefix, array $payload): string
    {
        ksort($payload);
        $normalized = json_encode($payload);
        $userId = auth()->id() ?? 'guest';

        return sprintf(
            'tradevisor:%s:user:%s:%s',
            $prefix,
            $userId,
            hash('sha256', $normalized)
        );
    }

    /**
     * Get comprehensive broker comparison analytics
     * OPTIMIZED: Single-pass architecture — loads all data once, groups by broker.
     */
    public function getBrokerComparison($days = 30, $displayCurrency = 'USD')
    {
        $cacheKey = $this->buildCacheKey('broker.analytics', [
            'days' => $days,
            'currency' => $displayCurrency,
            'broker_count' => TradingAccount::where('is_active', true)->distinct('broker_name')->count(),
        ]);
        
        $ttl = (int) config('analytics.cache.broker_ttl', 1800);

        return Cache::remember($cacheKey, $ttl, function() use ($days, $displayCurrency, $cacheKey) {
            Log::info('Broker analytics cache rebuild (optimized single-pass)', [
                'cache_key' => $cacheKey,
                'days' => $days,
                'currency' => $displayCurrency,
            ]);

            // Preload all currency rates in one query to avoid per-pair Redis lookups
            CurrencyService::preloadRates();

            // === SINGLE-PASS: Load ALL data upfront ===
            $allAccounts = TradingAccount::whereNotNull('broker_name')
                ->where('broker_name', '!=', '')
                ->get();

            $accountsByBroker = $allAccounts->groupBy('broker_name');

            $activeBrokerNames = $allAccounts->where('is_active', true)
                ->pluck('broker_name')->unique()->values();

            // Load ALL closed deals for the period with their trading account (one query)
            $allDeals = Deal::closedTrades()
                ->dateRange(now()->subDays($days))
                ->with('tradingAccount')
                ->get();

            // Group deals by broker_name via their trading account
            $dealsByBroker = $allDeals->groupBy(function ($deal) {
                return $deal->tradingAccount->broker_name ?? '__unknown__';
            });

            // DB-level cost aggregation per broker per currency (for getCostAnalysis)
            $costAggregates = $this->getCostAggregatesByBroker($days);

            // DB-level performance aggregation per broker per currency (for getPerformanceMetrics)
            $perfAggregates = $this->getPerformanceAggregatesByBroker($days);

            Log::info('Broker analytics data loaded', [
                'total_accounts' => $allAccounts->count(),
                'total_deals' => $allDeals->count(),
                'active_brokers' => $activeBrokerNames->count(),
            ]);

            $analytics = [];
            
            foreach ($activeBrokerNames as $brokerName) {
                $brokerAccounts = $accountsByBroker->get($brokerName, collect());
                $brokerDeals = $dealsByBroker->get($brokerName, collect());

                $analytics[] = [
                    'broker_name' => $brokerName,
                    'accounts' => $this->getAccountStats($brokerName, $days, $displayCurrency, $brokerAccounts),
                    'spreads' => $this->getSpreadAnalysis($brokerName, $days, $brokerDeals),
                    'costs' => $this->getCostAnalysis($brokerName, $days, $displayCurrency, $costAggregates),
                    'slippage' => $this->getSlippageStats($brokerName, $days, $brokerDeals),
                    'reliability' => $this->getReliabilityMetrics($brokerName, $days, $brokerAccounts),
                    'performance' => $this->getPerformanceMetrics($brokerName, $days, $displayCurrency, $perfAggregates),
                ];
            }

            // Free memory early
            unset($allDeals, $dealsByBroker, $allAccounts);
            
            return [
                'brokers' => $analytics,
                'summary' => $this->generateSummary($analytics),
                'top_symbols' => $this->getTopSymbols($days),
                'display_currency' => $displayCurrency,
            ];
        });
    }

    /**
     * DB-level cost aggregation grouped by broker and currency.
     * Returns a nested collection: broker_name -> currency -> {commission, swap, fee, volume, count}
     */
    private function getCostAggregatesByBroker($days)
    {
        $rows = DB::table('deals')
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.broker_name',
                DB::raw("COALESCE(trading_accounts.account_currency, 'USD') as currency"),
                DB::raw('SUM(ABS(deals.commission)) as total_commission'),
                DB::raw('SUM(deals.swap) as total_swap'),
                DB::raw('SUM(ABS(deals.fee)) as total_fee'),
                DB::raw('SUM(deals.volume) as total_volume'),
                DB::raw('COUNT(*) as trade_count')
            )
            ->whereNotNull('deals.symbol')
            ->where('deals.symbol', '!=', '')
            ->whereIn('deals.entry', ['out', 'inout'])
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy('trading_accounts.broker_name', DB::raw("COALESCE(trading_accounts.account_currency, 'USD')"))
            ->get();

        return $rows->groupBy('broker_name');
    }

    /**
     * DB-level performance aggregation grouped by broker and currency.
     * Returns a nested collection: broker_name -> [{currency, total_profit, winning, losing, volume, count}]
     */
    private function getPerformanceAggregatesByBroker($days)
    {
        $rows = DB::table('deals')
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.broker_name',
                DB::raw("COALESCE(trading_accounts.account_currency, 'USD') as currency"),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN deals.profit ELSE 0 END) as gross_profit'),
                DB::raw('SUM(CASE WHEN deals.profit < 0 THEN ABS(deals.profit) ELSE 0 END) as gross_loss'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(CASE WHEN deals.profit < 0 THEN 1 ELSE 0 END) as losing_trades'),
                DB::raw('SUM(deals.volume) as total_volume'),
                DB::raw('COUNT(*) as trade_count')
            )
            ->whereNotNull('deals.symbol')
            ->where('deals.symbol', '!=', '')
            ->whereIn('deals.entry', ['out', 'inout'])
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy('trading_accounts.broker_name', DB::raw("COALESCE(trading_accounts.account_currency, 'USD')"))
            ->get();

        return $rows->groupBy('broker_name');
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
     * OPTIMIZED: Accepts pre-loaded accounts collection
     */
    private function getAccountStats($brokerName, $days, $displayCurrency, $accounts = null)
    {
        if ($accounts === null) {
            $accounts = TradingAccount::where('broker_name', $brokerName)->get();
        }
        $totalAccounts = $accounts->count();
        $activeAccounts = $accounts->where('is_active', true)->count();
        
        $recentlyActive = $accounts->filter(function ($account) use ($days) {
            return $account->last_sync_at && $account->last_sync_at->gte(now()->subDays($days));
        })->count();

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
     * OPTIMIZED: Accepts pre-loaded deals collection
     */
    private function getSpreadAnalysis($brokerName, $days, $brokerDeals = null)
    {
        if ($brokerDeals === null) {
            $accountIds = TradingAccount::where('broker_name', $brokerName)->pluck('id');
            $brokerDeals = Deal::closedTrades()
                ->whereIn('trading_account_id', $accountIds)
                ->dateRange(now()->subDays($days))
                ->orderBy('time')
                ->get();
        }

        $allDeals = $brokerDeals->sortBy('time');

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
     * OPTIMIZED: Uses pre-aggregated DB data (GROUP BY broker, currency) instead of iterating deals
     */
    private function getCostAnalysis($brokerName, $days, $displayCurrency, $costAggregates = null)
    {
        // Use pre-aggregated data if available
        if ($costAggregates !== null) {
            $brokerRows = $costAggregates->get($brokerName, collect());
        } else {
            // Fallback: run the aggregation query for just this broker
            $brokerRows = DB::table('deals')
                ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
                ->select(
                    DB::raw("COALESCE(trading_accounts.account_currency, 'USD') as currency"),
                    DB::raw('SUM(ABS(deals.commission)) as total_commission'),
                    DB::raw('SUM(deals.swap) as total_swap'),
                    DB::raw('SUM(ABS(deals.fee)) as total_fee'),
                    DB::raw('SUM(deals.volume) as total_volume'),
                    DB::raw('COUNT(*) as trade_count')
                )
                ->where('trading_accounts.broker_name', $brokerName)
                ->whereNotNull('deals.symbol')
                ->where('deals.symbol', '!=', '')
                ->whereIn('deals.entry', ['out', 'inout'])
                ->where('deals.time', '>=', now()->subDays($days))
                ->groupBy(DB::raw("COALESCE(trading_accounts.account_currency, 'USD')"))
                ->get();
        }

        if ($brokerRows->isEmpty()) {
            return null;
        }

        $conversionStats = ['attempted' => 0, 'success' => 0, 'skipped' => 0];
        $totalCommission = 0.0;
        $totalSwap = 0.0;
        $totalFees = 0.0;
        $totalVolume = 0.0;
        $totalTradeCount = 0;

        // Convert aggregated totals per currency (a few rows) instead of per deal (thousands)
        foreach ($brokerRows as $row) {
            $currency = $row->currency ?: 'USD';
            $totalTradeCount += $row->trade_count;
            $totalVolume += $row->total_volume;

            $commission = $this->convertAmountSafely(
                (float) $row->total_commission,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_costs_commission'
            );
            if ($commission !== null) {
                $totalCommission += $commission;
            }

            $swap = $this->convertAmountSafely(
                (float) $row->total_swap,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_costs_swap'
            );
            if ($swap !== null) {
                $totalSwap += $swap;
            }

            $fee = $this->convertAmountSafely(
                (float) $row->total_fee,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_costs_fee'
            );
            if ($fee !== null) {
                $totalFees += $fee;
            }
        }

        $tradeCount = $totalTradeCount;

        if ($conversionStats['skipped'] > 0) {
            Log::warning('Broker cost analysis conversions skipped', [
                'broker' => $brokerName,
                'days' => $days,
                'attempted' => $conversionStats['attempted'],
                'successful' => $conversionStats['success'],
                'skipped' => $conversionStats['skipped'],
            ]);
        }

        return [
            'total_commission' => round($totalCommission, 2),
            'total_swap' => round($totalSwap, 2),
            'total_fees' => round($totalFees, 2),
            'avg_commission_per_trade' => $tradeCount > 0 ? round($totalCommission / $tradeCount, 2) : 0,
            'avg_commission_per_lot' => $totalVolume > 0 ? round($totalCommission / $totalVolume, 2) : 0,
            'total_cost' => round($totalCommission + abs($totalSwap) + $totalFees, 2),
            'cost_per_trade' => $tradeCount > 0 ? round(($totalCommission + abs($totalSwap) + $totalFees) / $tradeCount, 2) : 0,
            'conversion_attempts' => $conversionStats['attempted'],
            'conversion_successful' => $conversionStats['success'],
            'conversion_skipped' => $conversionStats['skipped'],
        ];
    }

    /**
     * Calculate slippage statistics
     * OPTIMIZED: Accepts pre-loaded deals collection
     */
    private function getSlippageStats($brokerName, $days, $brokerDeals = null)
    {
        if ($brokerDeals === null) {
            $accountIds = TradingAccount::where('broker_name', $brokerName)->pluck('id');
            $brokerDeals = Deal::closedTrades()
                ->whereIn('trading_account_id', $accountIds)
                ->dateRange(now()->subDays($days))
                ->get();
        }

        $deals = $brokerDeals;

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
     * OPTIMIZED: Accepts pre-loaded accounts collection
     */
    private function getReliabilityMetrics($brokerName, $days, $accounts = null)
    {
        if ($accounts === null) {
            $accounts = TradingAccount::where('broker_name', $brokerName)->get();
        }

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
     * OPTIMIZED: Uses pre-aggregated DB data (GROUP BY broker, currency) instead of iterating deals
     */
    private function getPerformanceMetrics($brokerName, $days, $displayCurrency, $perfAggregates = null)
    {
        // Use pre-aggregated data if available
        if ($perfAggregates !== null) {
            $brokerRows = $perfAggregates->get($brokerName, collect());
        } else {
            // Fallback: run the aggregation query for just this broker
            $brokerRows = DB::table('deals')
                ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
                ->select(
                    DB::raw("COALESCE(trading_accounts.account_currency, 'USD') as currency"),
                    DB::raw('SUM(deals.profit) as total_profit'),
                    DB::raw('SUM(CASE WHEN deals.profit > 0 THEN deals.profit ELSE 0 END) as gross_profit'),
                    DB::raw('SUM(CASE WHEN deals.profit < 0 THEN ABS(deals.profit) ELSE 0 END) as gross_loss'),
                    DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                    DB::raw('SUM(CASE WHEN deals.profit < 0 THEN 1 ELSE 0 END) as losing_trades'),
                    DB::raw('SUM(deals.volume) as total_volume'),
                    DB::raw('COUNT(*) as trade_count')
                )
                ->where('trading_accounts.broker_name', $brokerName)
                ->whereNotNull('deals.symbol')
                ->where('deals.symbol', '!=', '')
                ->whereIn('deals.entry', ['out', 'inout'])
                ->where('deals.time', '>=', now()->subDays($days))
                ->groupBy(DB::raw("COALESCE(trading_accounts.account_currency, 'USD')"))
                ->get();
        }

        if ($brokerRows->isEmpty()) {
            return null;
        }

        $conversionStats = ['attempted' => 0, 'success' => 0, 'skipped' => 0];
        $totalProfit = 0.0;
        $winningTradesCount = 0;
        $losingTradesCount = 0;
        $grossProfit = 0.0;
        $grossLoss = 0.0;
        $totalVolume = 0.0;
        $totalTradeCount = 0;

        // Convert aggregated totals per currency (a few rows) instead of per deal (thousands)
        foreach ($brokerRows as $row) {
            $currency = $row->currency ?: 'USD';
            $totalTradeCount += $row->trade_count;
            $totalVolume += $row->total_volume;
            $winningTradesCount += $row->winning_trades;
            $losingTradesCount += $row->losing_trades;

            $convertedProfit = $this->convertAmountSafely(
                (float) $row->total_profit,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_performance_profit'
            );

            if ($convertedProfit !== null) {
                $totalProfit += $convertedProfit;
            }

            $convertedGrossProfit = $this->convertAmountSafely(
                (float) $row->gross_profit,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_performance_gross_profit'
            );
            if ($convertedGrossProfit !== null) {
                $grossProfit += $convertedGrossProfit;
            }

            $convertedGrossLoss = $this->convertAmountSafely(
                (float) $row->gross_loss,
                $currency,
                $displayCurrency,
                $conversionStats,
                'broker_performance_gross_loss'
            );
            if ($convertedGrossLoss !== null) {
                $grossLoss += $convertedGrossLoss;
            }
        }

        $countedTrades = $totalTradeCount;

        if ($countedTrades === 0) {
            Log::warning('Broker performance: no trades found', [
                'broker' => $brokerName,
                'days' => $days,
            ]);

            return null;
        }

        if ($conversionStats['skipped'] > 0) {
            Log::warning('Broker performance conversions partial skip', [
                'broker' => $brokerName,
                'days' => $days,
                'attempted' => $conversionStats['attempted'],
                'successful' => $conversionStats['success'],
                'skipped' => $conversionStats['skipped'],
            ]);
        }

        $winRate = round(($winningTradesCount / $countedTrades) * 100, 1);

        $profitFactor = $grossLoss > 0
            ? round($grossProfit / $grossLoss, 2)
            : ($grossProfit > 0 ? 999 : 0);

        return [
            'total_trades' => $countedTrades,
            'total_profit' => round($totalProfit, 2),
            'win_rate' => $winRate,
            'profit_factor' => $profitFactor,
            'avg_profit_per_trade' => round($totalProfit / $countedTrades, 2),
            'total_volume' => round($totalVolume, 2),
            'conversion_attempts' => $conversionStats['attempted'],
            'conversion_successful' => $conversionStats['success'],
            'conversion_skipped' => $conversionStats['skipped'],
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
     * OPTIMIZED: SQL-level JOIN with symbol_mappings for normalization
     */
    private function getTopSymbols($days)
    {
        return DB::table('deals')
            ->leftJoin('symbol_mappings', 'deals.symbol', '=', 'symbol_mappings.raw_symbol')
            ->select(
                DB::raw('COALESCE(symbol_mappings.normalized_symbol, deals.symbol) as normalized_symbol'),
                DB::raw('COUNT(*) as trades')
            )
            ->whereNotNull('deals.symbol')
            ->where('deals.symbol', '!=', '')
            ->whereIn('deals.entry', ['out', 'inout'])
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy(DB::raw('COALESCE(symbol_mappings.normalized_symbol, deals.symbol)'))
            ->orderBy('trades', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'symbol' => $row->normalized_symbol,
                    'trades' => (int) $row->trades,
                ];
            });
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