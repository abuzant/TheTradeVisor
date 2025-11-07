<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use App\Traits\Sortable;


class DashboardController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $user = $request->user();
        $displayCurrency = $user->display_currency;

        // Define sortable columns for user's accounts
        $sortableColumns = [
            'broker_name',
            'account_number',
            'balance',
            'equity',
            'profit',
            'last_sync_at',
            'created_at'
        ];

        // Build accounts query
        $accountsQuery = $user->tradingAccounts()
            ->with(['openPositions', 'activeOrders']);

        // Apply sorting if requested
        if ($request->has('sort_by')) {
            $accountsQuery = $this->applySorting($accountsQuery, $request, $sortableColumns, 'last_sync_at', 'desc');
        } else {
            // Default sorting
            $accountsQuery->orderBy('last_sync_at', 'desc');
        }

        // Get accounts
        $accounts = $accountsQuery->get();

        // Calculate totals across all accounts
        $totals = [
            'accounts' => $accounts->count(),
            'total_balance' => $accounts->sum(function($account) use ($displayCurrency) {
                return $account->getBalanceInCurrency($displayCurrency);
            }),
            'total_equity' => $accounts->sum(function($account) use ($displayCurrency) {
                return $account->getEquityInCurrency($displayCurrency);
            }),
            'total_profit' => $accounts->sum(function($account) use ($displayCurrency) {
                return $account->getProfitInCurrency($displayCurrency);
            }),
            'display_currency' => $displayCurrency,
            'open_positions' => $accounts->sum(function($acc) {
                return $acc->openPositions->count();
            }),
            'pending_orders' => $accounts->sum(function($acc) {
                return $acc->activeOrders->count();
            }),
        ];

        // Get recent activity with pagination
        $recentDeals = Deal::whereIn('trading_account_id', $accounts->pluck('id'))
            ->tradesOnly()  // This now handles NULL deal_category
            ->with('tradingAccount')
            ->orderBy('time', 'desc')
            ->paginate(20);


        // Get account limit info
        $accountLimit = $user->getAccountLimitInfo();

        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'last_sync_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        //    ChartsData
        $accountsChartData = $this->prepareAccountsChartData($accounts, $displayCurrency);

        return view('dashboard', compact(
            'user',
            'accounts',
            'totals',
            'recentDeals',
            'accountLimit',
            'sortBy',
            'sortDirection',
        'accountsChartData'
        ));
    }

    public function account(Request $request, $accountId)
    {
        $user = $request->user();

        // Get specific account (ensure it belongs to user)
        $account = TradingAccount::where('id', $accountId)
            ->where('user_id', $user->id)
            ->with(['openPositions', 'activeOrders'])
            ->firstOrFail();

        // Define sortable columns for deals
        $sortableColumns = ['time', 'symbol', 'type', 'volume', 'price', 'profit'];

        // Get deals for this account (last 30 days)
        $dealsQuery = $account->deals()
            ->tradesOnly()  // Use the scope - handles NULL deal_category
            ->where('time', '>=', now()->subDays(30));

        // Apply sorting
        if ($request->has('sort_by')) {
            $dealsQuery = $this->applySorting($dealsQuery, $request, $sortableColumns, 'time', 'desc');
        } else {
            $dealsQuery->orderBy('time', 'desc');
        }

        $deals = $dealsQuery->paginate(50)->withQueryString();

        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'time');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Calculate statistics
        $stats = $this->calculateAccountStats($account);

        // Prepare chart data
        $chartData = $this->prepareChartData($account);

        return view('account.show', compact('account', 'deals', 'stats', 'chartData', 'sortBy', 'sortDirection'));
    }

    private function prepareChartData($account)
    {
        // Equity Curve (last 30 days)
        $equityData = Deal::where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->where('entry', 'out')
            ->orderBy('time')
            ->get()
            ->groupBy(function($deal) {
                return \Carbon\Carbon::parse($deal->time)->format('Y-m-d');
            })
            ->map(function($deals) {
                return $deals->sum('profit');
            });

        $runningBalance = $account->balance - $equityData->sum();
        $equityLabels = [];
        $equityValues = [];

        foreach($equityData as $date => $dailyProfit) {
            $runningBalance += $dailyProfit;
            $equityLabels[] = \Carbon\Carbon::parse($date)->format('M d');
            $equityValues[] = round($runningBalance, 2);
        }

        // Symbol Distribution
        $symbolData = Deal::select('symbol', DB::raw('count(*) as count'))
            ->where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->groupBy('symbol')
            ->orderBy('count', 'desc')
            ->limit(6)
            ->get();

        // Trading Hours
        $hoursData = Deal::where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->get()
            ->groupBy(function($deal) {
                return \Carbon\Carbon::parse($deal->time)->format('H');
            })
            ->map(function($deals) {
                return $deals->count();
            })
            ->sortKeys();

        $hoursLabels = [];
        $hoursValues = [];
        for($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hoursLabels[] = $hour . ':00';
            $hoursValues[] = $hoursData->get($hour, 0);
        }

        return [
            'equity' => [
                'labels' => $equityLabels,
                'data' => $equityValues,
            ],
            'symbols' => [
                'labels' => $symbolData->pluck('normalized_symbol')->toArray(),
                'data' => $symbolData->pluck('count')->toArray(),
            ],
            'hours' => [
                'labels' => $hoursLabels,
                'data' => $hoursValues,
            ],
        ];
    }

    private function calculateAccountStats($account)
    {
        $accountId = $account->id;

        // Total trades
        $totalTrades = Deal::where('trading_account_id', $accountId)
            ->whereIn('entry', ['out', 'inout'])
            ->count();

        // Winning trades
        $winningTrades = Deal::where('trading_account_id', $accountId)
            ->whereIn('entry', ['out', 'inout'])
            ->where('profit', '>', 0)
            ->count();

        // Total profit/loss
        $totalProfit = Deal::where('trading_account_id', $accountId)
            ->whereIn('entry', ['out', 'inout'])
            ->sum('profit');

        // Average profit per trade
        $avgProfit = $totalTrades > 0 ? $totalProfit / $totalTrades : 0;

        // Win rate
        $winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;

        // Most traded symbol
        $mostTradedSymbol = Deal::select('symbol', DB::raw('count(*) as count'))
            ->where('trading_account_id', $accountId)
            ->groupBy('symbol')
            ->orderBy('count', 'desc')
            ->first();

        return [
            'total_trades' => $totalTrades,
            'winning_trades' => $winningTrades,
            'losing_trades' => $totalTrades - $winningTrades,
            'win_rate' => round($winRate, 2),
            'total_profit' => $totalProfit,
            'avg_profit' => round($avgProfit, 2),
            'most_traded_symbol' => $mostTradedSymbol->normalized_symbol ?? 'N/A',
        ];
    }


    /**
     * Prepare equity and balance chart data for all accounts
     */
    private function prepareAccountsChartData($accounts, $displayCurrency)
    {
        $chartData = [];
        $currencyService = app(\App\Services\CurrencyService::class);

        foreach ($accounts as $account) {
        // Get balance history for last 30 days
        $history = Deal::where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->orderBy('time', 'asc')
            ->get();

        $balancePoints = [];
        $equityPoints = [];
        $runningBalance = 0;

        // Get starting balance (30 days ago)
        $startingBalance = Deal::where('trading_account_id', $account->id)
            ->where('time', '<', now()->subDays(30))
            ->sum('profit') ?? 0;

        $runningBalance = $account->balance - $history->sum('profit');

        // Build data points
        foreach ($history as $deal) {
            if ($deal->time) {
            $runningBalance += $deal->profit;

            // Convert to display currency
            $balanceInDisplayCurrency = $currencyService->convert(
                $runningBalance,
                $account->account_currency,
                $displayCurrency
            );

            $equityInDisplayCurrency = $currencyService->convert(
                $runningBalance + ($account->profit ?? 0),
                $account->account_currency,
                $displayCurrency
            );

            $balancePoints[] = [
                'x' => $deal->time->format('Y-m-d H:i:s'),
                'y' => round($balanceInDisplayCurrency, 2)
            ];

            $equityPoints[] = [
                'x' => $deal->time->format('Y-m-d H:i:s'),
                'y' => round($equityInDisplayCurrency, 2)
            ];
            }
        }

        // Add current point (converted to display currency)
        $currentBalance = $currencyService->convert(
            $account->balance,
            $account->account_currency,
            $displayCurrency
        );

        $currentEquity = $currencyService->convert(
            $account->equity,
            $account->account_currency,
            $displayCurrency
        );

        $balancePoints[] = [
            'x' => now()->format('Y-m-d H:i:s'),
            'y' => round($currentBalance, 2)
        ];

        $equityPoints[] = [
            'x' => now()->format('Y-m-d H:i:s'),
            'y' => round($currentEquity, 2)
        ];

        // Random color for each account
        $colors = [
            ['rgb' => '59, 130, 246', 'name' => 'Blue'],
            ['rgb' => '16, 185, 129', 'name' => 'Green'],
            ['rgb' => '249, 115, 22', 'name' => 'Orange'],
            ['rgb' => '139, 92, 246', 'name' => 'Purple'],
            ['rgb' => '236, 72, 153', 'name' => 'Pink'],
            ['rgb' => '245, 158, 11', 'name' => 'Amber'],
            ['rgb' => '14, 165, 233', 'name' => 'Sky'],
            ['rgb' => '168, 85, 247', 'name' => 'Violet'],
        ];

        $colorIndex = $account->id % count($colors);
        $color = $colors[$colorIndex];

        $chartData[] = [
            'account_id' => $account->id,
            'account_name' => $account->broker_name . ' - ' . ($account->account_number ?? 'Account'),
            'currency' => $account->account_currency,
            'display_currency' => $displayCurrency,
            'color' => $color['rgb'],
            'balance_data' => $balancePoints,
            'equity_data' => $equityPoints,
        ];
        }

        return $chartData;
    }



}
