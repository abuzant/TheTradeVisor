<?php

namespace App\Console\Commands;

use App\Models\TradingAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupInactiveAccounts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accounts:cleanup-inactive
                            {--days=180 : Number of days of inactivity before deletion}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Delete trading accounts and their data after specified days of inactivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $this->info("🔍 Searching for accounts inactive for more than {$days} days...");
        
        // Calculate the cutoff date
        $cutoffDate = now()->subDays($days);
        
        // Find inactive accounts
        $inactiveAccounts = TradingAccount::where(function ($query) use ($cutoffDate) {
            $query->where('last_sync_at', '<', $cutoffDate)
                  ->orWhereNull('last_sync_at');
        })
        ->where('created_at', '<', $cutoffDate) // Also check creation date
        ->with('user')
        ->get();
        
        if ($inactiveAccounts->isEmpty()) {
            $this->info('✅ No inactive accounts found.');
            return 0;
        }
        
        $this->warn("Found {$inactiveAccounts->count()} inactive accounts:");
        
        // Display accounts to be deleted
        $table = [];
        foreach ($inactiveAccounts as $account) {
            $lastSync = $account->last_sync_at 
                ? $account->last_sync_at->format('Y-m-d H:i:s') 
                : 'Never';
            
            $table[] = [
                'ID' => $account->id,
                'User' => $account->user->email ?? 'Unknown',
                'Broker' => $account->broker_name,
                'Account' => $account->account_number ?? $account->account_hash,
                'Last Sync' => $lastSync,
                'Created' => $account->created_at->format('Y-m-d'),
            ];
        }
        
        $this->table(
            ['ID', 'User', 'Broker', 'Account', 'Last Sync', 'Created'],
            $table
        );
        
        if ($dryRun) {
            $this->warn('🔸 DRY RUN - No data will be deleted');
            $this->info('Run without --dry-run to actually delete these accounts');
            return 0;
        }
        
        // Confirm deletion
        if (!$this->confirm('Do you want to delete these accounts and all their data?', false)) {
            $this->info('❌ Deletion cancelled');
            return 0;
        }
        
        // Delete accounts and their related data
        $deletedCount = 0;
        $errors = [];
        
        foreach ($inactiveAccounts as $account) {
            DB::beginTransaction();
            
            try {
                $accountId = $account->id;
                $userEmail = $account->user->email ?? 'Unknown';
                $accountNumber = $account->account_number ?? $account->account_hash;
                
                // Delete related data (cascade will handle most, but let's be explicit)
                $positionsDeleted = $account->positions()->delete();
                $dealsDeleted = $account->deals()->delete();
                $ordersDeleted = $account->orders()->delete();
                
                // Delete history upload progress if exists
                if ($account->historyUploadProgress) {
                    $account->historyUploadProgress->delete();
                }
                
                // Delete the account itself
                $account->delete();
                
                DB::commit();
                
                $deletedCount++;
                
                // Log the deletion
                Log::info('Inactive account deleted', [
                    'account_id' => $accountId,
                    'user_email' => $userEmail,
                    'account_number' => $accountNumber,
                    'broker' => $account->broker_name,
                    'last_sync' => $account->last_sync_at,
                    'positions_deleted' => $positionsDeleted,
                    'deals_deleted' => $dealsDeleted,
                    'orders_deleted' => $ordersDeleted,
                    'days_inactive' => $days,
                ]);
                
                $this->info("✅ Deleted account #{$accountId} ({$userEmail} - {$accountNumber})");
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                $error = "Failed to delete account #{$account->id}: {$e->getMessage()}";
                $errors[] = $error;
                
                Log::error('Failed to delete inactive account', [
                    'account_id' => $account->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                $this->error("❌ {$error}");
            }
        }
        
        // Summary
        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("   Total found: {$inactiveAccounts->count()}");
        $this->info("   Successfully deleted: {$deletedCount}");
        
        if (!empty($errors)) {
            $this->error("   Failed: " . count($errors));
            $this->newLine();
            $this->error("Errors:");
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }
        
        $this->newLine();
        $this->info('✅ Cleanup completed');
        
        return 0;
    }
}
