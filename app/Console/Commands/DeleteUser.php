<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TradingAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete {email} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a user and all related data (accounts, trades, files)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $force = $this->option('force');
        
        // Find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }
        
        $this->info("Found user: {$user->name} ({$user->email})");
        $this->info("User ID: {$user->id}");
        $this->newLine();
        
        // Get related data counts
        $accountsCount = $user->tradingAccounts()->count();
        
        $this->warn("This will delete:");
        $this->line("  - User account: {$user->name} ({$user->email})");
        $this->line("  - Trading accounts: {$accountsCount}");
        $this->line("  - All deals, positions, and orders");
        $this->line("  - All raw data files in storage");
        $this->line("  - All API request logs");
        $this->newLine();
        
        if (!$force) {
            if (!$this->confirm('Are you sure you want to delete this user and ALL related data?', false)) {
                $this->info('Deletion cancelled.');
                return 0;
            }
            
            if (!$this->confirm('This action cannot be undone. Are you REALLY sure?', false)) {
                $this->info('Deletion cancelled.');
                return 0;
            }
        }
        
        $this->newLine();
        $this->info('Starting deletion process...');
        $this->newLine();
        
        DB::beginTransaction();
        
        try {
            // 1. Delete trading accounts and related data
            $this->info("Step 1: Deleting trading accounts and related data...");
            $accounts = $user->tradingAccounts;
            
            foreach ($accounts as $account) {
                $this->line("  - Deleting account: {$account->account_number} ({$account->broker})");
                
                // Delete deals
                $dealsCount = DB::table('deals')->where('trading_account_id', $account->id)->count();
                DB::table('deals')->where('trading_account_id', $account->id)->delete();
                $this->line("    ✓ Deleted {$dealsCount} deals");
                
                // Delete positions
                $positionsCount = DB::table('positions')->where('trading_account_id', $account->id)->count();
                DB::table('positions')->where('trading_account_id', $account->id)->delete();
                $this->line("    ✓ Deleted {$positionsCount} positions");
                
                // Delete orders
                $ordersCount = DB::table('orders')->where('trading_account_id', $account->id)->count();
                DB::table('orders')->where('trading_account_id', $account->id)->delete();
                $this->line("    ✓ Deleted {$ordersCount} orders");
                
                // Delete account
                $account->delete();
                $this->line("    ✓ Deleted trading account");
            }
            
            $this->newLine();
            
            // 2. Delete API request logs
            $this->info("Step 2: Deleting API request logs...");
            $logsCount = DB::table('api_request_logs')->where('user_id', $user->id)->count();
            DB::table('api_request_logs')->where('user_id', $user->id)->delete();
            $this->line("  ✓ Deleted {$logsCount} API request logs");
            
            $this->newLine();
            
            // 3. Delete storage files
            $this->info("Step 3: Deleting storage files...");
            $userStoragePath = "{$user->id}";
            
            if (Storage::disk('trading_data')->exists($userStoragePath)) {
                $files = Storage::disk('trading_data')->allFiles($userStoragePath);
                $filesCount = count($files);
                
                Storage::disk('trading_data')->deleteDirectory($userStoragePath);
                $this->line("  ✓ Deleted {$filesCount} files from storage/{$userStoragePath}");
            } else {
                $this->line("  - No storage files found");
            }
            
            $this->newLine();
            
            // 4. Delete user
            $this->info("Step 4: Deleting user account...");
            $user->delete();
            $this->line("  ✓ Deleted user: {$email}");
            
            DB::commit();
            
            $this->newLine();
            $this->info('✅ User and all related data deleted successfully!');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->newLine();
            $this->error('❌ Error during deletion: ' . $e->getMessage());
            $this->error('Transaction rolled back. No data was deleted.');
            
            return 1;
        }
    }
}
