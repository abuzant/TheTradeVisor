<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingAccount;
use App\Models\AccountSnapshot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DetectDataGaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaps:detect {--hours=24} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect missing account snapshots and data gaps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No backfill will be triggered');
        }

        $this->info("🚀 Starting gap detection for last {$hours} hours...");

        $gapsDetected = 0;
        $accountsChecked = 0;
        $criticalGaps = 0;

        // Get all active trading accounts
        $activeAccounts = TradingAccount::where('is_active', true)
            ->where('last_sync_at', '>=', now()->subHours($hours))
            ->get();

        $this->info("📊 Found {$activeAccounts->count()} active accounts to check");

        $progressBar = $this->output->createProgressBar($activeAccounts->count());
        $progressBar->start();

        $gapReport = [];

        foreach ($activeAccounts as $account) {
            $accountsChecked++;
            $accountGaps = $this->checkAccountGaps($account, $hours);
            
            if (!empty($accountGaps)) {
                $gapsDetected += count($accountGaps);
                
                // Check for critical gaps (>6 hours)
                foreach ($accountGaps as $gap) {
                    if ($gap['duration_hours'] > 6) {
                        $criticalGaps++;
                    }
                }

                $gapReport[] = [
                    'account_id' => $account->id,
                    'account_number' => $account->account_number ?? $account->account_hash,
                    'broker' => $account->broker_name,
                    'gaps' => $accountGaps,
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Store gap report for backfill command
        $reportKey = "gap_report_" . now()->format('Y-m-d_H-i-s');
        Storage::disk('local')->put("gap_reports/{$reportKey}.json", json_encode($gapReport, JSON_PRETTY_PRINT));

        // Display summary
        $this->table(
            ['Metric', 'Count'],
            [
                ['Accounts Checked', $accountsChecked],
                ['Gaps Detected', $gapsDetected],
                ['Critical Gaps (>6h)', $criticalGaps],
            ]
        );

        if (!empty($gapReport)) {
            $this->warn("⚠️  Data gaps found! Report saved to: gap_reports/{$reportKey}.json");
            
            // Show critical gaps
            foreach ($gapReport as $report) {
                foreach ($report['gaps'] as $gap) {
                    if ($gap['duration_hours'] > 6) {
                        $this->error("🚨 CRITICAL: Account {$report['account_number']} ({$report['broker']}) missing data for {$gap['duration_hours']}h");
                    }
                }
            }

            // Log to system
            Log::warning('Data gaps detected', [
                'total_gaps' => $gapsDetected,
                'critical_gaps' => $criticalGaps,
                'report_file' => $reportKey,
                'checked_hours' => $hours,
            ]);

            if (!$dryRun) {
                $this->info("🔄 Triggering backfill for detected gaps...");
                // This will be implemented in the next command
            }
        } else {
            $this->info("✅ No data gaps detected in the last {$hours} hours");
        }

        return Command::SUCCESS;
    }

    /**
     * Check for gaps in a specific account
     */
    private function checkAccountGaps($account, $hours)
    {
        $gaps = [];
        $since = now()->subHours($hours);
        
        // Get snapshots for this account in the time range
        $snapshots = AccountSnapshot::where('trading_account_id', $account->id)
            ->where('snapshot_time', '>=', $since)
            ->orderBy('snapshot_time', 'asc')
            ->get();

        if ($snapshots->isEmpty()) {
            // No snapshots at all - check if there should be any
            $expectedSnapshots = $this->getExpectedSnapshotCount($account, $hours);
            if ($expectedSnapshots > 0) {
                $gaps[] = [
                    'start_time' => $since->toDateTimeString(),
                    'end_time' => now()->toDateTimeString(),
                    'duration_hours' => $hours,
                    'missing_snapshots' => $expectedSnapshots,
                    'severity' => 'critical',
                ];
            }
            return $gaps;
        }

        // Check gaps between snapshots
        $previousTime = $since;
        
        foreach ($snapshots as $snapshot) {
            $gapHours = $previousTime->diffInHours($snapshot->snapshot_time);
            
            if ($gapHours > 1) { // Gap larger than 1 hour
                $expectedInGap = $this->getExpectedSnapshotCount($account, $gapHours);
                
                $gaps[] = [
                    'start_time' => $previousTime->toDateTimeString(),
                    'end_time' => $snapshot->snapshot_time->toDateTimeString(),
                    'duration_hours' => $gapHours,
                    'missing_snapshots' => $expectedInGap,
                    'severity' => $gapHours > 6 ? 'critical' : 'warning',
                ];
            }
            
            $previousTime = $snapshot->snapshot_time;
        }

        // Check gap from last snapshot to now
        $lastSnapshot = $snapshots->last();
        $gapHours = $lastSnapshot->snapshot_time->diffInHours(now());
        
        if ($gapHours > 1) {
            $expectedInGap = $this->getExpectedSnapshotCount($account, $gapHours);
            
            $gaps[] = [
                'start_time' => $lastSnapshot->snapshot_time->toDateTimeString(),
                'end_time' => now()->toDateTimeString(),
                'duration_hours' => $gapHours,
                'missing_snapshots' => $expectedInGap,
                'severity' => $gapHours > 6 ? 'critical' : 'warning',
            ];
        }

        return $gaps;
    }

    /**
     * Get expected snapshot count based on account activity
     */
    private function getExpectedSnapshotCount($account, $hours)
    {
        // Assume snapshots should be taken every 5 minutes during active hours
        // 12 snapshots per hour
        return intval($hours * 12);
    }
}
