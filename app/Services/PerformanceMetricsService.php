<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerformanceMetricsService
{
    protected $currencyService;

    public function __construct(\App\Services\CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get comprehensive performance metrics for a user or account
     */
    public function getPerformanceMetrics($accountIds, int $days = 30, $displayCurrency = 'USD')
    {
        if (!is_array($accountIds)) {
            $accountIds = [$accountIds];
        }

        $cacheKey = $this->buildCacheKey('performance.metrics', [
            'account_ids' => array_map('intval', $accountIds),
            'days' => $days,
            'currency' => $displayCurrency,
        ]);

        $ttl = (int) config('analytics.cache.performance_ttl', 300);

        return Cache::remember($cacheKey, $ttl, function() use ($accountIds, $days, $displayCurrency) {
            Log::info('Performance metrics cache rebuild', [
                'account_ids' => $accountIds,
                'days' => $days,
                'currency' => $displayCurrency,
            ]);

            return [
                'trade_analysis' => $this->getTradeAnalysis($accountIds, $days, $displayCurrency),
                'symbol_performance' => $this->getSymbolPerformance($accountIds, $days, $displayCurrency),
                'timing_analysis' => $this->getTimingAnalysis($accountIds, $days, $displayCurrency),
                'risk_metrics' => $this->getRiskMetrics($accountIds, $days, $displayCurrency),
                'streaks' => $this->getStreakAnalysis($accountIds, $days),
                'equity_curve' => $this->getEquityCurve($accountIds, $days, $displayCurrency),
                'drawdown' => $this->getDrawdownAnalysis($accountIds, $days, $displayCurrency),
                'country_sentiment' => $this->getCountryBasedMarketSentiment($accountIds, $days, $displayCurrency),
                'platform_performance' => $this->getPlatformPerformanceMatrix($accountIds, $days, $displayCurrency),
                'display_currency' => $displayCurrency,
            ];
        });
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
     * Trade Analysis - Most successful trades, ROI, etc.
     * 
     * Uses Deal model with entry='out' for standardized approach across platforms:
     * - MT5 (Position-based): OUT deal = position closed with final profit
     * - MT4 (Order-based): OUT deal = order closed with final profit
     * - Works for both Netting and Hedging modes
     * 
     * Why Deals not Positions:
     * - Positions table only stores current/recent state (43 records)
     * - Deals table has complete transaction history (313+ records)
     * - Each closed position/order creates an OUT deal with final P/L
     */
    private function getTradeAnalysis($accountIds, $days, $displayCurrency)
    {
        // Get OUT deals (closed positions/orders with final profit)
        // This works for MT4, MT5 Netting, and MT5 Hedging
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->where('entry', 'out')
            ->where('time', '>=', now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        // Convert all profits to display currency
        $convertedDeals = $deals->map(function($deal) use ($displayCurrency) {
            $converted = $this->currencyService->safeConvert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            // Skip deals we cannot convert
            $deal->converted_profit = $converted ?? 0.0;

            return $deal;
        });

        // Most profitable trade
        $mostProfitable = $convertedDeals->sortByDesc('converted_profit')->first();
        
        // Worst trade
        $worstTrade = $convertedDeals->sortBy('converted_profit')->first();
        
        // Calculate average hold time
        $avgHoldTime = $this->calculateAverageHoldTime($accountIds, $days);

        // Total stats
        $totalProfit = $convertedDeals->sum('converted_profit');
        $totalVolume = $convertedDeals->sum('volume');
        $winningTrades = $convertedDeals->where('converted_profit', '>', 0);
        $losingTrades = $convertedDeals->where('converted_profit', '<', 0);

        return [
            'most_profitable_trade' => [
                'symbol' => $mostProfitable->normalized_symbol ?? $mostProfitable->symbol,
                'profit' => $mostProfitable->converted_profit,
                'volume' => $mostProfitable->volume,
                'date' => $mostProfitable->time->format('M d, Y'),
            ],
            'worst_trade' => [
                'symbol' => $worstTrade->normalized_symbol ?? $worstTrade->symbol,
                'profit' => $worstTrade->converted_profit,
                'volume' => $worstTrade->volume,
                'date' => $worstTrade->time->format('M d, Y'),
            ],
            'total_trades' => $convertedDeals->count(),
            'winning_trades' => $winningTrades->count(),
            'losing_trades' => $losingTrades->count(),
            'win_rate' => $convertedDeals->count() > 0 ? round(($winningTrades->count() / $convertedDeals->count()) * 100, 1) : 0,
            'avg_win' => $winningTrades->count() > 0 ? $winningTrades->avg('converted_profit') : 0,
            'avg_loss' => $losingTrades->count() > 0 ? abs($losingTrades->avg('converted_profit')) : 0,
            'profit_factor' => $this->calculateProfitFactor($convertedDeals, 'converted_profit'),
            'avg_hold_time' => $avgHoldTime,
        ];
    }

    /**
     * Symbol Performance - Win rate, profit per symbol
     */
    private function getSymbolPerformance($accountIds, $days, $displayCurrency)
    {
        // Use closed trades (OUT deals) to represent completed positions/orders per symbol
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->closedTrades()
            ->where('time', '>=', now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return collect([]);
        }

        // Convert and group by normalized symbol
        $symbolGroups = $deals->groupBy(function($deal) {
            return $deal->normalized_symbol ?? $deal->symbol;
        });

        $symbols = $symbolGroups->map(function($symbolDeals, $symbol) use ($displayCurrency) {
            // Convert all profits to display currency
            $convertedDeals = $symbolDeals->map(function($deal) use ($displayCurrency) {
                $converted = $this->currencyService->safeConvert(
                    $deal->profit,
                    $deal->tradingAccount->account_currency ?? 'USD',
                    $displayCurrency
                );

                $deal->converted_profit = $converted ?? 0.0;

                return $deal;
            });

            // Group by position_id so each position/order counts as ONE trade
            $positions = $convertedDeals->groupBy('position_id');

            $totalTrades = $positions->count();

            // Determine win/loss per position based on total converted profit
            $winningTrades = 0;
            $losingTrades = 0;
            $positionProfits = [];

            foreach ($positions as $positionId => $positionDeals) {
                $posProfit = $positionDeals->sum('converted_profit');
                $positionProfits[] = $posProfit;
                if ($posProfit > 0) {
                    $winningTrades++;
                } elseif ($posProfit < 0) {
                    $losingTrades++;
                }
            }

            $totalProfit = array_sum($positionProfits);
            $avgProfit = $totalTrades > 0 ? $totalProfit / $totalTrades : 0;

            // Volume: sum volume of OUT deals; this approximates traded size without double-counting IN+OUT
            $totalVolume = $convertedDeals->sum('volume');

            $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

            return [
                'symbol' => $symbol,
                'total_trades' => $totalTrades,
                'total_profit' => round($totalProfit, 2),
                'avg_profit' => round($avgProfit, 2),
                'win_rate' => $winRate,
                'total_volume' => round($totalVolume, 2),
                'performance' => $totalProfit > 0 ? 'Profitable' : 'Unprofitable',
            ];
        })
        ->filter(function($symbol) {
            return $symbol['total_trades'] >= 3; // Only show symbols with at least 3 trades
        })
        ->sortByDesc('total_profit')
        ->take(10)
        ->values();

        return $symbols;
    }

    /**
     * Timing Analysis - Best hours, days, etc.
     */
    private function getTimingAnalysis($accountIds, $days, $displayCurrency)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        // Convert profits to display currency
        $convertedDeals = $deals->map(function($deal) use ($displayCurrency) {
            $converted = $this->currencyService->safeConvert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            $deal->converted_profit = $converted ?? 0.0;

            return $deal;
        });

        // Group by hour
        $hourlyPerformance = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyPerformance[$h] = [
                'hour' => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
                'trades' => 0,
                'profit' => 0,
                'wins' => 0,
            ];
        }

        foreach ($convertedDeals as $deal) {
            $hour = (int) $deal->time->format('H');
            $hourlyPerformance[$hour]['trades']++;
            $hourlyPerformance[$hour]['profit'] += $deal->converted_profit;
            if ($deal->converted_profit > 0) {
                $hourlyPerformance[$hour]['wins']++;
            }
        }

        // Calculate win rates
        foreach ($hourlyPerformance as $hour => &$data) {
            $data['win_rate'] = $data['trades'] > 0 
                ? round(($data['wins'] / $data['trades']) * 100, 1) 
                : 0;
            $data['profit'] = round($data['profit'], 2);
        }

        // Find best and worst hours
        $bestHour = collect($hourlyPerformance)->sortByDesc('profit')->first();
        $worstHour = collect($hourlyPerformance)->sortBy('profit')->first();

        // Group by day of week
        $dailyPerformance = [];
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        foreach ($daysOfWeek as $day) {
            $dailyPerformance[$day] = [
                'day' => $day,
                'trades' => 0,
                'profit' => 0,
                'wins' => 0,
            ];
        }

        foreach ($convertedDeals as $deal) {
            $day = $deal->time->format('l');
            $dailyPerformance[$day]['trades']++;
            $dailyPerformance[$day]['profit'] += $deal->converted_profit;
            if ($deal->converted_profit > 0) {
                $dailyPerformance[$day]['wins']++;
            }
        }

        // Calculate win rates for days
        foreach ($dailyPerformance as $day => &$data) {
            $data['win_rate'] = $data['trades'] > 0 
                ? round(($data['wins'] / $data['trades']) * 100, 1) 
                : 0;
            $data['profit'] = round($data['profit'], 2);
        }

        $bestDay = collect($dailyPerformance)->sortByDesc('profit')->first();
        $worstDay = collect($dailyPerformance)->sortBy('profit')->first();

        return [
            'hourly_performance' => array_values($hourlyPerformance),
            'daily_performance' => array_values($dailyPerformance),
            'best_hour' => $bestHour,
            'worst_hour' => $worstHour,
            'best_day' => $bestDay,
            'worst_day' => $worstDay,
        ];
    }

    /**
     * Risk Metrics - Risk/reward ratio, SL vs TP
     */
    private function getRiskMetrics($accountIds, $days, $displayCurrency)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        // Convert profits
        $convertedDeals = $deals->map(function($deal) use ($displayCurrency) {
            $converted = $this->currencyService->safeConvert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            $deal->converted_profit = $converted ?? 0.0;

            return $deal;
        });

        $winningTrades = $convertedDeals->where('converted_profit', '>', 0);
        $losingTrades = $convertedDeals->where('converted_profit', '<', 0);

        $totalWins = $winningTrades->sum('converted_profit');
        $totalLosses = abs($losingTrades->sum('converted_profit'));

        $avgWin = $winningTrades->count() > 0 ? $winningTrades->avg('converted_profit') : 0;
        $avgLoss = $losingTrades->count() > 0 ? abs($losingTrades->avg('converted_profit')) : 0;

        $riskRewardRatio = $avgLoss > 0 ? round($avgWin / $avgLoss, 2) : 0;

        return [
            'risk_reward_ratio' => $riskRewardRatio,
            'profit_factor' => $totalLosses > 0 ? round($totalWins / $totalLosses, 2) : ($totalWins > 0 ? 999 : 0),
            'avg_win' => round($avgWin, 2),
            'avg_loss' => round($avgLoss, 2),
            'largest_win' => round($winningTrades->max('converted_profit') ?? 0, 2),
            'largest_loss' => round(abs($losingTrades->min('converted_profit') ?? 0), 2),
            'total_wins' => round($totalWins, 2),
            'total_losses' => round($totalLosses, 2),
        ];
    }

    /**
     * Streak Analysis - Consecutive wins/losses
     */
    private function getStreakAnalysis($accountIds, $days)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->orderBy('time', 'asc')
            ->get();

        if ($deals->isEmpty()) {
            return null;
        }

        $maxWinStreak = 0;
        $maxLossStreak = 0;
        $currentWinStreak = 0;
        $currentLossStreak = 0;
        $currentStreak = 0;

        foreach ($deals as $deal) {
            if ($deal->profit > 0) {
                $currentWinStreak++;
                $currentLossStreak = 0;
                $maxWinStreak = max($maxWinStreak, $currentWinStreak);
                $currentStreak = $currentWinStreak;
            } elseif ($deal->profit < 0) {
                $currentLossStreak++;
                $currentWinStreak = 0;
                $maxLossStreak = max($maxLossStreak, $currentLossStreak);
                $currentStreak = -$currentLossStreak;
            }
        }

        return [
            'max_win_streak' => $maxWinStreak,
            'max_loss_streak' => $maxLossStreak,
            'current_streak' => $currentStreak,
            'current_streak_type' => $currentStreak > 0 ? 'winning' : ($currentStreak < 0 ? 'losing' : 'none'),
        ];
    }

    /**
     * Equity Curve - Balance over time
     */
    private function getEquityCurve($accountIds, $days, $displayCurrency)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->orderBy('time', 'asc')
            ->get();

        if ($deals->isEmpty()) {
            return [];
        }

        $equityCurve = [];
        $runningBalance = 0;

        // Get starting balance
        $accounts = TradingAccount::whereIn('id', $accountIds)->get();
        
        // Convert current balances and calculate starting balance
        $currentBalance = $accounts->sum(function($account) use ($displayCurrency) {
            $converted = $this->currencyService->safeConvert(
                $account->balance,
                $account->account_currency,
                $displayCurrency
            );

            return $converted ?? 0.0;
        });

        $totalProfit = $deals->sum(function($deal) use ($displayCurrency) {
            $converted = $this->currencyService->safeConvert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            );

            return $converted ?? 0.0;
        });

        $startingBalance = $currentBalance - $totalProfit;
        $runningBalance = $startingBalance;

        foreach ($deals as $deal) {
            $convertedProfit = $this->currencyService->safeConvert(
                $deal->profit,
                $deal->tradingAccount->account_currency ?? 'USD',
                $displayCurrency
            ) ?? 0.0;
            
            $runningBalance += $convertedProfit;
            
            $equityCurve[] = [
                'date' => $deal->time->format('Y-m-d H:i:s'),
                'balance' => round($runningBalance, 2),
                'profit' => round($convertedProfit, 2),
            ];
        }

        return $equityCurve;
    }

    /**
     * Drawdown Analysis
     */
    private function getDrawdownAnalysis($accountIds, $days, $displayCurrency)
    {
        $equityCurve = $this->getEquityCurve($accountIds, $days, $displayCurrency);

        if (empty($equityCurve)) {
            return null;
        }

        $maxBalance = 0;
        $maxDrawdown = 0;
        $currentDrawdown = 0;
        $drawdownPeriods = [];

        foreach ($equityCurve as $point) {
            $balance = $point['balance'];
            
            if ($balance > $maxBalance) {
                $maxBalance = $balance;
            }

            $currentDrawdown = $maxBalance > 0 
                ? (($maxBalance - $balance) / $maxBalance) * 100 
                : 0;

            $maxDrawdown = max($maxDrawdown, $currentDrawdown);

            $drawdownPeriods[] = [
                'date' => $point['date'],
                'drawdown' => round($currentDrawdown, 2),
            ];
        }

        return [
            'max_drawdown' => round($maxDrawdown, 2),
            'current_drawdown' => round($currentDrawdown, 2),
            'drawdown_periods' => $drawdownPeriods,
        ];
    }

    /**
     * Helper: Calculate average hold time
     * Uses deals to find actual open and close times
     */
    private function calculateAverageHoldTime($accountIds, $days)
    {
        // Get deals with IN and OUT entries within the time period
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->whereIn('entry', ['in', 'out'])
            ->orderBy('time', 'asc')
            ->get(['position_id', 'entry', 'time']);

        if ($deals->isEmpty()) {
            return 'N/A';
        }

        // Group by position_id to find matching IN/OUT pairs
        $grouped = $deals->groupBy('position_id');
        $holdTimes = [];

        foreach ($grouped as $positionDeals) {
            $inDeal = $positionDeals->where('entry', 'in')->first();
            $outDeal = $positionDeals->where('entry', 'out')->first();
            
            // Only calculate if we have both IN and OUT (closed position)
            if ($inDeal && $outDeal) {
                $openTime = \Carbon\Carbon::parse($inDeal->time);
                $closeTime = \Carbon\Carbon::parse($outDeal->time);
                $holdTimes[] = $openTime->diffInHours($closeTime);
            }
        }

        if (empty($holdTimes)) {
            return 'N/A';
        }

        $avgHoldTimeHours = array_sum($holdTimes) / count($holdTimes);

        // Format as human readable
        if ($avgHoldTimeHours < 1) {
            return round($avgHoldTimeHours * 60) . ' min';
        } elseif ($avgHoldTimeHours < 24) {
            return round($avgHoldTimeHours, 1) . ' hrs';
        } else {
            $days = floor($avgHoldTimeHours / 24);
            $hours = round($avgHoldTimeHours % 24);
            return $days . 'd ' . $hours . 'h';
        }
    }

    /**
     * Helper: Calculate profit factor
     */
    private function calculateProfitFactor($deals, $profitField = 'profit')
    {
        $grossProfit = $deals->where($profitField, '>', 0)->sum($profitField);
        $grossLoss = abs($deals->where($profitField, '<', 0)->sum($profitField));

        return $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : ($grossProfit > 0 ? 999 : 0);
    }

    /**
     * Country-Based Market Sentiment Analysis
     */
    private function getCountryBasedMarketSentiment($accountIds, $days, $displayCurrency)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->whereHas('tradingAccount', function($query) {
                $query->whereNotNull('detected_country');
            })
            ->get();

        if ($deals->isEmpty()) {
            return collect([]);
        }

        // Group by country and calculate sentiment
        $countryGroups = $deals->groupBy(function($deal) {
            return $deal->tradingAccount->detected_country ?? 'Unknown';
        });

        $countrySentiment = $countryGroups->map(function($countryDeals, $country) use ($displayCurrency) {
            // Convert profits to display currency, skip unconvertible deals
            $convertedDeals = $countryDeals->map(function($deal) use ($displayCurrency) {
                $converted = $this->currencyService->safeConvert(
                    $deal->profit,
                    $deal->tradingAccount->account_currency ?? 'USD',
                    $displayCurrency
                );

                if ($converted === null) {
                    return null;
                }

                $deal->converted_profit = $converted;
                return $deal;
            })->filter()->values();

            if ($convertedDeals->isEmpty()) {
                return null;
            }

            $buyTrades = $convertedDeals->where('type', 'buy');
            $sellTrades = $convertedDeals->where('type', 'sell');
            $winningTrades = $convertedDeals->where('converted_profit', '>', 0);
            $losingTrades = $convertedDeals->where('converted_profit', '<', 0);

            $totalTrades = $convertedDeals->count();
            $totalProfit = $convertedDeals->sum('converted_profit');
            $totalVolume = $convertedDeals->sum('volume');

            // Calculate sentiment percentages
            $buyPercentage = $totalTrades > 0 ? round(($buyTrades->count() / $totalTrades) * 100, 1) : 0;
            $sellPercentage = $totalTrades > 0 ? round(($sellTrades->count() / $totalTrades) * 100, 1) : 0;
            $winRate = $totalTrades > 0 ? round(($winningTrades->count() / $totalTrades) * 100, 1) : 0;

            // Determine sentiment (bullish/bearish/neutral)
            $sentiment = 'neutral';
            $sentimentScore = 0;
            
            if ($buyPercentage > $sellPercentage + 10) {
                $sentiment = 'bullish';
                $sentimentScore = min(($buyPercentage - $sellPercentage) / 2, 50);
            } elseif ($sellPercentage > $buyPercentage + 10) {
                $sentiment = 'bearish';
                $sentimentScore = min(($sellPercentage - $buyPercentage) / 2, 50);
            }

            return [
                'country' => $country,
                'country_code' => strtoupper($country),
                'total_trades' => $totalTrades,
                'buy_trades' => $buyTrades->count(),
                'sell_trades' => $sellTrades->count(),
                'buy_percentage' => $buyPercentage,
                'sell_percentage' => $sellPercentage,
                'winning_trades' => $winningTrades->count(),
                'losing_trades' => $losingTrades->count(),
                'win_rate' => $winRate,
                'total_profit' => round($totalProfit, 2),
                'total_volume' => round($totalVolume, 2),
                'avg_profit' => round($convertedDeals->avg('converted_profit'), 2),
                'sentiment' => $sentiment,
                'sentiment_score' => round($sentimentScore, 1),
                'profit_per_trade' => $totalTrades > 0 ? round($totalProfit / $totalTrades, 2) : 0,
            ];
        })
        ->filter()
        ->filter(function($country) {
            return $country['total_trades'] >= 5; // Only show countries with at least 5 trades
        })
        ->sortByDesc('total_trades')
        ->values();

        return $countrySentiment;
    }

    /**
     * Platform Performance Matrix (MT4 vs MT5, Hedging vs Netting)
     */
    private function getPlatformPerformanceMatrix($accountIds, $days, $displayCurrency)
    {
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->with('tradingAccount')
            ->tradesOnly()
            ->where('time', '>=', now()->subDays($days))
            ->whereHas('tradingAccount', function($query) {
                $query->whereNotNull('platform_type');
            })
            ->get();

        if ($deals->isEmpty()) {
            return collect([]);
        }

        // Group by platform type and account mode
        $platformGroups = $deals->groupBy(function($deal) {
            $platform = $deal->tradingAccount->platform_type ?? 'Unknown';
            $mode = $deal->tradingAccount->account_mode ?? 'hedging'; // MT4 is always hedging
            return "{$platform}_{$mode}";
        });

        $platformPerformance = $platformGroups->map(function($platformDeals, $platformKey) use ($displayCurrency) {
            // Parse platform and mode from key
            list($platform, $mode) = explode('_', $platformKey);
            
            // Convert profits to display currency, skip unconvertible deals
            $convertedDeals = $platformDeals->map(function($deal) use ($displayCurrency) {
                $converted = $this->currencyService->safeConvert(
                    $deal->profit,
                    $deal->tradingAccount->account_currency ?? 'USD',
                    $displayCurrency
                );

                if ($converted === null) {
                    return null;
                }

                $deal->converted_profit = $converted;
                return $deal;
            })->filter()->values();

            if ($convertedDeals->isEmpty()) {
                return null;
            }

            $buyTrades = $convertedDeals->where('type', 'buy');
            $sellTrades = $convertedDeals->where('type', 'sell');
            $winningTrades = $convertedDeals->where('converted_profit', '>', 0);
            $losingTrades = $convertedDeals->where('converted_profit', '<', 0);

            $totalTrades = $convertedDeals->count();
            $totalProfit = $convertedDeals->sum('converted_profit');
            $totalVolume = $convertedDeals->sum('volume');

            // Calculate metrics
            $winRate = $totalTrades > 0 ? round(($winningTrades->count() / $totalTrades) * 100, 1) : 0;
            $avgProfit = $convertedDeals->avg('converted_profit');
            $avgWin = $winningTrades->count() > 0 ? $winningTrades->avg('converted_profit') : 0;
            $avgLoss = $losingTrades->count() > 0 ? abs($losingTrades->avg('converted_profit')) : 0;
            $riskRewardRatio = $avgLoss > 0 ? round($avgWin / $avgLoss, 2) : 0;
            $profitFactor = $this->calculateProfitFactor($convertedDeals, 'converted_profit');

            // Get unique accounts using this platform/mode
            $uniqueAccounts = $platformDeals->pluck('trading_account_id')->unique()->count();

            return [
                'platform_type' => strtoupper($platform),
                'account_mode' => ucfirst($mode),
                'platform_key' => $platformKey,
                'unique_accounts' => $uniqueAccounts,
                'total_trades' => $totalTrades,
                'buy_trades' => $buyTrades->count(),
                'sell_trades' => $sellTrades->count(),
                'winning_trades' => $winningTrades->count(),
                'losing_trades' => $losingTrades->count(),
                'win_rate' => $winRate,
                'total_profit' => round($totalProfit, 2),
                'total_volume' => round($totalVolume, 2),
                'avg_profit' => round($avgProfit, 2),
                'avg_win' => round($avgWin, 2),
                'avg_loss' => round($avgLoss, 2),
                'risk_reward_ratio' => $riskRewardRatio,
                'profit_factor' => $profitFactor,
                'profit_per_trade' => $totalTrades > 0 ? round($totalProfit / $totalTrades, 2) : 0,
                'volume_per_trade' => $totalTrades > 0 ? round($totalVolume / $totalTrades, 2) : 0,
            ];
        })
        ->filter()
        ->filter(function($platform) {
            return $platform['total_trades'] >= 1; // Show all platforms with at least 1 trade
        })
        ->sortByDesc('total_profit')
        ->values();

        return $platformPerformance;
    }
}
