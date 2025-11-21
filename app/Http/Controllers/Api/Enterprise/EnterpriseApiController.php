<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\WhitelistedBrokerUsage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnterpriseApiController extends Controller
{
    /**
     * Get enterprise broker from request
     */
    private function getBroker(Request $request)
    {
        return $request->attributes->get('enterprise_broker');
    }

    /**
     * Get all account IDs for this broker
     */
    private function getBrokerAccountIds(Request $request)
    {
        $broker = $this->getBroker($request);
        
        return WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->pluck('trading_account_id')
            ->toArray();
    }

    /**
     * Apply filters to account query
     */
    private function applyFilters($query, Request $request)
    {
        // Platform filter
        if ($request->has('platform') && $request->platform !== 'all') {
            $query->where('platform_type', strtoupper($request->platform));
        }

        // Country filter
        if ($request->has('country') && $request->country !== 'all') {
            $query->where('country_code', strtoupper($request->country));
        }

        // Status filter (active/dormant)
        if ($request->has('status') && $request->status !== 'all') {
            $days = 30; // Dormant threshold
            
            if ($request->status === 'active') {
                $query->whereHas('whitelistedBrokerUsage', function($q) use ($days) {
                    $q->where('last_seen_at', '>=', now()->subDays($days));
                });
            } elseif ($request->status === 'dormant') {
                $query->whereHas('whitelistedBrokerUsage', function($q) use ($days) {
                    $q->where('last_seen_at', '<', now()->subDays($days))
                      ->orWhereNull('last_seen_at');
                });
            }
        }

        return $query;
    }

    /**
     * Get date range based on period parameter
     */
    private function getDateRange(Request $request)
    {
        $period = $request->input('period', '30d');
        
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '60d' => 60,
            '90d' => 90,
            '180d' => 180,
            default => 30,
        };

        return [
            'start' => now()->subDays($days),
            'end' => now(),
            'days' => $days,
        ];
    }

    /**
     * Endpoint 1: Get All Accounts
     * GET /api/enterprise/v1/accounts
     */
    public function accounts(Request $request)
    {
        $broker = $this->getBroker($request);
        $accountIds = $this->getBrokerAccountIds($request);

        // Build query
        $query = TradingAccount::whereIn('id', $accountIds);
        $query = $this->applyFilters($query, $request);

        // Get total counts
        $totalAccounts = count($accountIds);
        $activeCount = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->where('last_seen_at', '>=', now()->subDays(30))
            ->count();
        $dormantCount = $totalAccounts - $activeCount;

        // Paginate
        $perPage = min($request->input('per_page', 50), 100); // Max 100 per page
        $accounts = $query->paginate($perPage);

        // Format response
        $data = $accounts->map(function($account) {
            $usage = $account->whitelistedBrokerUsage;
            
            return [
                'account_number' => $account->account_number,
                'platform' => $account->platform_type,
                'country' => $account->country_code,
                'balance' => (float) $account->balance,
                'equity' => (float) $account->equity,
                'profit' => (float) $account->profit,
                'trades' => $account->deals()->where('entry', 'out')->count(),
                'last_activity' => $usage ? $usage->last_seen_at?->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $totalAccounts,
                'active' => $activeCount,
                'dormant' => $dormantCount,
                'accounts' => $data,
            ],
            'pagination' => [
                'current_page' => $accounts->currentPage(),
                'total_pages' => $accounts->lastPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
        ]);
    }

    /**
     * Endpoint 2: Get Aggregated Metrics
     * GET /api/enterprise/v1/metrics
     */
    public function metrics(Request $request)
    {
        $broker = $this->getBroker($request);
        $accountIds = $this->getBrokerAccountIds($request);
        $dateRange = $this->getDateRange($request);

        // Apply filters
        $query = TradingAccount::whereIn('id', $accountIds);
        $query = $this->applyFilters($query, $request);
        $filteredAccountIds = $query->pluck('id')->toArray();

        // Symbol filter
        $dealsQuery = Deal::whereIn('trading_account_id', $filteredAccountIds)
            ->where('entry', 'out')
            ->where('time', '>=', $dateRange['start']);

        if ($request->has('symbol') && $request->symbol !== 'all') {
            $dealsQuery->where('symbol', strtoupper($request->symbol));
        }

        // Calculate metrics
        $totalAccounts = count($filteredAccountIds);
        $totalBalance = TradingAccount::whereIn('id', $filteredAccountIds)->sum('balance');
        $totalEquity = TradingAccount::whereIn('id', $filteredAccountIds)->sum('equity');
        
        $deals = $dealsQuery->get();
        $totalProfit = $deals->sum('profit');
        $totalTrades = $deals->count();
        
        $winningTrades = $deals->where('profit', '>', 0)->count();
        $winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
        
        $grossProfit = $deals->where('profit', '>', 0)->sum('profit');
        $grossLoss = abs($deals->where('profit', '<', 0)->sum('profit'));
        $profitFactor = $grossLoss > 0 ? $grossProfit / $grossLoss : 0;

        // Best and worst symbols
        $symbolStats = $deals->groupBy('symbol')->map(function($symbolDeals) {
            return $symbolDeals->sum('profit');
        })->sortDesc();

        return response()->json([
            'success' => true,
            'data' => [
                'total_accounts' => $totalAccounts,
                'total_balance' => round($totalBalance, 2),
                'total_equity' => round($totalEquity, 2),
                'total_profit' => round($totalProfit, 2),
                'total_trades' => $totalTrades,
                'win_rate' => round($winRate, 2),
                'profit_factor' => round($profitFactor, 2),
                'best_symbol' => $symbolStats->keys()->first(),
                'worst_symbol' => $symbolStats->keys()->last(),
            ],
        ]);
    }

    /**
     * Endpoint 3: Get Performance Data
     * GET /api/enterprise/v1/performance
     */
    public function performance(Request $request)
    {
        $accountIds = $this->getBrokerAccountIds($request);
        $dateRange = $this->getDateRange($request);

        // Apply filters
        $query = TradingAccount::whereIn('id', $accountIds);
        $query = $this->applyFilters($query, $request);
        $filteredAccountIds = $query->pluck('id')->toArray();

        // Equity curve (daily aggregated equity)
        $equityCurve = TradingAccount::whereIn('id', $filteredAccountIds)
            ->selectRaw('DATE(updated_at) as date, SUM(equity) as total_equity')
            ->where('updated_at', '>=', $dateRange['start'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date,
                    'equity' => round($item->total_equity, 2),
                ];
            });

        // Profit by symbol
        $profitBySymbol = Deal::whereIn('trading_account_id', $filteredAccountIds)
            ->where('entry', 'out')
            ->where('time', '>=', $dateRange['start'])
            ->selectRaw('symbol, SUM(profit) as total_profit')
            ->groupBy('symbol')
            ->orderByDesc('total_profit')
            ->limit(20)
            ->get()
            ->pluck('total_profit', 'symbol')
            ->map(fn($profit) => round($profit, 2));

        // Profit by country
        $profitByCountry = Deal::whereIn('trading_account_id', $filteredAccountIds)
            ->where('entry', 'out')
            ->where('time', '>=', $dateRange['start'])
            ->join('trading_accounts', 'deals.trading_account_id', '=', 'trading_accounts.id')
            ->selectRaw('trading_accounts.country_code, SUM(deals.profit) as total_profit')
            ->groupBy('trading_accounts.country_code')
            ->get()
            ->pluck('total_profit', 'country_code')
            ->map(fn($profit) => round($profit, 2));

        return response()->json([
            'success' => true,
            'data' => [
                'equity_curve' => $equityCurve,
                'profit_by_symbol' => $profitBySymbol,
                'profit_by_country' => $profitByCountry,
            ],
        ]);
    }

    /**
     * Endpoint 4: Get Top Performers
     * GET /api/enterprise/v1/top-performers
     */
    public function topPerformers(Request $request)
    {
        $accountIds = $this->getBrokerAccountIds($request);
        $dateRange = $this->getDateRange($request);
        $limit = min($request->input('limit', 10), 50); // Max 50
        $sortBy = $request->input('sort', 'profit'); // profit, win_rate, trades

        // Get account performance
        $accountPerformance = Deal::whereIn('trading_account_id', $accountIds)
            ->where('entry', 'out')
            ->where('time', '>=', $dateRange['start'])
            ->selectRaw('
                trading_account_id,
                SUM(profit) as total_profit,
                COUNT(*) as total_trades,
                SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades
            ')
            ->groupBy('trading_account_id')
            ->get();

        // Calculate win rate and sort
        $performers = $accountPerformance->map(function($perf) {
            $account = TradingAccount::find($perf->trading_account_id);
            $winRate = $perf->total_trades > 0 ? ($perf->winning_trades / $perf->total_trades) * 100 : 0;

            return [
                'account_number' => $account->account_number,
                'profit' => round($perf->total_profit, 2),
                'win_rate' => round($winRate, 2),
                'trades' => $perf->total_trades,
            ];
        });

        // Sort based on criteria
        if ($sortBy === 'win_rate') {
            $performers = $performers->sortByDesc('win_rate');
        } elseif ($sortBy === 'trades') {
            $performers = $performers->sortByDesc('trades');
        } else {
            $performers = $performers->sortByDesc('profit');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'top_accounts' => $performers->take($limit)->values(),
            ],
        ]);
    }

    /**
     * Endpoint 5: Get Trading Hours Analysis
     * GET /api/enterprise/v1/trading-hours
     */
    public function tradingHours(Request $request)
    {
        $accountIds = $this->getBrokerAccountIds($request);
        $dateRange = $this->getDateRange($request);

        // Get profit by hour
        $hourlyStats = Deal::whereIn('trading_account_id', $accountIds)
            ->where('entry', 'out')
            ->where('time', '>=', $dateRange['start'])
            ->selectRaw('EXTRACT(HOUR FROM time) as hour, SUM(profit) as total_profit, COUNT(*) as total_trades')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($stat) {
                return [
                    'hour' => (int) $stat->hour,
                    'profit' => round($stat->total_profit, 2),
                    'trades' => $stat->total_trades,
                ];
            });

        $bestHours = $hourlyStats->sortByDesc('profit')->take(5)->values();
        $worstHours = $hourlyStats->sortBy('profit')->take(5)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'best_hours' => $bestHours,
                'worst_hours' => $worstHours,
                'all_hours' => $hourlyStats->values(),
            ],
        ]);
    }

    /**
     * Endpoint 6: Export Data
     * GET /api/enterprise/v1/export
     */
    public function export(Request $request)
    {
        // For now, return JSON. In future, can add CSV/Excel/PDF generation
        $format = $request->input('format', 'json');
        $type = $request->input('type', 'accounts');

        if ($format !== 'json') {
            return response()->json([
                'success' => false,
                'error' => 'UNSUPPORTED_FORMAT',
                'message' => 'Only JSON format is currently supported. CSV/Excel/PDF coming soon.',
            ], 400);
        }

        // Delegate to appropriate endpoint
        switch ($type) {
            case 'accounts':
                return $this->accounts($request);
            case 'metrics':
                return $this->metrics($request);
            case 'performance':
                return $this->performance($request);
            default:
                return response()->json([
                    'success' => false,
                    'error' => 'INVALID_TYPE',
                    'message' => 'Type must be one of: accounts, metrics, performance',
                ], 400);
        }
    }
}
