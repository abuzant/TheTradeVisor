<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountSnapshot;
use Illuminate\Support\Facades\Log;

class CleanupOldSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshots:cleanup {--days=180} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete account snapshots older than specified days (default: 180 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be deleted');
        }

        $this->info("🚀 Cleaning up snapshots older than {$days} days...");
        $this->info("📅 Cutoff date: {$cutoffDate->toDateTimeString()}");
        $this->newLine();

        $query = AccountSnapshot::where('snapshot_time', '<', $cutoffDate);

        if ($dryRun) {
            $count = $query->count();
            $this->info("Would delete {$count} snapshots");
            
            if ($count > 0) {
                $this->newLine();
                $this->info('Breakdown by user:');
                $breakdown = AccountSnapshot::where('snapshot_time', '<', $cutoffDate)
                    ->selectRaw('user_id, COUNT(*) as count')
                    ->groupBy('user_id')
                    ->get();
                
                foreach ($breakdown as $item) {
                    $this->info("  User {$item->user_id}: {$item->count} snapshots");
                }
            }
            
            return Command::SUCCESS;
        }

        // Actual deletion
        $deleted = $query->delete();
        
        $this->info("✅ Deleted {$deleted} snapshots older than {$days} days");
        
        // Log the cleanup
        Log::info('Account snapshots cleanup completed', [
            'deleted_count' => $deleted,
            'days' => $days,
            'cutoff_date' => $cutoffDate->toDateTimeString(),
        ]);

        return Command::SUCCESS;
    }
}
