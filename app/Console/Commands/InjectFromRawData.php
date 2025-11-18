<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessTradingData;
use App\Jobs\ProcessHistoricalData;

class InjectFromRawData extends Command
{
    protected $signature = 'data:inject {--user_id=}';
    protected $description = 'Inject all data from raw_data JSON files';

    public function handle()
    {
        $userId = $this->option('user_id');
        
        $this->error('🚨 DATA INJECTION FROM RAW JSON FILES');
        $this->info('This will process ALL JSON files and restore your data.');
        
        $disk = Storage::disk('trading_data');
        $allFiles = $disk->allFiles();
        
        // Filter JSON files
        $jsonFiles = array_filter($allFiles, function($file) use ($userId) {
            if (!str_ends_with($file, '.json')) {
                return false;
            }
            if ($userId) {
                return str_starts_with($file, $userId . '/');
            }
            return true;
        });
        
        $this->info('📁 Found ' . count($jsonFiles) . ' JSON files to process');
        $this->info('🚀 Starting injection...');
        
        $progressBar = $this->output->createProgressBar(count($jsonFiles));
        $progressBar->start();
        
        $processed = 0;
        $errors = 0;
        
        foreach ($jsonFiles as $file) {
            try {
                $content = $disk->get($file);
                $data = json_decode($content, true);
                
                if (!$data || !isset($data['account'])) {
                    $progressBar->advance();
                    continue;
                }
                
                // Determine if historical or current
                $isHistorical = $data['meta']['is_historical'] ?? false;
                
                // Dispatch job synchronously for faster processing
                if ($isHistorical) {
                    ProcessHistoricalData::dispatchSync($data, $file);
                } else {
                    ProcessTradingData::dispatchSync($data, $file);
                }
                
                $processed++;
                $progressBar->advance();
                
            } catch (\Exception $e) {
                $errors++;
                $progressBar->advance();
                $this->error("\n❌ Error: " . $e->getMessage());
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info('✅ Injection complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files', count($jsonFiles)],
                ['Processed', $processed],
                ['Errors', $errors],
            ]
        );
        
        // Show final stats
        $this->info("\n📊 Database Stats:");
        $this->table(
            ['Table', 'Count'],
            [
                ['Users', \App\Models\User::count()],
                ['Trading Accounts', \App\Models\TradingAccount::count()],
                ['Positions', \App\Models\Position::count()],
                ['Deals', \App\Models\Deal::count()],
                ['Orders', \App\Models\Order::count()],
            ]
        );
        
        return Command::SUCCESS;
    }
}
