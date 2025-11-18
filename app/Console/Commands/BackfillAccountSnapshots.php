<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\TradingAccount;
use App\Models\AccountSnapshot;
use Carbon\Carbon;

class BackfillAccountSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshots:backfill {--user_id=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill account snapshots from historical JSON files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be written');
        }

        $this->info('🚀 Starting account snapshots backfill...');

        // Get all JSON files from raw_data storage
        $disk = Storage::disk('trading_data');
        $allFiles = $disk->allFiles();

        // Filter JSON files
        $jsonFiles = array_filter($allFiles, function($file) use ($userId) {
            if (!str_ends_with($file, '.json')) {
                return false;
            }
            
            // If user_id specified, only process that user's files
            if ($userId) {
                return str_starts_with($file, $userId . '/');
            }
            
            return true;
        });

        $this->info('📁 Found ' . count($jsonFiles) . ' JSON files to process');

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($jsonFiles));
        $progressBar->start();

        foreach ($jsonFiles as $file) {
            try {
                $content = $disk->get($file);
                $data = json_decode($content, true);

                if (!$data || !isset($data['account'])) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Extract account info
                $accountData = $data['account'];
                $accountNumber = $accountData['account_number'] ?? null;
                $brokerServer = $accountData['server'] ?? null;
                $fileUserId = $data['user_id'] ?? null;

                if (!$brokerServer || !$fileUserId) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Find trading account
                $tradingAccount = TradingAccount::where('user_id', $fileUserId)
                    ->where('broker_server', $brokerServer)
                    ->when($accountNumber, function($q) use ($accountNumber) {
                        $q->where('account_number', $accountNumber);
                    })
                    ->first();

                if (!$tradingAccount) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Determine snapshot time
                $isHistorical = $data['meta']['is_historical'] ?? false;
                $snapshotTime = null;

                if ($isHistorical && isset($data['meta']['history_date'])) {
                    // Historical data - use end of day
                    // Convert dot format to dash format (2025.11.08 -> 2025-11-08)
                    $dateStr = str_replace('.', '-', $data['meta']['history_date']);
                    $snapshotTime = Carbon::parse($dateStr)->setTime(23, 59, 59);
                } elseif (isset($data['meta']['timestamp'])) {
                    // Current data - use timestamp from meta
                    // Convert dot format to dash format (2025.11.08 08:29:59 -> 2025-11-08 08:29:59)
                    $timestampStr = str_replace('.', '-', $data['meta']['timestamp']);
                    $snapshotTime = Carbon::parse($timestampStr);
                } elseif (isset($data['received_at'])) {
                    // Fallback to received_at
                    $timestampStr = str_replace('.', '-', $data['received_at']);
                    $snapshotTime = Carbon::parse($timestampStr);
                } else {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Check if snapshot already exists
                $exists = AccountSnapshot::where('trading_account_id', $tradingAccount->id)
                    ->where('snapshot_time', $snapshotTime)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Create snapshot
                if (!$dryRun) {
                    AccountSnapshot::create([
                        'user_id' => $tradingAccount->user_id,
                        'trading_account_id' => $tradingAccount->id,
                        'balance' => $accountData['balance'] ?? 0,
                        'equity' => $accountData['equity'] ?? 0,
                        'margin' => $accountData['margin'] ?? 0,
                        'free_margin' => $accountData['free_margin'] ?? 0,
                        'margin_level' => $accountData['margin_level'] ?? null,
                        'profit' => $accountData['profit'] ?? 0,
                        'snapshot_time' => $snapshotTime,
                        'is_historical' => $isHistorical,
                        'source' => 'backfill',
                    ]);
                }

                $processed++;
                $progressBar->advance();

            } catch (\Exception $e) {
                $errors++;
                $progressBar->advance();
                $this->error("\n❌ Error processing file {$file}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('✅ Backfill complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files', count($jsonFiles)],
                ['Processed', $processed],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );

        return Command::SUCCESS;
    }
}
