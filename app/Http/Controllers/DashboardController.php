<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\Sortable;
use App\Services\PositionAggregationService;


class DashboardController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $user = $request->user();
        $displayCurrency = $user->display_currency;
        $sortBy = $request->get('sort_by', 'last_sync_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Cache key unique to user, currency, and sorting
        $cacheKey = "dashboard.user.{$user->id}.{$displayCurrency}.{$sortBy}.{$sortDirection}";

        // Cache for 2 minutes (balance changes frequently)
        $dashboardData = Cache::remember($cacheKey, 120, function() use ($user, $displayCurrency, $request, $sortBy, $sortDirection) {
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
            // RULE: Multi-account view = Always convert to USD
            $currencyService = app(\App\Services\CurrencyService::class);
            
            $totalBalance = 0;
            $totalEquity = 0;
            $totalProfit = 0;
            
            foreach ($accounts as $account) {
                // Convert each account to USD
                $totalBalance += $currencyService->convert(
                    $account->balance,
                    $account->account_currency ?? 'USD',
                    'USD'
                );
                
                $totalEquity += $currencyService->convert(
                    $account->equity,
                    $account->account_currency ?? 'USD',
                    'USD'
                );
                
                $totalProfit += $currencyService->convert(
                    $account->profit ?? 0,
                    $account->account_currency ?? 'USD',
                    'USD'
                );
            }
            
            $totals = [
                'accounts' => $accounts->count(),
                'total_balance' => round($totalBalance, 2),
                'total_equity' => round($totalEquity, 2),
                'total_profit' => round($totalProfit, 2),
                'display_currency' => 'USD', // Always USD for multi-account view
                'open_positions' => $accounts->sum(function($acc) {
                    return $acc->openPositions->count();
                }),
                'pending_orders' => $accounts->sum(function($acc) {
                    return $acc->activeOrders->count();
                }),
            ];

            // Charts Data (convert to USD for multi-account view)
            $accountsChartData = $this->prepareAccountsChartData($accounts, 'USD');

            return [
                'accounts' => $accounts,
                'totals' => $totals,
                'accountsChartData' => $accountsChartData,
            ];
        });

        // Recent positions (closed) - cache separately (1 minute, updates more frequently)
        $recentPositions = Cache::remember("dashboard.positions.{$user->id}", 60, function() use ($user) {
            $accountIds = $user->tradingAccounts()->pluck('id');
            if ($accountIds->isEmpty()) {
                return collect();
            }
            
            return Position::whereIn('trading_account_id', $accountIds)
                ->where('is_open', false)
                ->with('tradingAccount')
                ->orderBy('update_time', 'desc')
                ->limit(20)
                ->get();
        });

        // Get account limit info (not cached, lightweight)
        $accountLimit = $user->getAccountLimitInfo();

        // Collect all open positions from all accounts (for dashboard display)
        $allOpenPositions = $dashboardData['accounts']->flatMap(function($account) {
            return $account->openPositions->map(function($position) use ($account) {
                $position->account_currency = $account->account_currency;
                $position->account_number = $account->account_number;
                $position->broker_name = $account->broker_name;
                return $position;
            });
        });

        return view('dashboard', array_merge($dashboardData, [
            'user' => $user,
            'recentPositions' => $recentPositions,
            'accountLimit' => $accountLimit,
            'allOpenPositions' => $allOpenPositions,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]));
    }

    public function account(Request $request, $accountId, PositionAggregationService $positionService)
    {
        $user = $request->user();
        $sortBy = $request->get('sort_by', 'time');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Cache key for account details
        $cacheKey = "account.{$accountId}.details.{$sortBy}.{$sortDirection}";

        // Cache for 2 minutes
        $accountData = Cache::remember($cacheKey, 120, function() use ($accountId, $user, $request, $sortBy, $sortDirection) {
            // Get specific account (ensure it belongs to user)
            $account = TradingAccount::where('id', $accountId)
                ->where('user_id', $user->id)
                ->with(['openPositions', 'activeOrders'])
                ->firstOrFail();

            // Calculate statistics
            $stats = $this->calculateAccountStats($account);

            // Prepare chart data
            $chartData = $this->prepareChartData($account);

            return [
                'account' => $account,
                'stats' => $stats,
                'chartData' => $chartData,
            ];
        });

        // Get account
        $account = $accountData['account'];
        
        // Get all positions (filtering done client-side with Alpine.js)
        $positions = Position::where('trading_account_id', $account->id)
            ->where('open_time', '>=', now()->subDays(30))
            ->orderBy('open_time', 'desc')
            ->paginate(20);
        
        // Load deals for each position
        foreach ($positions as $position) {
            if ($position->platform_type === 'MT5' && $position->position_identifier) {
                // For MT5 netting, get all deals with same position_identifier
                $position->deals = Deal::where('trading_account_id', $account->id)
                    ->where('position_id', $position->id)
                    ->orderBy('time', 'asc')
                    ->limit(100)
                    ->get();
            } else {
                // For MT4/MT5 hedging, get deals with same ticket
                $position->deals = Deal::where('trading_account_id', $account->id)
                    ->where('ticket', $position->ticket)
                    ->orderBy('time', 'asc')
                    ->limit(100)
                    ->get();
            }
        }

        return view('account.show', array_merge($accountData, [
            'positions' => $positions,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]));
    }

    private function prepareChartData($account)
    {
        // Equity Curve (last 30 days)
        $equityData = Deal::where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->where('entry', 'out')
            ->orderBy('time')
            ->limit(1000)
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
            ->limit(1000)
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
     * RULE: Multi-account view = Convert to USD
     */
    private function prepareAccountsChartData($accounts, $displayCurrency = 'USD')
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

        // Build data points (convert to USD for multi-account view)
        foreach ($history as $deal) {
            if ($deal->time) {
            $runningBalance += $deal->profit;

            // Convert to USD
            $balanceInUSD = $currencyService->convert(
                $runningBalance,
                $account->account_currency ?? 'USD',
                $displayCurrency
            );

            $equityInUSD = $currencyService->convert(
                $runningBalance + ($account->profit ?? 0),
                $account->account_currency ?? 'USD',
                $displayCurrency
            );

            $balancePoints[] = [
                'x' => $deal->time->format('Y-m-d H:i:s'),
                'y' => round($balanceInUSD, 2)
            ];

            $equityPoints[] = [
                'x' => $deal->time->format('Y-m-d H:i:s'),
                'y' => round($equityInUSD, 2)
            ];
            }
        }

        // Add current point (convert to USD)
        $currentBalanceUSD = $currencyService->convert(
            $account->balance,
            $account->account_currency ?? 'USD',
            $displayCurrency
        );

        $currentEquityUSD = $currencyService->convert(
            $account->equity,
            $account->account_currency ?? 'USD',
            $displayCurrency
        );

        $balancePoints[] = [
            'x' => now()->format('Y-m-d H:i:s'),
            'y' => round($currentBalanceUSD, 2)
        ];

        $equityPoints[] = [
            'x' => now()->format('Y-m-d H:i:s'),
            'y' => round($currentEquityUSD, 2)
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
            'display_currency' => $displayCurrency, // Always USD for multi-account
            'color' => $color['rgb'],
            'balance_data' => $balancePoints,
            'equity_data' => $equityPoints,
        ];
        }

        return $chartData;
    }



}
