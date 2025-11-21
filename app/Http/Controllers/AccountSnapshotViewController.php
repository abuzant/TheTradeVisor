<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use App\Models\AccountSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Helpers\TimeFilterHelper;

class AccountSnapshotViewController extends Controller
{
    /**
     * Account Health overview - shows selected accounts with their health metrics side-by-side
     */
    public function accountHealth(Request $request)
    {
        $user = auth()->user();
        $allAccounts = $user->tradingAccounts()->get();

        if ($allAccounts->isEmpty()) {
            return redirect()->route('accounts.index')
                ->with('info', 'Please add a trading account to view account health.');
        }

        // Get time range from request (default: 7 days)
        $days = $request->input('days', 7);
        $days = in_array((int)$days, [7, 30, 90, 180], true) ? (int)$days : 7;
        
        // Get available time periods (use first account for data access check)
        $timePeriods = TimeFilterHelper::getPeriodsForAccount($allAccounts->first());

        // Get selected account IDs from request (default: first 2 accounts)
        $selectedIds = $request->input('accounts', []);
        if (empty($selectedIds)) {
            $selectedIds = $allAccounts->take(2)->pluck('id')->toArray();
        } else {
            // Ensure it's an array and limit to 2
            $selectedIds = is_array($selectedIds) ? array_slice($selectedIds, 0, 2) : [$selectedIds];
        }

        // Get selected accounts
        $selectedAccounts = $allAccounts->whereIn('id', $selectedIds)->values();

        // Get health data for selected accounts
        $accountsData = [];
        foreach ($selectedAccounts as $account) {
            $accountsData[] = [
                'account' => $account,
                'data' => $this->getViewData($account, $days),
            ];
        }

        return view('accounts.health-overview', [
            'accountsData' => $accountsData,
            'allAccounts' => $allAccounts,
            'selectedIds' => $selectedIds,
            'days' => $days,
            'timePeriods' => $timePeriods,
        ]);
    }

    /**
     * Display the account snapshots page with widgets
     * 
     * Caching strategy:
     * - 7 days: Cache for 2 hours (data changes frequently)
     * - 30/90/180 days: Cache for 24 hours (historical data is stable)
     */
    public function index(Request $request, TradingAccount $account)
    {
        // Authorization: User can only view their own account snapshots
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this account.');
        }

        // Get available time periods based on account's data access
        $timePeriods = TimeFilterHelper::getPeriodsForAccount($account);
        
        // Get time range from request (default: 7 days)
        $days = $request->input('days', 7);
        $days = in_array((int)$days, [7, 30, 90, 180], true) ? (int)$days : 7;
        
        // Check if requested period is locked
        $periodKey = $days . 'd';
        $requestedPeriodData = $timePeriods[$periodKey] ?? null;
        if ($requestedPeriodData && $requestedPeriodData['locked']) {
            // Redirect to default period if trying to access locked period
            return redirect()->route('account.snapshots', ['account' => $account->id, 'days' => 7])
                ->with('error', 'This time period requires enterprise broker access. Ask your broker about enterprise access.');
        }

        // Determine cache TTL based on time range
        $cacheTTL = $days === 7 ? 7200 : 86400; // 2 hours for 7d, 24 hours for others
        
        // Create unique cache key
        $cacheKey = "account_snapshots_{$account->id}_days_{$days}";

        // Cache the entire view data
        $viewData = Cache::remember($cacheKey, $cacheTTL, function () use ($account, $days) {
            return $this->getViewData($account, $days);
        });

        return view('accounts.snapshots', array_merge($viewData, [
            'account' => $account,
            'days' => $days,
            'timePeriods' => $timePeriods,
        ]));
    }

    /**
     * Get all data needed for the view
     */
    private function getViewData(TradingAccount $account, int $days): array
    {

        // Get current snapshot (latest)
        $currentSnapshot = AccountSnapshot::where('trading_account_id', $account->id)
            ->orderBy('snapshot_time', 'desc')
            ->first();

        // Get previous snapshot (24 hours ago) for change calculation
        $previousSnapshot = AccountSnapshot::where('trading_account_id', $account->id)
            ->where('snapshot_time', '<=', now()->subDay())
            ->orderBy('snapshot_time', 'desc')
            ->first();

        // Calculate 24h changes
        $changes = $this->calculateChanges($currentSnapshot, $previousSnapshot);

        // Get daily snapshots for chart (last snapshot of each day)
        $chartData = $this->getChartData($account->id, $days);

        // Get statistics with max drawdown
        $statistics = $this->getStatistics($account->id, $days);

        return [
            'currentSnapshot' => $currentSnapshot,
            'changes' => $changes,
            'chartData' => $chartData,
            'statistics' => $statistics,
        ];
    }

    /**
     * Calculate 24-hour changes
     */
    private function calculateChanges($current, $previous)
    {
        if (!$current || !$previous) {
            return [
                'balance' => 0,
                'equity' => 0,
                'margin_level' => 0,
                'profit' => 0,
            ];
        }

        return [
            'balance' => $this->calculatePercentageChange($previous->balance, $current->balance),
            'equity' => $this->calculatePercentageChange($previous->equity, $current->equity),
            'margin_level' => $this->calculatePercentageChange($previous->margin_level, $current->margin_level),
            'profit' => $current->profit - $previous->profit,
        ];
    }

    /**
     * Calculate percentage change
     */
    private function calculatePercentageChange($old, $new)
    {
        if ($old == 0) {
            return 0;
        }
        return (($new - $old) / abs($old)) * 100;
    }

    /**
     * Get chart data (daily snapshots)
     */
    private function getChartData($accountId, $days)
    {
        $startDate = now()->subDays($days);

        // Get last snapshot of each day using window function
        $snapshots = DB::table('account_snapshots')
            ->select([
                DB::raw('DATE(snapshot_time) as date'),
                DB::raw('MAX(balance) as balance'),
                DB::raw('MAX(equity) as equity'),
                DB::raw('MAX(margin) as margin'),
                DB::raw('MAX(free_margin) as free_margin'),
                DB::raw('MAX(profit) as profit'),
            ])
            ->where('trading_account_id', $accountId)
            ->where('snapshot_time', '>=', $startDate)
            ->groupBy(DB::raw('DATE(snapshot_time)'))
            ->orderBy('date', 'asc')
            ->get();

        // Format for Chart.js
        return [
            'labels' => $snapshots->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'balance' => $snapshots->pluck('balance')->map(fn($v) => (float)$v)->toArray(),
            'equity' => $snapshots->pluck('equity')->map(fn($v) => (float)$v)->toArray(),
            'margin' => $snapshots->pluck('margin')->map(fn($v) => (float)$v)->toArray(),
            'free_margin' => $snapshots->pluck('free_margin')->map(fn($v) => (float)$v)->toArray(),
            'profit' => $snapshots->pluck('profit')->map(fn($v) => (float)$v)->toArray(),
        ];
    }

    /**
     * Get statistics including max drawdown
     */
    private function getStatistics($accountId, $days)
    {
        $startDate = now()->subDays($days);

        $stats = AccountSnapshot::where('trading_account_id', $accountId)
            ->where('snapshot_time', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total_snapshots,
                MAX(balance) as max_balance,
                MIN(balance) as min_balance,
                AVG(balance) as avg_balance,
                MAX(equity) as max_equity,
                MIN(equity) as min_equity,
                AVG(equity) as avg_equity,
                MAX(margin) as max_margin,
                AVG(margin) as avg_margin,
                MAX(profit) as max_profit,
                MIN(profit) as min_profit
            ')
            ->first();

        // Calculate max drawdown
        $maxDrawdown = $this->calculateMaxDrawdown($accountId, $startDate);

        return [
            'total_snapshots' => $stats->total_snapshots ?? 0,
            'balance' => [
                'max' => $stats->max_balance ?? 0,
                'min' => $stats->min_balance ?? 0,
                'avg' => $stats->avg_balance ?? 0,
            ],
            'equity' => [
                'max' => $stats->max_equity ?? 0,
                'min' => $stats->min_equity ?? 0,
                'avg' => $stats->avg_equity ?? 0,
            ],
            'margin' => [
                'max' => $stats->max_margin ?? 0,
                'avg' => $stats->avg_margin ?? 0,
            ],
            'profit' => [
                'max' => $stats->max_profit ?? 0,
                'min' => $stats->min_profit ?? 0,
            ],
            'max_drawdown' => $maxDrawdown,
        ];
    }

    /**
     * Calculate maximum drawdown percentage
     */
    private function calculateMaxDrawdown($accountId, $startDate)
    {
        $snapshots = AccountSnapshot::where('trading_account_id', $accountId)
            ->where('snapshot_time', '>=', $startDate)
            ->orderBy('snapshot_time', 'asc')
            ->pluck('equity')
            ->toArray();

        if (empty($snapshots)) {
            return 0;
        }

        $maxDrawdown = 0;
        $peak = $snapshots[0];

        foreach ($snapshots as $equity) {
            if ($equity > $peak) {
                $peak = $equity;
            }

            if ($peak > 0) {
                $drawdown = (($peak - $equity) / $peak) * 100;
                $maxDrawdown = max($maxDrawdown, $drawdown);
            }
        }

        return round($maxDrawdown, 2);
    }
}
