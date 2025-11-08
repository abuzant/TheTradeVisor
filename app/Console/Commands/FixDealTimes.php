<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use Carbon\Carbon;

class FixDealTimes extends Command
{
    protected $signature = 'deals:fix-times';
    protected $description = 'Fix NULL time values in deals by converting from time_msc (milliseconds since epoch)';

    public function handle()
    {
        $this->info('Finding deals with NULL time but valid time_msc...');
        
        $dealsToFix = Deal::whereNull('time')
            ->whereNotNull('time_msc')
            ->where('time_msc', '>', 0)
            ->get();
        
        if ($dealsToFix->isEmpty()) {
            $this->info('No deals found that need fixing.');
            return 0;
        }
        
        $this->info("Found {$dealsToFix->count()} deals to fix");
        
        $bar = $this->output->createProgressBar($dealsToFix->count());
        $bar->start();
        
        $fixed = 0;
        $failed = 0;
        
        foreach ($dealsToFix as $deal) {
            try {
                // Convert milliseconds to seconds and create Carbon instance
                $timestamp = $deal->time_msc / 1000;
                $carbonTime = Carbon::createFromTimestamp($timestamp);
                
                // Update the deal
                $deal->time = $carbonTime;
                $deal->save();
                
                $fixed++;
            } catch (\Exception $e) {
                $this->error("\nFailed to fix deal {$deal->ticket}: " . $e->getMessage());
                $failed++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Fixed: {$fixed} deals");
        if ($failed > 0) {
            $this->error("✗ Failed: {$failed} deals");
        }
        
        // Verify
        $remainingNull = Deal::whereNull('time')->count();
        $this->info("Remaining deals with NULL time: {$remainingNull}");
        
        return 0;
    }
}
