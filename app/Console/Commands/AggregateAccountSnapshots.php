<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountSnapshot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AggregateAccountSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshots:aggregate {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate old account snapshots to reduce storage (keep hourly for 31-90 days, daily for 91-180 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be deleted');
        }

        $this->info('🚀 Starting account snapshots aggregation...');
        $this->newLine();

        // Strategy:
        // 0-30 days: Keep ALL snapshots (no aggregation)
        // 31-90 days: Keep 1 per hour (aggregate rest)
        // 91-180 days: Keep 1 per day (aggregate rest)

        $totalDeleted = 0;

        // Aggregate to hourly (31-90 days)
        $this->info('📊 Aggregating 31-90 day old snapshots to hourly...');
        $hourlyDeleted = $this->aggregateToHourly(31, 90, $dryRun);
        $totalDeleted += $hourlyDeleted;
        $this->info("   Deleted: {$hourlyDeleted} snapshots");
        $this->newLine();

        // Aggregate to daily (91-180 days)
        $this->info('📊 Aggregating 91-180 day old snapshots to daily...');
        $dailyDeleted = $this->aggregateToDaily(91, 180, $dryRun);
        $totalDeleted += $dailyDeleted;
        $this->info("   Deleted: {$dailyDeleted} snapshots");
        $this->newLine();

        $this->info("✅ Aggregation complete! Total deleted: {$totalDeleted} snapshots");

        return Command::SUCCESS;
    }

    /**
     * Aggregate snapshots to hourly (keep last snapshot of each hour)
     */
    protected function aggregateToHourly($startDay, $endDay, $dryRun = false)
    {
        $start = now()->subDays($endDay);
        $end = now()->subDays($startDay);

        // For each trading account and each hour, keep only the LAST snapshot
        // Delete all others in that hour
        $query = "
            DELETE FROM account_snapshots
            WHERE id IN (
                SELECT id FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (
                               PARTITION BY trading_account_id, 
                                           DATE_TRUNC('hour', snapshot_time)
                               ORDER BY snapshot_time DESC
                           ) as rn
                    FROM account_snapshots
                    WHERE snapshot_time BETWEEN ? AND ?
                ) sub
                WHERE rn > 1
            )
        ";

        if ($dryRun) {
            // Count how many would be deleted
            $countQuery = "
                SELECT COUNT(*) as count FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (
                               PARTITION BY trading_account_id, 
                                           DATE_TRUNC('hour', snapshot_time)
                               ORDER BY snapshot_time DESC
                           ) as rn
                    FROM account_snapshots
                    WHERE snapshot_time BETWEEN ? AND ?
                ) sub
                WHERE rn > 1
            ";
            $result = DB::select($countQuery, [$start, $end]);
            return $result[0]->count ?? 0;
        }

        return DB::delete($query, [$start, $end]);
    }

    /**
     * Aggregate snapshots to daily (keep last snapshot of each day)
     */
    protected function aggregateToDaily($startDay, $endDay, $dryRun = false)
    {
        $start = now()->subDays($endDay);
        $end = now()->subDays($startDay);

        // For each trading account and each day, keep only the LAST snapshot
        // Delete all others in that day
        $query = "
            DELETE FROM account_snapshots
            WHERE id IN (
                SELECT id FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (
                               PARTITION BY trading_account_id, 
                                           DATE_TRUNC('day', snapshot_time)
                               ORDER BY snapshot_time DESC
                           ) as rn
                    FROM account_snapshots
                    WHERE snapshot_time BETWEEN ? AND ?
                ) sub
                WHERE rn > 1
            )
        ";

        if ($dryRun) {
            // Count how many would be deleted
            $countQuery = "
                SELECT COUNT(*) as count FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (
                               PARTITION BY trading_account_id, 
                                           DATE_TRUNC('day', snapshot_time)
                               ORDER BY snapshot_time DESC
                           ) as rn
                    FROM account_snapshots
                    WHERE snapshot_time BETWEEN ? AND ?
                ) sub
                WHERE rn > 1
            ";
            $result = DB::select($countQuery, [$start, $end]);
            return $result[0]->count ?? 0;
        }

        return DB::delete($query, [$start, $end]);
    }
}
