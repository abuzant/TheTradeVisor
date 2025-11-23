<?php

namespace App\Services\PublicProfile;

use App\Models\PublicProfileAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProfileDataAggregatorService
{
    /**
     * Get all data for public profile (cached for 15 minutes)
     */
    public function getProfileData(PublicProfileAccount $profileAccount): array
    {
        $cacheKey = "public_profile_{$profileAccount->id}";
        
        return Cache::remember($cacheKey, 900, function () use ($profileAccount) {
            $account = $profileAccount->tradingAccount;
            
            // Fixed 30-day period for public view
            $startDate = now()->subDays(30);
            
            return [
                'account' => $account,
                'profile' => $profileAccount,
                'user' => $profileAccount->user,
                'badges' => $this->getBadges($account),
                'stats' => $this->getStats($account, $startDate),
                'equity_curve' => $this->getEquityCurve($account, $startDate),
                'symbol_performance' => $this->getSymbolPerformance($account, $startDate),
                'trading_hours' => $this->getTradingHours($account, $startDate),
                'monthly_calendar' => $this->getMonthlyCalendar($account, $startDate),
                'recent_trades' => $profileAccount->show_recent_trades ? $this->getRecentTrades($account) : [],
                'milestones' => $this->getMilestones($account),
            ];
        });
    }

    /**
     * Get account badges
     */
    private function getBadges($account): array
    {
        $badgeService = app(BadgeCalculationService::class);
        return $badgeService->getBadgesForDisplay($account);
    }

    /**
     * Get account statistics
     */
    private function getStats($account, $startDate): array
    {
        $deals = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->where('time', '>=', $startDate)
            ->get();

        $totalTrades = $deals->count();
        $winningTrades = $deals->where('profit', '>', 0)->count();
        $losingTrades = $deals->where('profit', '<', 0)->count();
        
        $totalProfit = $deals->sum('profit');
        $totalVolume = $deals->sum('volume');
        
        $grossProfit = $deals->where('profit', '>', 0)->sum('profit');
        $grossLoss = abs($deals->where('profit', '<', 0)->sum('profit'));
        
        $profitFactor = $grossLoss > 0 ? $grossProfit / $grossLoss : 0;
        $winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
        
        $avgWin = $winningTrades > 0 ? $grossProfit / $winningTrades : 0;
        $avgLoss = $losingTrades > 0 ? $grossLoss / $losingTrades : 0;
        
        $bestTrade = $deals->max('profit');
        $worstTrade = $deals->min('profit');

        return [
            'total_trades' => $totalTrades,
            'winning_trades' => $winningTrades,
            'losing_trades' => $losingTrades,
            'win_rate' => round($winRate, 2),
            'total_profit' => $totalProfit,
            'total_volume' => $totalVolume,
            'profit_factor' => round($profitFactor, 2),
            'avg_win' => $avgWin,
            'avg_loss' => $avgLoss,
            'best_trade' => $bestTrade,
            'worst_trade' => $worstTrade,
            'currency' => $account->account_currency,
        ];
    }

    /**
     * Get equity curve data
     */
    private function getEquityCurve($account, $startDate): array
    {
        // Get account snapshots for equity curve
        $snapshots = DB::table('account_snapshots')
            ->where('trading_account_id', $account->id)
            ->where('snapshot_time', '>=', $startDate)
            ->orderBy('snapshot_time', 'asc')
            ->get(['snapshot_time', 'equity', 'balance']);

        return $snapshots->map(function ($snapshot) {
            return [
                'date' => $snapshot->snapshot_time,
                'equity' => (float) $snapshot->equity,
                'balance' => (float) $snapshot->balance,
            ];
        })->toArray();
    }

    /**
     * Get symbol performance breakdown
     */
    private function getSymbolPerformance($account, $startDate): array
    {
        return Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->where('time', '>=', $startDate)
            ->select('symbol')
            ->selectRaw('COUNT(*) as trades')
            ->selectRaw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as wins')
            ->selectRaw('SUM(profit) as total_profit')
            ->selectRaw('SUM(volume) as total_volume')
            ->groupBy('symbol')
            ->orderByDesc('trades')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $winRate = $item->trades > 0 ? ($item->wins / $item->trades) * 100 : 0;
                return [
                    'symbol' => $item->symbol,
                    'trades' => $item->trades,
                    'win_rate' => round($winRate, 2),
                    'profit' => $item->total_profit,
                    'volume' => $item->total_volume,
                ];
            })
            ->toArray();
    }

    /**
     * Get trading hours heatmap
     */
    private function getTradingHours($account, $startDate): array
    {
        $deals = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->where('time', '>=', $startDate)
            ->get(['time']);

        $hourCounts = array_fill(0, 24, 0);
        
        foreach ($deals as $deal) {
            $hour = (int) $deal->time->format('H');
            $hourCounts[$hour]++;
        }

        return $hourCounts;
    }

    /**
     * Get monthly calendar data
     */
    private function getMonthlyCalendar($account, $startDate): array
    {
        $deals = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->where('time', '>=', $startDate)
            ->get(['time', 'profit']);

        $calendar = [];
        
        foreach ($deals as $deal) {
            $date = $deal->time->format('Y-m-d');
            if (!isset($calendar[$date])) {
                $calendar[$date] = 0;
            }
            $calendar[$date] += $deal->profit;
        }

        return $calendar;
    }

    /**
     * Get recent trades
     */
    private function getRecentTrades($account): array
    {
        return Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('time', 'desc')
            ->limit(10)
            ->get(['symbol', 'type', 'profit', 'volume', 'time'])
            ->map(function ($deal) {
                return [
                    'symbol' => $deal->symbol,
                    'type' => $deal->type,
                    'profit' => $deal->profit,
                    'volume' => $deal->volume,
                    'time' => $deal->time,
                ];
            })
            ->toArray();
    }

    /**
     * Get trading milestones
     */
    private function getMilestones($account): array
    {
        $firstTrade = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'in')
            ->orderBy('time', 'asc')
            ->first();

        $bestTrade = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->orderByDesc('profit')
            ->first();

        $totalTrades = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->count();

        $tradingDays = Deal::where('trading_account_id', $account->id)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->selectRaw('DATE(time) as trade_date')
            ->distinct()
            ->count();

        return [
            'first_trade_date' => $firstTrade ? $firstTrade->time : null,
            'best_trade' => $bestTrade ? [
                'profit' => $bestTrade->profit,
                'symbol' => $bestTrade->symbol,
                'date' => $bestTrade->time,
            ] : null,
            'total_trades' => $totalTrades,
            'trading_days' => $tradingDays,
        ];
    }

    /**
     * Clear cache for profile
     */
    public function clearCache(PublicProfileAccount $profileAccount): void
    {
        $cacheKey = "public_profile_{$profileAccount->id}";
        Cache::forget($cacheKey);
    }
}
