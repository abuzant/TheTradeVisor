<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use App\Models\AccountSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountSnapshotViewController extends Controller
{
    /**
     * Display the account snapshots page with widgets
     */
    public function index(Request $request, TradingAccount $account)
    {
        // Authorization: User can only view their own account snapshots
        if ($account->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this account.');
        }

        // Get time range from request (default: 30 days)
        $days = $request->input('days', 30);
        $days = in_array($days, [7, 30, 90, 180]) ? $days : 30;

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

        return view('accounts.snapshots', compact(
            'account',
            'currentSnapshot',
            'changes',
            'chartData',
            'statistics',
            'days'
        ));
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
