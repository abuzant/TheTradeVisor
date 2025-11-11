<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\Position;

class DetectAccountPlatforms extends Command
{
    protected $signature = 'accounts:detect-platforms';
    protected $description = 'Detect and update platform type for all trading accounts';

    public function handle()
    {
        $accounts = TradingAccount::whereNull('platform_type')->get();
        
        $this->info("Found {$accounts->count()} accounts without platform detection.");
        
        foreach ($accounts as $account) {
            $this->info("Checking account #{$account->id} - {$account->account_number}...");
            
            // Check deals for position_id (MT5 indicator)
            $dealWithPositionId = Deal::where('trading_account_id', $account->id)
                ->whereNotNull('position_id')
                ->first();
            
            if ($dealWithPositionId) {
                // MT5 detected
                $this->info("  → MT5 detected (has position_id in deals)");
                
                // Determine mode by checking if multiple positions per symbol exist
                $symbolsWithMultiplePositions = Position::where('trading_account_id', $account->id)
                    ->where('is_open', true)
                    ->select('symbol', \DB::raw('COUNT(*) as position_count'))
                    ->groupBy('symbol')
                    ->havingRaw('COUNT(*) > 1')
                    ->count();
                
                $mode = $symbolsWithMultiplePositions > 0 ? 'hedging' : 'netting';
                
                $account->update([
                    'platform_type' => 'MT5',
                    'account_mode' => $mode,
                    'platform_detected_at' => now()
                ]);
                
                $this->info("  ✓ Updated to MT5 ({$mode})");
            } else {
                // MT4 (no position_id field)
                $this->info("  → MT4 detected (no position_id in deals)");
                
                $account->update([
                    'platform_type' => 'MT4',
                    'account_mode' => 'hedging', // MT4 is always hedging
                    'platform_detected_at' => now()
                ]);
                
                $this->info("  ✓ Updated to MT4 (hedging)");
            }
        }
        
        $this->info("\nPlatform detection complete!");
        
        return 0;
    }
}
