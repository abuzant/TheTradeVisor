<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnterpriseBroker;
use App\Models\WhitelistedBrokerUsage;
use App\Models\TradingAccount;
use App\Models\Deal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnterpriseController extends Controller
{
    /**
     * Show comprehensive enterprise dashboard with all analytics
     */
    public function dashboard()
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        // Get all trading accounts for this broker
        $tradingAccounts = TradingAccount::where('broker_name', $broker->official_broker_name)
            ->with('user')
            ->get();

        $accountIds = $tradingAccounts->pluck('id')->toArray();
        $userIds = $tradingAccounts->pluck('user_id')->unique()->toArray();

        // Basic stats
        $stats = [
            'total_users' => count($userIds),
            'total_accounts' => $tradingAccounts->count(),
            'active_last_7_days' => $tradingAccounts->where('last_data_received_at', '>=', now()->subDays(7))->count(),
            'total_balance' => $tradingAccounts->sum('balance'),
            'total_equity' => $tradingAccounts->sum('equity'),
            'total_profit' => $tradingAccounts->sum('profit'),
        ];

        // Trading performance (last 30 days)
        $performance = [];
        if (!empty($accountIds)) {
            $deals = Deal::whereIn('trading_account_id', $accountIds)
                ->where('time', '>=', now()->subDays(30))
                ->where('entry', 'out')
                ->whereIn('type', ['buy', 'sell'])
                ->get();

            $performance = [
                'total_trades' => $deals->count(),
                'winning_trades' => $deals->where('profit', '>', 0)->count(),
                'losing_trades' => $deals->where('profit', '<', 0)->count(),
                'total_volume' => $deals->sum('volume'),
                'total_profit' => $deals->sum('profit'),
                'win_rate' => $deals->count() > 0 ? round(($deals->where('profit', '>', 0)->count() / $deals->count()) * 100, 2) : 0,
            ];
        }

        // Top performing accounts
        $topAccounts = $tradingAccounts->sortByDesc('profit')->take(10);

        // Recent activity
        $recentAccounts = $tradingAccounts->sortByDesc('last_data_received_at')->take(20);

        // Symbol performance (last 30 days)
        $symbolStats = [];
        if (!empty($accountIds)) {
            $symbolStats = Deal::whereIn('trading_account_id', $accountIds)
                ->where('time', '>=', now()->subDays(30))
                ->where('entry', 'out')
                ->whereIn('type', ['buy', 'sell'])
                ->select('symbol', 
                    DB::raw('COUNT(*) as trade_count'),
                    DB::raw('SUM(profit) as total_profit'),
                    DB::raw('SUM(volume) as total_volume'),
                    DB::raw('COUNT(CASE WHEN profit > 0 THEN 1 END) as winning_trades')
                )
                ->groupBy('symbol')
                ->orderByDesc('total_profit')
                ->limit(10)
                ->get();
        }

        // Daily profit chart data (last 30 days)
        $dailyProfits = [];
        if (!empty($accountIds)) {
            $dailyProfits = Deal::whereIn('trading_account_id', $accountIds)
                ->where('time', '>=', now()->subDays(30))
                ->where('entry', 'out')
                ->whereIn('type', ['buy', 'sell'])
                ->select(
                    DB::raw('DATE(time) as date'),
                    DB::raw('SUM(profit) as profit'),
                    DB::raw('COUNT(*) as trades')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return view('enterprise.dashboard', compact(
            'broker', 
            'stats', 
            'performance', 
            'topAccounts', 
            'recentAccounts',
            'symbolStats',
            'dailyProfits',
            'tradingAccounts'
        ));
    }

    /**
     * Show enterprise settings
     */
    public function settings()
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        return view('enterprise.settings', compact('broker'));
    }

    /**
     * Update enterprise settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $broker = $user->enterpriseBroker;

        if (!$broker) {
            return redirect()->route('dashboard')->with('error', 'Enterprise account not found');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'official_broker_name' => 'required|string|max:255',
        ]);

        $broker->update([
            'company_name' => $request->company_name,
            'official_broker_name' => $request->official_broker_name,
        ]);

        return back()->with('success', 'Settings updated successfully');
    }

}
