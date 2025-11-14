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
        
        // Removed emergency logging - user bleeding issue resolved
        
        // Multi-account context: Always use USD
        $displayCurrency = 'USD';
        $sortBy = $request->get('sort_by', 'last_sync_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Cache key: user + session + IP for security (prevents cache poisoning)
        $sessionId = session()->getId();
        $userIp = $request->ip();
        $cacheKey = "dashboard.user.{$user->id}.{$sessionId}.{$userIp}.usd.{$sortBy}.{$sortDirection}";

        // Cache for 5 minutes (balance changes frequently)
        $dashboardData = Cache::remember($cacheKey, 300, function() use ($user, $displayCurrency, $request, $sortBy, $sortDirection) {
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

        // Recent closed trades - Cache with user + session + IP
        // Use Deal model with entry='out' and group by position_id to build
        // position-like objects with expandable deal history (same UX as account page)
        $positionsCacheKey = "dashboard.closed_trades.{$user->id}.{$sessionId}.{$userIp}";
        $recentPositions = Cache::remember($positionsCacheKey, 300, function() use ($user) {
            $accountIds = $user->tradingAccounts()->pluck('id')->toArray();
            if (empty($accountIds)) {
                return collect();
            }

            // Get closed trades (OUT deals) for last 30 days across all accounts
            $closedTrades = Deal::closedTrades()
                ->forAccounts($accountIds)
                ->with('tradingAccount')
                ->dateRange(now()->subDays(30))
                ->recent()
                ->limit(1000)
                ->get();

            if ($closedTrades->isEmpty()) {
                return collect();
            }

            // Group by position_id to build position-level view with deals
            $positions = $closedTrades->groupBy('position_id')->map(function($positionDeals, $positionId) {
                $sampleDeal = $positionDeals->first();
                $account = $sampleDeal->tradingAccount;

                // Load all deals (IN + OUT) for this position on this account
                $allDeals = Deal::forPosition($positionId)
                    ->forAccount($sampleDeal->trading_account_id)
                    ->orderBy('time', 'asc')
                    ->limit(100)
                    ->get();

                $inDeal = $allDeals->where('entry', 'in')->first();
                $outDeal = $allDeals->where('entry', 'out')->sortByDesc('time')->first();

                // Determine position type from IN deal (not closing action)
                $positionType = $inDeal ? $inDeal->display_type : ($outDeal ? $outDeal->display_type : null);
                $isBuy = $inDeal ? $inDeal->is_buy : false;

                return (object) [
                    'position_id' => $positionId,
                    'symbol' => $outDeal ? $outDeal->symbol : $sampleDeal->symbol,
                    'normalized_symbol' => $outDeal ? $outDeal->normalized_symbol : $sampleDeal->normalized_symbol,
                    'type' => $positionType,
                    'display_type' => $positionType,
                    'is_buy' => $isBuy,
                    'is_open' => false,
                    'volume' => $allDeals->where('entry', 'in')->sum('volume'),
                    'open_price' => $inDeal ? $inDeal->price : 0,
                    'close_price' => $outDeal ? $outDeal->price : 0,
                    'profit' => $allDeals->where('entry', 'out')->sum('profit'),
                    'commission' => $allDeals->sum('commission'),
                    'swap' => $outDeal ? $outDeal->swap : 0,
                    'open_time' => $inDeal ? $inDeal->time : null,
                    'close_time' => $outDeal ? $outDeal->time : null,
                    'deals' => $allDeals,
                    'deal_count' => $allDeals->count(),
                    'trading_account_id' => $sampleDeal->trading_account_id,
                    'platform_type' => $account->platform_type ?? 'MT4',
                    'position_identifier' => $positionId,
                    'account_currency' => $account->account_currency ?? 'USD',
                    'account_number' => $account->account_number,
                    'broker_name' => $account->broker_name,
                ];
            })->values();

            // Limit to most recent 20 positions by close_time
            return $positions->sortByDesc('close_time')->take(20)->values();
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

        // Emergency logging removed - issue resolved
        
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

        // Cache key for account details - user + session + IP for security
        $sessionId = session()->getId();
        $userIp = $request->ip();
        $cacheKey = "account.{$user->id}.{$sessionId}.{$userIp}.{$accountId}.details.{$sortBy}.{$sortDirection}";

        // Cache for 5 minutes
        $accountData = Cache::remember($cacheKey, 300, function() use ($accountId, $user, $request, $sortBy, $sortDirection) {
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
        
        // Get recent closed trades (last 30 days)
        // FIXED: Use Deal model with entry='out' for complete trade history
        $closedTrades = Deal::closedTrades()
            ->forAccount($account->id)
            ->dateRange(now()->subDays(30))
            ->recent()
            ->limit(1000)
            ->get();
        
        // Group by position_id to show position-level view
        $positions = $closedTrades->groupBy('position_id')->map(function($positionDeals, $positionId) use ($account) {
            // Get all deals for this position (IN and OUT)
            $allDeals = Deal::forPosition($positionId)
                ->forAccount($account->id)
                ->orderBy('time', 'asc')
                ->limit(100)
                ->get();
            
            $inDeal = $allDeals->where('entry', 'in')->first();
            $outDeal = $allDeals->where('entry', 'out')->sortByDesc('time')->first();
            
            // Determine position type from IN deal (not OUT deal)
            $positionType = $inDeal ? $inDeal->display_type : $outDeal->display_type;
            $isBuy = $inDeal ? $inDeal->is_buy : false;
            
            // Create a position-like object for display
            return (object) [
                'position_id' => $positionId,
                'symbol' => $outDeal->symbol,
                'normalized_symbol' => $outDeal->normalized_symbol,
                'type' => $positionType, // Use position type, not closing action
                'display_type' => $positionType,
                'is_buy' => $isBuy,
                'is_open' => false, // These are all closed positions
                'volume' => $allDeals->where('entry', 'in')->sum('volume'),
                'open_price' => $inDeal->price ?? 0,
                'close_price' => $outDeal->price ?? 0,
                'profit' => $allDeals->where('entry', 'out')->sum('profit'),
                'commission' => $allDeals->sum('commission'),
                'swap' => $outDeal->swap ?? 0,
                'open_time' => $inDeal->time ?? null,
                'close_time' => $outDeal->time ?? null,
                'deals' => $allDeals,
                'deal_count' => $allDeals->count(), // Number of deals for this position
                'trading_account_id' => $account->id,
                'platform_type' => $account->platform_type ?? 'MT4', // Platform type
                'position_identifier' => $positionId, // Position identifier
            ];
        })->values();
        
        // Paginate manually
        $currentPage = request()->get('page', 1);
        $perPage = 20;
        $positions = new \Illuminate\Pagination\LengthAwarePaginator(
            $positions->forPage($currentPage, $perPage),
            $positions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        // Get balance history for last 30 days (limit to 5000 points for chart)
        $history = Deal::where('trading_account_id', $account->id)
            ->where('time', '>=', now()->subDays(30))
            ->orderBy('time', 'asc')
            ->limit(5000)
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
