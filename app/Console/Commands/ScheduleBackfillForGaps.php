<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\BackfillSession;

class ScheduleBackfillForGaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaps:backfill {--report=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule smart backfill for detected data gaps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reportFile = $this->option('report');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No backfill sessions will be created');
        }

        if (!$reportFile) {
            // Use the latest report
            $reports = Storage::disk('local')->files('gap_reports');
            if (empty($reports)) {
                $this->error('❌ No gap reports found. Run gaps:detect first.');
                return Command::FAILURE;
            }
            
            $reportFile = last($reports);
            $this->info("📄 Using latest report: {$reportFile}");
        }

        $this->info("🚀 Creating smart backfill sessions for gaps in: {$reportFile}");

        // Load gap report
        $reportContent = Storage::disk('local')->get($reportFile);
        $gapReport = json_decode($reportContent, true);

        if (empty($gapReport)) {
            $this->info("✅ No gaps to backfill");
            return Command::SUCCESS;
        }

        $totalGaps = 0;
        $criticalGaps = 0;
        $sessionsCreated = 0;
        $totalFilesToProcess = 0;

        foreach ($gapReport as $accountReport) {
            $accountId = $accountReport['account_id'];
            $accountNumber = $accountReport['account_number'];
            $userId = $this->getUserIdByAccountId($accountId);
            
            if (!$userId) {
                $this->warn("⚠️  Could not find user for account {$accountNumber}");
                continue;
            }

            foreach ($accountReport['gaps'] as $gap) {
                $totalGaps++;
                
                if ($gap['severity'] === 'critical') {
                    $criticalGaps++;
                }

                $this->line("Creating session for account {$accountNumber}: {$gap['duration_hours']}h gap");

                if (!$dryRun) {
                    $session = $this->createBackfillSession($userId, $accountId, $gap, $accountReport);
                    
                    if ($session) {
                        $sessionsCreated++;
                        $totalFilesToProcess += $session->total_files_to_process;
                        
                        $this->line("  ✅ Created session {$session->session_id} ({$session->total_files_to_process} files)");
                    }
                }
            }
        }

        // Display summary
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Gaps Found', $totalGaps],
                ['Critical Gaps', $criticalGaps],
                ['Sessions Created', $dryRun ? 0 : $sessionsCreated],
                ['Total Files to Process', $totalFilesToProcess],
            ]
        );

        if (!$dryRun && $sessionsCreated > 0) {
            $this->newLine();
            $this->info("🔄 Smart backfill sessions created. Use 'php artisan backfill:smart --session=<id>' to process.");
            
            // Trigger processing for critical gaps immediately
            $criticalSessions = BackfillSession::where('priority', 'critical')
                ->where('status', 'pending')
                ->limit(3) // Process max 3 critical sessions at once
                ->get();

            if ($criticalSessions->isNotEmpty()) {
                $this->info("🚨 Processing {$criticalSessions->count()} critical sessions immediately...");
                
                foreach ($criticalSessions as $session) {
                    $this->call('backfill:smart', [
                        '--session' => $session->session_id
                    ]);
                }
            }
            
            Log::info('Smart backfill sessions created for gaps', [
                'sessions_created' => $sessionsCreated,
                'total_gaps' => $totalGaps,
                'critical_gaps' => $criticalGaps,
                'total_files' => $totalFilesToProcess,
                'report_file' => $reportFile,
            ]);
        } else {
            $this->info("🔍 DRY RUN: Would create {$totalGaps} backfill sessions");
        }

        return Command::SUCCESS;
    }

    /**
     * Create backfill session for a specific gap
     */
    private function createBackfillSession($userId, $accountId, $gap, $accountReport)
    {
        try {
            // Determine priority based on gap severity
            $priority = $gap['severity'] === 'critical' ? 'critical' : 'normal';
            
            // Calculate time range for backfill
            $startTime = Carbon::parse($gap['start_time']);
            $endTime = Carbon::parse($gap['end_time']);
            
            // Estimate files to process (rough calculation)
            $durationHours = $gap['duration_hours'];
            $estimatedFiles = intval($durationHours * 12); // 12 files per hour estimate
            
            $session = BackfillSession::create([
                'user_id' => $userId,
                'trading_account_id' => $accountId,
                'trigger_type' => 'gap_detection',
                'priority' => $priority,
                'status' => 'pending',
                'gap_start_time' => $startTime,
                'gap_end_time' => $endTime,
                'total_files_to_process' => $estimatedFiles,
                'estimated_missing_snapshots' => $gap['missing_snapshots'],
                'metadata' => [
                    'account_number' => $accountReport['account_number'],
                    'broker' => $accountReport['broker'],
                    'gap_duration_hours' => $gap['duration_hours'],
                    'gap_severity' => $gap['severity'],
                    'missing_snapshots' => $gap['missing_snapshots'],
                    'original_gap_start' => $gap['start_time'],
                    'original_gap_end' => $gap['end_time'],
                ],
                'notes' => "Auto-created session for {$gap['duration_hours']}h data gap ({$gap['severity']})",
            ]);

            return $session;
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to create session for account {$accountId}: " . $e->getMessage());
            Log::error('Failed to create backfill session for gap', [
                'account_id' => $accountId,
                'gap' => $gap,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get user ID by account ID
     */
    private function getUserIdByAccountId($accountId)
    {
        try {
            $account = \App\Models\TradingAccount::find($accountId);
            return $account ? $account->user_id : null;
        } catch (\Exception $e) {
            Log::error('Failed to get user by account ID', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
