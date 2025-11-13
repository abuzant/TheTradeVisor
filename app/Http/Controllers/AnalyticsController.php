<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    /**
     * Global analytics dashboard
     */
    public function index(Request $request, $days = 30)
    {
        // User must have at least one active account to view global analytics
        $user = $request->user();

        if ($user->tradingAccounts()->where('is_active', true)->count() === 0) {
            return view('analytics.locked');
        }

        // Validate days parameter
        if (!in_array($days, [1, 7, 30])) {
            $days = 30;
        }

        // Cache key for the analytics data
        $cacheKey = "global_analytics_{$days}";
        
        // Try to get cached data first
        $analytics = Cache::remember($cacheKey, 300, function () use ($days) {
            return [
                'overview' => $this->getOverviewStats($days),
                'popular_pairs' => $this->getPopularPairs($days),
                'trading_by_hour' => $this->getTradingByHour($days),
                'regional_activity' => $this->getRegionalActivity($days),
                'broker_distribution' => $this->getBrokerDistribution($days),
                'market_sentiment' => $this->getMarketSentiment($days),
                'top_performers' => $this->getTopPerformers($days),
                'daily_volume_trend' => $this->getDailyVolumeTrend($days),
                'win_rate_by_symbol' => $this->getWinRateBySymbol($days),
                'trading_costs' => $this->getTradingCosts($days),
                'position_sizes' => $this->getPositionSizeDistribution($days),
                'trade_duration' => $this->getTradeDurationStats($days),
                
                // NEW COMPREHENSIVE ANALYTICS
                'country_platform_matrix' => $this->getCountryPlatformMatrix($days),
                'symbol_country_heatmap' => $this->getSymbolCountryHeatmap($days),
                'broker_country_analytics' => $this->getBrokerCountryAnalytics($days),
                'trading_sessions' => $this->getTradingSessionAnalysis($days),
                'risk_analytics' => $this->getRiskAnalytics($days),
                'correlation_matrix' => $this->getCorrelationMatrix($days),
                'performance_leaderboards' => $this->getPerformanceLeaderboards($days),
                'economic_impact' => $this->getEconomicImpactAnalysis($days),
                'real_time_activity' => $this->getRealTimeActivityMonitor(),
                'symbol_performance_trends' => $this->getSymbolPerformanceTrends($days),
                'account_size_analysis' => $this->getAccountSizeAnalysis($days),
                'profit_loss_distribution' => $this->getProfitLossDistribution($days),
                'trading_patterns' => $this->getTradingPatterns($days),
                'market_volatility' => $this->getMarketVolatilityAnalysis($days),
            ];
        });

        // Get display currency (default to USD for global analytics)
        $displayCurrency = 'USD';

        return view('analytics.index', compact('analytics', 'days', 'displayCurrency'));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats($days = 30)
    {
        return [
            'total_traders' => TradingAccount::distinct('user_id')->count('user_id'),
            'active_accounts' => TradingAccount::where('is_active', true)->count(),
            'total_brokers' => TradingAccount::distinct('broker_name')->count('broker_name'),
            'open_positions' => Position::where('is_open', true)->count(),
            'total_volume' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->sum('volume'),
            'total_trades' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->count(),
            'total_profit' => Deal::whereNotNull('symbol')
                                  ->where('symbol', '!=', '')
                                  ->where('time', '>=', now()->subDays($days))
                                  ->sum('profit'),
            'avg_trade_profit' => Deal::whereNotNull('symbol')
                                      ->where('symbol', '!=', '')
                                      ->where('time', '>=', now()->subDays($days))
                                      ->avg('profit'),
            'countries' => TradingAccount::whereNotNull('detected_country')->distinct('detected_country')->count('detected_country'),
        ];
    }

    /**
     * Get most popular trading pairs
     */
    private function getPopularPairs($days = 30)
    {
        return Deal::select('symbol', DB::raw('COUNT(*) as trades'), DB::raw('SUM(volume) as total_volume'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('symbol', '!=', 'UNKNOWN')
            ->where('time', '>=', now()->subDays($days))
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [3])
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'raw_symbol' => $item->symbol,
                    'trades' => $item->trades,
                    'volume' => round($item->total_volume, 2),
                ];
            });
    }

    /**
     * Get trading activity by hour (UTC)
     */
    private function getTradingByHour($days = 30)
    {
        $data = Deal::select(DB::raw('EXTRACT(HOUR FROM time) as hour'), DB::raw('COUNT(*) as count'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
        ->where('time', '>=', now()->subDays($days))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        $result = [];
        for ($i = 0; $i < 24; $i++) {
            $result[] = [
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                'trades' => $data->get($i, 0),
            ];
        }

        return $result;
    }

    /**
     * Get regional trading activity
     */
    private function getRegionalActivity()
    {
        // Try new country_code field first, fallback to detected_country
        $query = TradingAccount::where('is_active', true);
        
        // Check if we have any country data
        $hasCountryCode = TradingAccount::whereNotNull('country_code')->exists();
        $hasDetectedCountry = TradingAccount::whereNotNull('detected_country')->exists();
        
        if ($hasCountryCode) {
            $accounts = $query->whereNotNull('country_code')->limit(100)->get();
            $grouped = $accounts->groupBy('country_code');
            
            $data = $grouped->map(function($accountsByCountry, $countryCode) {
                $totalBalanceUSD = 0;
                $currencyService = app(\App\Services\CurrencyService::class);
                
                foreach ($accountsByCountry as $account) {
                    // Convert each account's balance to USD
                    $balanceUSD = $currencyService->convert(
                        $account->balance,
                        $account->account_currency ?? 'USD',
                        'USD'
                    );
                    $totalBalanceUSD += $balanceUSD;
                }
                
                return [
                    'country' => $accountsByCountry->first()->country_name ?? $countryCode,
                    'country_code' => $countryCode,
                    'accounts' => $accountsByCountry->count(),
                    'balance' => round($totalBalanceUSD, 2),
                ];
            })->sortByDesc('accounts')->take(10)->values();
        } elseif ($hasDetectedCountry) {
            $accounts = $query->whereNotNull('detected_country')->limit(100)->get();
            $grouped = $accounts->groupBy('detected_country');
            
            $data = $grouped->map(function($accountsByCountry, $country) {
                $totalBalanceUSD = 0;
                $currencyService = app(\App\Services\CurrencyService::class);
                
                foreach ($accountsByCountry as $account) {
                    // Convert each account's balance to USD
                    $balanceUSD = $currencyService->convert(
                        $account->balance,
                        $account->account_currency ?? 'USD',
                        'USD'
                    );
                    $totalBalanceUSD += $balanceUSD;
                }
                
                return [
                    'country' => $country,
                    'country_code' => null,
                    'accounts' => $accountsByCountry->count(),
                    'balance' => round($totalBalanceUSD, 2),
                ];
            })->sortByDesc('accounts')->take(10)->values();
        } else {
            // No country data available - return a message
            $accounts = TradingAccount::where('is_active', true)->limit(100)->get();
            $totalBalanceUSD = 0;
            $currencyService = app(\App\Services\CurrencyService::class);
            
            foreach ($accounts as $account) {
                $balanceUSD = $currencyService->convert(
                    $account->balance,
                    $account->account_currency ?? 'USD',
                    'USD'
                );
                $totalBalanceUSD += $balanceUSD;
            }
            
            return collect([
                [
                    'country' => 'No location data available',
                    'country_code' => null,
                    'accounts' => $accounts->count(),
                    'balance' => round($totalBalanceUSD, 2),
                    'note' => 'Country detection requires IP geolocation to be enabled'
                ]
            ]);
        }
        
        return $data;
    }

    /**
     * Get broker distribution
     */
    private function getBrokerDistribution()
    {
        $brokers = TradingAccount::select('broker_name', DB::raw('COUNT(*) as accounts'))
            ->where('is_active', true)
            ->whereNotNull('broker_name')
            ->groupBy('broker_name')
            ->orderBy('accounts', 'desc')
            ->limit(8)
            ->get();
    }

    /**
     * Get market sentiment (buy vs sell positions)
     */
    private function getMarketSentiment()
    {
        // Get open positions
        $positions = Position::select('symbol', 'type', DB::raw('COUNT(*) as count'))
            ->where('is_open', true)
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->groupBy('symbol', 'type')
            ->limit(50)
            ->get();

        // Also get recent deals (last 24 hours) for additional sentiment
        $recentDeals = Deal::select('symbol', 'type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subHours(24))
            ->whereIn('type', ['buy', 'sell'])
            ->groupBy('symbol', 'type')
            ->limit(50)
            ->get();

        $sentiment = [];

        // Process open positions
        foreach ($positions as $position) {
            $normalizedSymbol = \App\Models\SymbolMapping::normalize($position->symbol);
            if (!isset($sentiment[$normalizedSymbol])) {
                $sentiment[$normalizedSymbol] = ['buy' => 0, 'sell' => 0, 'recent_buy' => 0, 'recent_sell' => 0];
            }
            $sentiment[$normalizedSymbol][$position->type] += $position->count;
        }

        // Process recent deals
        foreach ($recentDeals as $deal) {
            $normalizedSymbol = \App\Models\SymbolMapping::normalize($deal->symbol);
            if (!isset($sentiment[$normalizedSymbol])) {
                $sentiment[$normalizedSymbol] = ['buy' => 0, 'sell' => 0, 'recent_buy' => 0, 'recent_sell' => 0];
            }
            $sentiment[$normalizedSymbol]['recent_' . $deal->type] += $deal->count;
        }

        // Calculate sentiment percentage
        $result = [];
        foreach ($sentiment as $symbol => $data) {
            $total = $data['buy'] + $data['sell'];
            $recentTotal = $data['recent_buy'] + $data['recent_sell'];
            
            // Include symbols with either open positions OR recent activity
            if ($total > 0 || $recentTotal > 0) {
                // Use open positions for primary sentiment, recent activity as secondary
                $buyPercent = $total > 0 ? round(($data['buy'] / $total) * 100, 1) : 
                             ($recentTotal > 0 ? round(($data['recent_buy'] / $recentTotal) * 100, 1) : 50);
                $sellPercent = $total > 0 ? round(($data['sell'] / $total) * 100, 1) : 
                              ($recentTotal > 0 ? round(($data['recent_sell'] / $recentTotal) * 100, 1) : 50);
                
                // Determine sentiment
                $sentimentType = 'neutral';
                if ($buyPercent > 60) $sentimentType = 'bullish';
                elseif ($sellPercent > 60) $sentimentType = 'bearish';
                elseif ($buyPercent > 55) $sentimentType = 'slight_bullish';
                elseif ($sellPercent > 55) $sentimentType = 'slight_bearish';
                
                $result[] = [
                    'symbol' => $symbol,
                    'buy_percent' => $buyPercent,
                    'sell_percent' => $sellPercent,
                    'total_positions' => $total,
                    'recent_activity' => $recentTotal,
                    'sentiment_type' => $sentimentType,
                    'dominant' => $buyPercent > $sellPercent ? 'buy' : 'sell',
                ];
            }
        }

        // Sort by total activity (positions + recent)
        usort($result, function($a, $b) {
            $activityA = $a['total_positions'] + $a['recent_activity'];
            $activityB = $b['total_positions'] + $b['recent_activity'];
            return $activityB - $activityA;
        });

        return array_slice($result, 0, 10);
    }

    /**
     * Get top performing symbols (by profit)
     */
    private function getTopPerformers($days = 30)
    {
        return Deal::select('symbol', DB::raw('SUM(profit) as total_profit'), DB::raw('COUNT(*) as trades'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('symbol', '!=', 'UNKNOWN')
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['out', 'inout'])
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [3])
            ->orderByRaw('SUM(profit) DESC')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'raw_symbol' => $item->symbol,
                    'profit' => round($item->total_profit, 2),
                    'trades' => $item->trades,
                    'avg_profit' => round($item->total_profit / $item->trades, 2),
                ];
            });
    }

    /**
     * Get daily volume trend
     */
    private function getDailyVolumeTrend($days = 30)
    {
        return Deal::select(DB::raw('DATE(time) as date'), DB::raw('SUM(volume) as volume'), DB::raw('COUNT(*) as trades'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get win rate by symbol
     */
    private function getWinRateBySymbol($days = 30)
    {
        return Deal::select('symbol',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(profit) as total_profit'))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['out', 'inout'])
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [5])
            ->get()
            ->map(function($item) {
                $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'total_trades' => $item->total_trades,
                    'win_rate' => $winRate,
                    'total_profit' => round($item->total_profit, 2),
                ];
            })
            ->sortByDesc('win_rate')
            ->take(10)
            ->values();
    }

    /**
     * Get trading costs analysis (converted to USD for global multi-account view)
     */
    private function getTradingCosts($days = 30)
    {
        $deals = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->with('tradingAccount')
            ->get();

        $currencyService = app(\App\Services\CurrencyService::class);
        $totalCommissionUSD = 0;
        $totalSwapUSD = 0;
        
        foreach ($deals as $deal) {
            if ($deal->tradingAccount) {
                $currency = $deal->tradingAccount->account_currency ?? 'USD';
                $totalCommissionUSD += $currencyService->convert($deal->commission ?? 0, $currency, 'USD');
                $totalSwapUSD += $currencyService->convert($deal->swap ?? 0, $currency, 'USD');
            } else {
                $totalCommissionUSD += $deal->commission ?? 0;
                $totalSwapUSD += $deal->swap ?? 0;
            }
        }
        
        $totalTrades = $deals->count();
        $totalCosts = $totalCommissionUSD + $totalSwapUSD;

        return [
            'total_commission' => round($totalCommissionUSD, 2),
            'total_swap' => round($totalSwapUSD, 2),
            'total_costs' => round($totalCosts, 2),
            'avg_cost_per_trade' => $totalTrades > 0 ? round($totalCosts / $totalTrades, 2) : 0,
        ];
    }

    /**
     * Get position size distribution
     */
    private function getPositionSizeDistribution($days = 30)
    {
        $sizes = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->pluck('volume');

        return [
            'avg' => round($sizes->avg(), 2),
            'min' => round($sizes->min(), 2),
            'max' => round($sizes->max(), 2),
            'median' => round($sizes->median(), 2),
        ];
    }

    /**
     * Get trade duration statistics
     */
    private function getTradeDurationStats($days = 30)
    {
        // Get positions that were closed in the period
        $closedPositions = Deal::whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', 'out')
            ->count();

        return [
            'total_closed' => $closedPositions,
            'avg_duration' => 'N/A', // Would need position open/close time tracking
        ];
    }

    // ========================================
    // NEW COMPREHENSIVE ANALYTICS METHODS
    // ========================================

    /**
     * Country-Platform Analytics Matrix
     */
    private function getCountryPlatformMatrix($days = 30)
    {
        $data = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.country_code as country',
                'trading_accounts.platform_type',
                'trading_accounts.account_mode',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('SUM(deals.volume) as total_volume'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('AVG(deals.profit) as avg_profit'),
                DB::raw('COUNT(DISTINCT trading_accounts.id) as unique_accounts')
            )
            ->whereNotNull('trading_accounts.country_code')
            ->whereNotNull('trading_accounts.platform_type')
            ->where('deals.time', '>=', now()->subDays($days))
            ->where('deals.symbol', '!=', '')
            ->groupBy('trading_accounts.country_code', 'trading_accounts.platform_type', 'trading_accounts.account_mode')
            ->havingRaw('COUNT(*) >= 1')
            ->orderBy('total_trades', 'desc')
            ->limit(50)
            ->get();

        return $data->map(function($item) {
            $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
            return [
                'country' => $item->country,
                'platform_type' => strtoupper($item->platform_type),
                'account_mode' => ucfirst($item->account_mode),
                'total_trades' => $item->total_trades,
                'total_profit' => round($item->total_profit, 2),
                'total_volume' => round($item->total_volume, 2),
                'win_rate' => $winRate,
                'avg_profit' => round($item->avg_profit, 2),
                'unique_accounts' => $item->unique_accounts,
                'profit_per_trade' => $item->total_trades > 0 ? round($item->total_profit / $item->total_trades, 2) : 0,
            ];
        });
    }

    /**
     * Symbol-Country Performance Heatmap
     */
    private function getSymbolCountryHeatmap($days = 30)
    {
        $data = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'deals.symbol',
                'trading_accounts.country_code as country',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('AVG(deals.profit) as avg_profit'),
                DB::raw('SUM(deals.volume) as total_volume')
            )
            ->whereNotNull('trading_accounts.country_code')
            ->where('deals.time', '>=', now()->subDays($days))
            ->where('deals.symbol', '!=', '')
            ->whereIn('deals.symbol', function($query) {
                $query->select('symbol')
                    ->from('deals')
                    ->where('time', '>=', now()->subDays(30))
                    ->groupBy('symbol')
                    ->havingRaw('COUNT(*) >= 10')
                    ->limit(15);
            })
            ->groupBy('deals.symbol', 'trading_accounts.country_code')
            ->havingRaw('COUNT(*) >= 1')
            ->orderBy('total_trades', 'desc')
            ->limit(100)
            ->get();

        return $data->map(function($item) {
            $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
            $normalizedSymbol = \App\Models\SymbolMapping::normalize($item->symbol);
            
            return [
                'symbol' => $normalizedSymbol,
                'raw_symbol' => $item->symbol,
                'country' => $item->country,
                'total_trades' => $item->total_trades,
                'total_profit' => round($item->total_profit, 2),
                'win_rate' => $winRate,
                'avg_profit' => round($item->avg_profit, 2),
                'total_volume' => round($item->total_volume, 2),
                'performance_score' => $this->calculatePerformanceScore($winRate, $item->avg_profit),
            ];
        });
    }

    /**
     * Broker-Country Analytics
     */
    private function getBrokerCountryAnalytics($days = 30)
    {
        $data = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.broker_name',
                'trading_accounts.detected_country as country',
                DB::raw('COUNT(DISTINCT trading_accounts.id) as unique_accounts'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('SUM(CASE WHEN deals.profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(deals.volume) as total_volume'),
                DB::raw('AVG(deals.profit) as avg_profit')
            )
            ->whereNotNull('trading_accounts.detected_country')
            ->whereNotNull('trading_accounts.broker_name')
            ->where('deals.time', '>=', now()->subDays($days))
            ->where('deals.symbol', '!=', '')
            ->groupBy('trading_accounts.broker_name', 'trading_accounts.detected_country')
            ->havingRaw('COUNT(*) >= 5')
            ->orderBy('total_trades', 'desc')
            ->limit(50)
            ->get();

        return $data->map(function($item) {
            $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
            return [
                'broker_name' => $item->broker_name,
                'country' => $item->country,
                'unique_accounts' => $item->unique_accounts,
                'total_trades' => $item->total_trades,
                'total_profit' => round($item->total_profit, 2),
                'win_rate' => $winRate,
                'total_volume' => round($item->total_volume, 2),
                'avg_profit' => round($item->avg_profit, 2),
                'profit_per_account' => $item->unique_accounts > 0 ? round($item->total_profit / $item->unique_accounts, 2) : 0,
            ];
        });
    }

    /**
     * Trading Session Analysis
     */
    private function getTradingSessionAnalysis($days = 30)
    {
        $data = Deal::select(
                'symbol',
                DB::raw('EXTRACT(HOUR FROM time) as hour'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('AVG(profit) as avg_profit')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->whereIn('symbol', function($query) {
                $query->select('symbol')
                    ->from('deals')
                    ->where('time', '>=', now()->subDays(30))
                    ->groupBy('symbol')
                    ->havingRaw('COUNT(*) >= 20')
                    ->limit(10);
            })
            ->groupBy('symbol', 'hour')
            ->orderBy('symbol')
            ->orderBy('hour')
            ->get();

        $sessions = [
            'Sydney' => ['start' => 0, 'end' => 8],
            'Tokyo' => ['start' => 23, 'end' => 7],
            'London' => ['start' => 8, 'end' => 16],
            'New York' => ['start' => 13, 'end' => 21]
        ];

        $result = [];
        foreach ($sessions as $sessionName => $sessionTime) {
            $sessionData = $data->filter(function($item) use ($sessionTime) {
                $hour = (int)$item->hour;
                if ($sessionTime['start'] > $sessionTime['end']) {
                    return $hour >= $sessionTime['start'] || $hour < $sessionTime['end'];
                }
                return $hour >= $sessionTime['start'] && $hour < $sessionTime['end'];
            });

            $totalTrades = $sessionData->sum('total_trades');
            $totalProfit = $sessionData->sum('total_profit');
            $winningTrades = $sessionData->sum('winning_trades');
            $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

            $result[$sessionName] = [
                'session_name' => $sessionName,
                'time_range' => sprintf('%02d:00-%02d:00 UTC', $sessionTime['start'], $sessionTime['end']),
                'total_trades' => $totalTrades,
                'total_profit' => round($totalProfit, 2),
                'win_rate' => $winRate,
                'total_volume' => round($sessionData->sum('total_volume'), 2),
                'avg_profit' => $totalTrades > 0 ? round($totalProfit / $totalTrades, 2) : 0,
                'top_symbols' => $sessionData->sortByDesc('total_trades')->take(5)->values(),
            ];
        }

        return collect($result)->values();
    }

    /**
     * Risk Analytics Dashboard
     */
    private function getRiskAnalytics($days = 30)
    {
        $data = Deal::select(
                'symbol',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(CASE WHEN profit < 0 THEN 1 ELSE 0 END) as losing_trades'),
                DB::raw('AVG(CASE WHEN profit > 0 THEN profit END) as avg_win'),
                DB::raw('AVG(CASE WHEN profit < 0 THEN profit END) as avg_loss'),
                DB::raw('MIN(profit) as max_loss'),
                DB::raw('MAX(profit) as max_win'),
                DB::raw('STDDEV(profit) as volatility'),
                DB::raw('SUM(volume) as total_volume')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= 10')
            ->orderBy('total_trades', 'desc')
            ->limit(50)
            ->get();

        return $data->map(function($item) {
            $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
            $lossRate = $item->total_trades > 0 ? round(($item->losing_trades / $item->total_trades) * 100, 1) : 0;
            $profitFactor = $item->avg_loss < 0 ? round(abs($item->avg_win / $item->avg_loss), 2) : 0;
            $riskReward = $item->avg_loss < 0 ? round(abs($item->avg_win / $item->avg_loss), 2) : 0;
            $riskScore = $this->calculateRiskScore($winRate, $profitFactor, $item->volatility);
            
            return [
                'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                'total_trades' => $item->total_trades,
                'win_rate' => $winRate,
                'loss_rate' => $lossRate,
                'avg_win' => round($item->avg_win, 2),
                'avg_loss' => round($item->avg_loss, 2),
                'max_win' => round($item->max_win, 2),
                'max_loss' => round($item->max_loss, 2),
                'profit_factor' => $profitFactor,
                'risk_reward_ratio' => $riskReward,
                'volatility' => round($item->volatility, 2),
                'total_profit' => round($item->total_profit, 2),
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
            ];
        })->sortByDesc('risk_score')->values();
    }

    /**
     * Correlation Matrix
     */
    private function getCorrelationMatrix($days = 30)
    {
        // Get daily profit data for top symbols
        $symbols = Deal::select('symbol')
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= 5')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(8)
            ->pluck('symbol');

        $dailyData = Deal::select(
                'symbol',
                DB::raw('DATE(time) as date'),
                DB::raw('SUM(profit) as daily_profit')
            )
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('symbol', $symbols)
            ->groupBy('symbol', DB::raw('DATE(time)'))
            ->orderBy('date')
            ->limit(200)
            ->get()
            ->groupBy('symbol');

        $correlations = [];
        foreach ($symbols as $i => $symbol1) {
            foreach ($symbols as $j => $symbol2) {
                if ($i <= $j) {
                    $correlation = $this->calculateCorrelation(
                        $dailyData->get($symbol1, collect())->pluck('daily_profit'),
                        $dailyData->get($symbol2, collect())->pluck('daily_profit')
                    );
                    
                    $correlations[] = [
                        'symbol1' => \App\Models\SymbolMapping::normalize($symbol1),
                        'symbol2' => \App\Models\SymbolMapping::normalize($symbol2),
                        'correlation' => round($correlation, 3),
                        'strength' => $this->getCorrelationStrength($correlation),
                    ];
                }
            }
        }

        return collect($correlations);
    }

    /**
     * Performance Leaderboards
     */
    private function getPerformanceLeaderboards($days = 30)
    {
        // Top Countries by Profit
        $topCountries = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.detected_country as country',
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('COUNT(DISTINCT trading_accounts.id) as unique_accounts')
            )
            ->whereNotNull('trading_accounts.detected_country')
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy('trading_accounts.detected_country')
            ->havingRaw('COUNT(*) >= 10')
            ->orderBy('total_profit', 'desc')
            ->limit(10)
            ->get();

        // Top Symbols by Win Rate
        $topSymbols = Deal::select(
                'symbol',
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= 20')
            ->orderByRaw('(SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) / COUNT(*)) DESC')
            ->limit(10)
            ->get();

        // Top Brokers by Volume
        $topBrokers = Deal::join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->select(
                'trading_accounts.broker_name',
                DB::raw('SUM(deals.volume) as total_volume'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(deals.profit) as total_profit'),
                DB::raw('COUNT(DISTINCT trading_accounts.id) as unique_accounts')
            )
            ->whereNotNull('trading_accounts.broker_name')
            ->where('deals.time', '>=', now()->subDays($days))
            ->groupBy('trading_accounts.broker_name')
            ->orderBy('total_volume', 'desc')
            ->limit(10)
            ->get();

        return [
            'top_countries' => $topCountries->map(function($item) {
                return [
                    'country' => $item->country,
                    'total_profit' => round($item->total_profit, 2),
                    'total_trades' => $item->total_trades,
                    'unique_accounts' => $item->unique_accounts,
                    'avg_profit_per_account' => $item->unique_accounts > 0 ? round($item->total_profit / $item->unique_accounts, 2) : 0,
                ];
            }),
            'top_symbols' => $topSymbols->map(function($item) {
                $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'win_rate' => $winRate,
                    'total_trades' => $item->total_trades,
                    'total_profit' => round($item->total_profit, 2),
                    'winning_trades' => $item->winning_trades,
                ];
            }),
            'top_brokers' => $topBrokers->map(function($item) {
                return [
                    'broker_name' => $item->broker_name,
                    'total_volume' => round($item->total_volume, 2),
                    'total_trades' => $item->total_trades,
                    'total_profit' => round($item->total_profit, 2),
                    'unique_accounts' => $item->unique_accounts,
                    'avg_volume_per_account' => $item->unique_accounts > 0 ? round($item->total_volume / $item->unique_accounts, 2) : 0,
                ];
            }),
        ];
    }

    /**
     * Economic Impact Analysis (placeholder for future integration)
     */
    private function getEconomicImpactAnalysis($days = 30)
    {
        // This would integrate with economic calendar data
        // For now, showing trading volume around major news hours
        $newsHours = [8, 13, 15]; // London open, NY open, NY close
        
        $data = Deal::select(
                DB::raw('EXTRACT(HOUR FROM time) as hour'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('AVG(profit) as avg_profit')
            )
            ->where('time', '>=', now()->subDays($days))
            ->whereIn(DB::raw('EXTRACT(HOUR FROM time)'), $newsHours)
            ->groupBy('hour')
            ->orderBy('hour')
            ->limit(24)
            ->get();

        return [
            'news_hour_impact' => $data->map(function($item) {
                return [
                    'hour' => (int)$item->hour,
                    'time_label' => sprintf('%02d:00 UTC', $item->hour),
                    'total_trades' => $item->total_trades,
                    'total_volume' => round($item->total_volume, 2),
                    'total_profit' => round($item->total_profit, 2),
                    'avg_profit' => round($item->avg_profit, 2),
                    'impact_type' => $this->getNewsHourType((int)$item->hour),
                ];
            }),
            'volatility_around_news' => $this->getNewsVolatilityAnalysis($days),
        ];
    }

    /**
     * Real-Time Activity Monitor
     */
    private function getRealTimeActivityMonitor()
    {
        $recentActivity = Deal::select(
                'symbol',
                DB::raw('COUNT(*) as trades_last_hour'),
                DB::raw('SUM(profit) as profit_last_hour'),
                DB::raw('SUM(volume) as volume_last_hour')
            )
            ->where('time', '>=', now()->subHour())
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->orderBy('trades_last_hour', 'desc')
            ->limit(10)
            ->get();

        $openPositions = Position::select(
                'symbol',
                DB::raw('COUNT(*) as open_positions'),
                DB::raw('SUM(profit) as total_profit')
            )
            ->where('is_open', true)
            ->where('symbol', '!=', '')
            ->groupBy('symbol')
            ->orderBy('open_positions', 'desc')
            ->limit(10)
            ->get();

        return [
            'recent_activity' => $recentActivity->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'trades_last_hour' => $item->trades_last_hour,
                    'profit_last_hour' => round($item->profit_last_hour, 2),
                    'volume_last_hour' => round($item->volume_last_hour, 2),
                    'activity_level' => $this->getActivityLevel($item->trades_last_hour),
                ];
            }),
            'open_positions' => $openPositions->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'open_positions' => $item->open_positions,
                    'total_profit' => round($item->total_profit, 2),
                ];
            }),
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Symbol Performance Trends
     */
    private function getSymbolPerformanceTrends($days = 30)
    {
        $data = Deal::select(
                'symbol',
                DB::raw('DATE(time) as date'),
                DB::raw('SUM(profit) as daily_profit'),
                DB::raw('COUNT(*) as daily_trades')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->whereIn('symbol', function($query) {
                $query->select('symbol')
                    ->from('deals')
                    ->where('time', '>=', now()->subDays(30))
                    ->groupBy('symbol')
                    ->havingRaw('COUNT(*) >= 20')
                    ->limit(6);
            })
            ->groupBy('symbol', 'date')
            ->orderBy('date')
            ->get()
            ->groupBy('symbol');

        return $data->map(function($symbolData, $symbol) {
            $normalizedSymbol = \App\Models\SymbolMapping::normalize($symbol);
            $trend = $this->calculateTrend($symbolData->pluck('daily_profit'));
            
            return [
                'symbol' => $normalizedSymbol,
                'trend_direction' => $trend['direction'],
                'trend_strength' => $trend['strength'],
                'total_profit' => round($symbolData->sum('daily_profit'), 2),
                'avg_daily_profit' => round($symbolData->avg('daily_profit'), 2),
                'total_trades' => $symbolData->sum('daily_trades'),
                'profitable_days' => $symbolData->where('daily_profit', '>', 0)->count(),
                'losing_days' => $symbolData->where('daily_profit', '<', 0)->count(),
                'daily_data' => $symbolData->map(function($item) {
                    return [
                        'date' => $item->date,
                        'profit' => round($item->daily_profit, 2),
                        'trades' => $item->daily_trades,
                    ];
                })->values(),
            ];
        })->values();
    }

    /**
     * Account Size Analysis
     */
    private function getAccountSizeAnalysis($days = 30)
    {
        $data = TradingAccount::select(
                'account_currency',
                DB::raw('COUNT(*) as account_count'),
                DB::raw('AVG(balance) as avg_balance'),
                DB::raw('MIN(balance) as min_balance'),
                DB::raw('MAX(balance) as max_balance'),
                DB::raw('AVG(equity) as avg_equity'),
                DB::raw('SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active_accounts')
            )
            ->where('balance', '>', 0)
            ->groupBy('account_currency')
            ->orderBy('account_count', 'desc')
            ->limit(20)
            ->get();

        return $data->map(function($item) {
            return [
                'currency' => $item->account_currency,
                'account_count' => $item->account_count,
                'avg_balance' => round($item->avg_balance, 2),
                'min_balance' => round($item->min_balance, 2),
                'max_balance' => round($item->max_balance, 2),
                'avg_equity' => round($item->avg_equity, 2),
                'active_accounts' => $item->active_accounts,
                'activity_rate' => $item->account_count > 0 ? round(($item->active_accounts / $item->account_count) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Profit/Loss Distribution
     */
    private function getProfitLossDistribution($days = 30)
    {
        $data = Deal::select('profit')
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->pluck('profit');

        if ($data->isEmpty()) {
            return [
                'total_trades' => 0,
                'profitable_trades' => 0,
                'losing_trades' => 0,
                'breakeven_trades' => 0,
                'win_rate' => 0,
                'avg_profit' => 0,
                'avg_loss' => 0,
                'profit_distribution' => [],
                'loss_distribution' => [],
            ];
        }

        $profitableTrades = $data->filter(fn($p) => $p > 0);
        $losingTrades = $data->filter(fn($p) => $p < 0);
        $breakevenTrades = $data->filter(fn($p) => $p == 0);

        // Create distribution buckets
        $profitBuckets = $this->createDistributionBuckets($profitableTrades, true);
        $lossBuckets = $this->createDistributionBuckets($losingTrades, false);

        return [
            'total_trades' => $data->count(),
            'profitable_trades' => $profitableTrades->count(),
            'losing_trades' => $losingTrades->count(),
            'breakeven_trades' => $breakevenTrades->count(),
            'win_rate' => round(($profitableTrades->count() / $data->count()) * 100, 1),
            'avg_profit' => round($profitableTrades->avg(), 2),
            'avg_loss' => round($losingTrades->avg(), 2),
            'largest_win' => round($profitableTrades->max(), 2),
            'largest_loss' => round($losingTrades->min(), 2),
            'profit_distribution' => $profitBuckets,
            'loss_distribution' => $lossBuckets,
        ];
    }

    /**
     * Trading Patterns Analysis
     */
    private function getTradingPatterns($days = 30)
    {
        // Day of week analysis
        $dayOfWeekData = Deal::select(
                DB::raw('EXTRACT(DOW FROM time) as day_of_week'),
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades')
            )
            ->where('time', '>=', now()->subDays($days))
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->limit(7)
            ->get();

        // Position holding time patterns (simplified)
        $positionPatterns = Deal::select(
                'symbol',
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(profit) as avg_profit'),
                DB::raw('AVG(volume) as avg_volume')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->whereIn('type', ['buy', 'sell'])
            ->groupBy('symbol', 'type')
            ->havingRaw('COUNT(*) >= 5')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        return [
            'day_of_week_analysis' => $dayOfWeekData->map(function($item) {
                $winRate = $item->total_trades > 0 ? round(($item->winning_trades / $item->total_trades) * 100, 1) : 0;
                return [
                    'day_of_week' => (int)$item->day_of_week,
                    'day_name' => $this->getDayName((int)$item->day_of_week),
                    'total_trades' => $item->total_trades,
                    'total_profit' => round($item->total_profit, 2),
                    'win_rate' => $winRate,
                    'avg_profit_per_trade' => $item->total_trades > 0 ? round($item->total_profit / $item->total_trades, 2) : 0,
                ];
            }),
            'position_patterns' => $positionPatterns->map(function($item) {
                return [
                    'symbol' => \App\Models\SymbolMapping::normalize($item->symbol),
                    'type' => $item->type,
                    'count' => $item->count,
                    'avg_profit' => round($item->avg_profit, 2),
                    'avg_volume' => round($item->avg_volume, 2),
                    'profitability' => $item->avg_profit > 0 ? 'profitable' : 'loss_making',
                ];
            }),
        ];
    }

    /**
     * Market Volatility Analysis
     */
    private function getMarketVolatilityAnalysis($days = 30)
    {
        $data = Deal::select(
                'symbol',
                DB::raw('DATE(time) as date'),
                DB::raw('AVG(profit) as avg_profit'),
                DB::raw('STDDEV(profit) as daily_volatility'),
                DB::raw('COUNT(*) as trade_count')
            )
            ->where('time', '>=', now()->subDays($days))
            ->where('symbol', '!=', '')
            ->whereIn('symbol', function($query) {
                $query->select('symbol')
                    ->from('deals')
                    ->where('time', '>=', now()->subDays(30))
                    ->groupBy('symbol')
                    ->havingRaw('COUNT(*) >= 20')
                    ->limit(10);
            })
            ->groupBy('symbol', 'date')
            ->havingRaw('COUNT(*) >= 3')
            ->orderBy('date')
            ->get()
            ->groupBy('symbol');

        return $data->map(function($symbolData, $symbol) {
            $volatilities = $symbolData->pluck('daily_volatility')->filter();
            $avgVolatility = $volatilities->avg();
            $maxVolatility = $volatilities->max();
            
            return [
                'symbol' => \App\Models\SymbolMapping::normalize($symbol),
                'avg_daily_volatility' => round($avgVolatility, 2),
                'max_daily_volatility' => round($maxVolatility, 2),
                'volatility_trend' => $this->calculateVolatilityTrend($volatilities),
                'total_trades' => $symbolData->sum('trade_count'),
                'avg_profit' => round($symbolData->avg('avg_profit'), 2),
                'risk_level' => $this->getRiskLevel($avgVolatility),
                'daily_data' => $symbolData->map(function($item) {
                    return [
                        'date' => $item->date,
                        'volatility' => round($item->daily_volatility, 2),
                        'avg_profit' => round($item->avg_profit, 2),
                        'trade_count' => $item->trade_count,
                    ];
                })->values(),
            ];
        })->sortByDesc('avg_daily_volatility')->values();
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function calculatePerformanceScore($winRate, $avgProfit)
    {
        // Simple scoring algorithm combining win rate and average profit
        $score = ($winRate * 0.6) + (min(max($avgProfit * 2, -50), 50) + 50) * 0.4;
        return round(max(0, min(100, $score)), 1);
    }

    private function calculateRiskScore($winRate, $profitFactor, $volatility)
    {
        // Risk score: higher is better (lower risk)
        $score = ($winRate * 0.4) + (min($profitFactor * 10, 50) * 0.3) + ((100 - min($volatility * 2, 100)) * 0.3);
        return round(max(0, min(100, $score)), 1);
    }

    private function calculateCorrelation($data1, $data2)
    {
        if ($data1->count() < 2 || $data2->count() < 2) {
            return 0;
        }

        $n = min($data1->count(), $data2->count());
        $mean1 = $data1->take($n)->avg();
        $mean2 = $data2->take($n)->avg();

        $numerator = 0;
        $sum1 = 0;
        $sum2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $data1->get($i) - $mean1;
            $y = $data2->get($i) - $mean2;
            $numerator += $x * $y;
            $sum1 += $x * $x;
            $sum2 += $y * $y;
        }

        $denominator = sqrt($sum1 * $sum2);
        return $denominator == 0 ? 0 : $numerator / $denominator;
    }

    private function getCorrelationStrength($correlation)
    {
        $abs = abs($correlation);
        if ($abs >= 0.8) return 'Very Strong';
        if ($abs >= 0.6) return 'Strong';
        if ($abs >= 0.4) return 'Moderate';
        if ($abs >= 0.2) return 'Weak';
        return 'Very Weak';
    }

    private function getNewsHourType($hour)
    {
        switch ($hour) {
            case 8: return 'London Open';
            case 13: return 'NY Open';
            case 15: return 'NY Close';
            default: return 'Regular Hours';
        }
    }

    private function getNewsVolatilityAnalysis($days)
    {
        // Placeholder for future economic calendar integration
        return [
            'high_impact_events' => 0,
            'medium_impact_events' => 0,
            'avg_volatility_increase' => 0,
        ];
    }

    private function getActivityLevel($trades)
    {
        if ($trades >= 50) return 'Very High';
        if ($trades >= 20) return 'High';
        if ($trades >= 10) return 'Medium';
        if ($trades >= 5) return 'Low';
        return 'Very Low';
    }

    private function calculateTrend($data)
    {
        if ($data->count() < 2) {
            return ['direction' => 'neutral', 'strength' => 0];
        }

        $first = $data->first();
        $last = $data->last();
        $change = $last - $first;
        $avgValue = $data->avg();

        if ($avgValue == 0) {
            return ['direction' => 'neutral', 'strength' => 0];
        }

        $percentChange = ($change / abs($avgValue)) * 100;

        if ($percentChange > 20) return ['direction' => 'strong_up', 'strength' => min($percentChange, 100)];
        if ($percentChange > 5) return ['direction' => 'up', 'strength' => $percentChange];
        if ($percentChange < -20) return ['direction' => 'strong_down', 'strength' => min(abs($percentChange), 100)];
        if ($percentChange < -5) return ['direction' => 'down', 'strength' => abs($percentChange)];
        
        return ['direction' => 'neutral', 'strength' => 0];
    }

    private function createDistributionBuckets($data, $isProfit)
    {
        if ($data->isEmpty()) {
            return collect([]);
        }

        $min = $data->min();
        $max = $data->max();
        $range = $max - $min;
        $bucketCount = 10;
        $bucketSize = $range / $bucketCount;

        $buckets = [];
        for ($i = 0; $i < $bucketCount; $i++) {
            $start = $min + ($i * $bucketSize);
            $end = $start + $bucketSize;
            
            $count = $data->filter(function($value) use ($start, $end) {
                return $value >= $start && $value < $end;
            })->count();

            $buckets[] = [
                'range' => sprintf('%s%.2f - %s%.2f', $isProfit ? '' : '-', abs($start), $isProfit ? '' : '-', abs($end)),
                'count' => $count,
                'percentage' => round(($count / $data->count()) * 100, 1),
            ];
        }

        return collect($buckets);
    }

    private function getDayName($dayOfWeek)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$dayOfWeek] ?? 'Unknown';
    }

    private function calculateVolatilityTrend($volatilities)
    {
        if ($volatilities->count() < 2) {
            return 'stable';
        }

        $first = $volatilities->first();
        $last = $volatilities->last();
        $change = (($last - $first) / $first) * 100;

        if ($change > 20) return 'increasing';
        if ($change < -20) return 'decreasing';
        return 'stable';
    }

    private function getRiskLevel($volatility)
    {
        if ($volatility >= 100) return 'Very High';
        if ($volatility >= 50) return 'High';
        if ($volatility >= 25) return 'Medium';
        if ($volatility >= 10) return 'Low';
        return 'Very Low';
    }
}
