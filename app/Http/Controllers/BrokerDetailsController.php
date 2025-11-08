<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class BrokerDetailsController extends Controller
{
    /**
     * Show broker details page
     */
    public function show(Request $request, $broker)
    {
        $user = $request->user();
        $broker = urldecode($broker);
        $days = $request->get('days', 30);

        // Get user's accounts with this broker
        $userAccounts = $user->tradingAccounts()
            ->where('broker_name', $broker)
            ->get();

        if ($userAccounts->isEmpty()) {
            abort(404, 'Broker not found in your accounts');
        }

        $accountIds = $userAccounts->pluck('id');

        // Broker statistics (no aggregation - will show per account)
        $stats = [
            'total_accounts' => $userAccounts->count(),
            'active_accounts' => $userAccounts->where('is_active', true)->count(),
            'open_positions' => Position::whereIn('trading_account_id', $accountIds)
                ->where('is_open', true)
                ->count(),
        ];

        // Trading activity (last N days)
        $tradingActivity = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->select(
                DB::raw('COUNT(*) as total_trades'),
                DB::raw('SUM(profit) as total_profit'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('SUM(commission) as total_commission'),
                DB::raw('SUM(swap) as total_swap'),
                DB::raw('SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades')
            )
            ->first();

        $stats['total_trades'] = $tradingActivity->total_trades ?? 0;
        $stats['trading_profit'] = $tradingActivity->total_profit ?? 0;
        $stats['total_volume'] = $tradingActivity->total_volume ?? 0;
        $stats['total_commission'] = $tradingActivity->total_commission ?? 0;
        $stats['total_swap'] = $tradingActivity->total_swap ?? 0;
        $stats['win_rate'] = $stats['total_trades'] > 0
            ? round(($tradingActivity->winning_trades / $stats['total_trades']) * 100, 1)
            : 0;

        // Most traded symbols
        $topSymbols = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->whereNotNull('symbol')
            ->where('symbol', '!=', '')
            ->select('symbol',
                DB::raw('COUNT(*) as trades'),
                DB::raw('SUM(profit) as profit'),
                DB::raw('SUM(volume) as volume'))
            ->groupBy('symbol')
            ->havingRaw('COUNT(*) >= ?', [3])
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->normalized_symbol = \App\Models\SymbolMapping::normalize($item->symbol);
                return $item;
            });

        // Daily profit trend
        $dailyProfitTrend = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->select(DB::raw('DATE(time) as date'), DB::raw('SUM(profit) as profit'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Account servers
        $servers = $userAccounts->pluck('broker_server')->unique()->filter()->values();

        return view('broker-details.show', compact(
            'broker',
            'stats',
            'topSymbols',
            'dailyProfitTrend',
            'userAccounts',
            'servers',
            'days'
        ));
    }
}
