<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\User;
use Carbon\Carbon;

class DigestService
{
    /**
     * Build a digest payload for a user over the given number of days.
     */
    public function buildUserDigest(User $user, int $days): array
    {
        $end = Carbon::now();
        $start = $end->copy()->subDays($days);

        $deals = Deal::whereIn('trading_account_id', $user->tradingAccounts()->pluck('id'))
            ->whereBetween('time', [$start, $end])
            ->whereIn('entry', ['out', 'inout'])
            ->get();

        $totalTrades = $deals->count();
        $winningTrades = $deals->where('profit', '>', 0)->count();
        $losingTrades = $deals->where('profit', '<', 0)->count();
        $totalProfit = $deals->sum('profit');
        $avgProfit = $totalTrades > 0 ? $deals->avg('profit') : 0;
        $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

        $bySymbol = $deals->groupBy('symbol')->map(function ($group) {
            $first = $group->first();
            return [
                'symbol' => $first?->symbol ?? 'Unknown',
                'trades' => $group->count(),
                'profit' => $group->sum('profit'),
            ];
        })->values();

        $topSymbol = $bySymbol->sortByDesc('profit')->first();
        $worstSymbol = $bySymbol->sortBy('profit')->first();

        return [
            'period_start' => $start,
            'period_end' => $end,
            'days' => $days,
            'total_trades' => $totalTrades,
            'winning_trades' => $winningTrades,
            'losing_trades' => $losingTrades,
            'total_profit' => round($totalProfit, 2),
            'avg_profit' => round($avgProfit, 2),
            'win_rate' => $winRate,
            'top_symbol' => $topSymbol,
            'worst_symbol' => $worstSymbol,
            'top_pairs' => $bySymbol->sortByDesc('profit')->take(5)->values(),
            'bottom_pairs' => $bySymbol->sortBy('profit')->take(5)->values(),
            'volume_change_percent' => 0, // placeholder
            'best_time' => null, // placeholder
            'worst_time' => null, // placeholder
            'riskiest_symbols' => [], // placeholder
            'long_positions' => [], // placeholder
        ];
    }
}
