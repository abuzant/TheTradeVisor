<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\AccountSnapshot;
use Illuminate\Support\Facades\Gate;

class AccountSnapshotController extends Controller
{
    /**
     * Get snapshots for a specific account
     * GET /api/accounts/{account}/snapshots
     */
    public function accountSnapshots(Request $request, TradingAccount $account)
    {
        // Authorization: User can only view their own account snapshots
        $user = $request->input('authenticated_user') ?? $request->user();
        if (!$user || $account->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'interval' => 'nullable|in:raw,hourly,daily',
            'limit' => 'nullable|integer|min:1|max:10000'
        ]);

        $query = AccountSnapshot::where('trading_account_id', $account->id);

        // Apply date filters
        if ($validated['from'] ?? null) {
            $query->where('snapshot_time', '>=', $validated['from']);
        }

        if ($validated['to'] ?? null) {
            $query->where('snapshot_time', '<=', $validated['to']);
        }

        // Get snapshots
        $snapshots = $query->orderBy('snapshot_time', 'desc')
                          ->limit($validated['limit'] ?? 1000)
                          ->get();

        // Apply aggregation if requested
        if (($validated['interval'] ?? 'raw') !== 'raw') {
            $snapshots = $this->aggregateSnapshots($snapshots, $validated['interval']);
        }

        return response()->json([
            'account_id' => $account->id,
            'account_number' => $account->account_number,
            'currency' => $account->account_currency,
            'count' => $snapshots->count(),
            'snapshots' => $snapshots
        ]);
    }

    /**
     * Get snapshots for all user's accounts
     * GET /api/users/me/snapshots
     */
    public function userSnapshots(Request $request)
    {
        $user = $request->input('authenticated_user') ?? $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'interval' => 'nullable|in:raw,hourly,daily',
            'limit' => 'nullable|integer|min:1|max:10000'
        ]);

        $query = AccountSnapshot::where('user_id', $user->id);

        // Apply date filters
        if ($validated['from'] ?? null) {
            $query->where('snapshot_time', '>=', $validated['from']);
        }

        if ($validated['to'] ?? null) {
            $query->where('snapshot_time', '<=', $validated['to']);
        }

        // Get snapshots
        $snapshots = $query->orderBy('snapshot_time', 'desc')
                          ->limit($validated['limit'] ?? 1000)
                          ->get();

        // Apply aggregation if requested
        if (($validated['interval'] ?? 'raw') !== 'raw') {
            $snapshots = $this->aggregateSnapshots($snapshots, $validated['interval']);
        }

        return response()->json([
            'user_id' => $user->id,
            'count' => $snapshots->count(),
            'snapshots' => $snapshots
        ]);
    }

    /**
     * Export snapshots as CSV
     * GET /api/accounts/{account}/snapshots/export
     */
    public function export(Request $request, TradingAccount $account)
    {
        // Authorization
        $user = $request->input('authenticated_user') ?? $request->user();
        if (!$user || $account->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $query = AccountSnapshot::where('trading_account_id', $account->id)
                               ->orderBy('snapshot_time', 'asc');

        if ($validated['from'] ?? null) {
            $query->where('snapshot_time', '>=', $validated['from']);
        }

        if ($validated['to'] ?? null) {
            $query->where('snapshot_time', '<=', $validated['to']);
        }

        $snapshots = $query->get();

        $csv = "Timestamp,Balance,Equity,Margin,Free_Margin,Margin_Level,Profit\n";

        foreach ($snapshots as $snap) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $snap->snapshot_time,
                $snap->balance,
                $snap->equity,
                $snap->margin,
                $snap->free_margin,
                $snap->margin_level ?? 0,
                $snap->profit
            );
        }

        $filename = 'account_' . $account->account_number . '_snapshots_' . date('Y-m-d') . '.csv';

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get aggregated statistics
     * GET /api/accounts/{account}/snapshots/stats
     */
    public function stats(Request $request, TradingAccount $account)
    {
        // Authorization - check if account belongs to the authenticated user
        $user = $request->input('authenticated_user') ?? $request->user();
        if (!$user || $account->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $days = $request->input('days', 30);
        $from = now()->subDays($days);

        $snapshots = AccountSnapshot::where('trading_account_id', $account->id)
                                   ->where('snapshot_time', '>=', $from)
                                   ->orderBy('snapshot_time', 'asc')
                                   ->get();

        if ($snapshots->isEmpty()) {
            return response()->json([
                'error' => 'No snapshots found for the specified period'
            ], 404);
        }

        $maxDrawdown = $this->calculateMaxDrawdown($snapshots);

        return response()->json([
            'period_days' => $days,
            'total_snapshots' => $snapshots->count(),
            'balance' => [
                'current' => $snapshots->last()->balance ?? 0,
                'highest' => $snapshots->max('balance'),
                'lowest' => $snapshots->min('balance'),
                'average' => round($snapshots->avg('balance'), 2),
            ],
            'equity' => [
                'current' => $snapshots->last()->equity ?? 0,
                'highest' => $snapshots->max('equity'),
                'lowest' => $snapshots->min('equity'),
                'average' => round($snapshots->avg('equity'), 2),
                'max_drawdown' => $maxDrawdown,
            ],
            'margin' => [
                'current' => $snapshots->last()->margin ?? 0,
                'highest' => $snapshots->max('margin'),
                'average' => round($snapshots->avg('margin'), 2),
            ],
            'profit' => [
                'current' => $snapshots->last()->profit ?? 0,
                'highest' => $snapshots->max('profit'),
                'lowest' => $snapshots->min('profit'),
            ],
        ]);
    }

    /**
     * Aggregate snapshots to hourly or daily
     */
    protected function aggregateSnapshots($snapshots, $interval)
    {
        $format = $interval === 'hourly' ? 'Y-m-d H:00:00' : 'Y-m-d';

        return $snapshots->groupBy(function($snap) use ($format) {
            return $snap->snapshot_time->format($format);
        })->map(function($group) {
            return $group->last(); // Take last snapshot of each period
        })->values();
    }

    /**
     * Calculate maximum drawdown
     */
    protected function calculateMaxDrawdown($snapshots)
    {
        if ($snapshots->isEmpty()) {
            return 0;
        }

        $peak = $snapshots->first()->equity;
        $maxDrawdown = 0;

        foreach ($snapshots as $snap) {
            if ($snap->equity > $peak) {
                $peak = $snap->equity;
            }

            $drawdown = (($peak - $snap->equity) / $peak) * 100;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }

        return round($maxDrawdown, 2);
    }
}
