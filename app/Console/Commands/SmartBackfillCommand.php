<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackfillSession;
use App\Models\TradingAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmartBackfillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backfill:smart {--session=} {--user_id=} {--account_id=} {--start_time=} {--end_time=} {--priority=normal} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Smart backfill with time range targeting and progress tracking';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionId = $this->option('session');
        $userId = $this->option('user_id');
        $accountId = $this->option('account_id');
        $startTime = $this->option('start_time');
        $endTime = $this->option('end_time');
        $priority = $this->option('priority');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No actual backfill will be performed');
        }

        if ($sessionId) {
            // Process existing session
            return $this->processSession($sessionId, $dryRun);
        } elseif ($userId) {
            // Create new session for user
            return $this->createAndProcessSession($userId, $accountId, $startTime, $endTime, $priority, $dryRun);
        } else {
            $this->error('❌ Either --session or --user_id must be provided');
            return Command::FAILURE;
        }
    }

    /**
     * Process an existing backfill session
     */
    private function processSession($sessionId, $dryRun)
    {
        $session = BackfillSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            $this->error("❌ Session {$sessionId} not found");
            return Command::FAILURE;
        }

        if ($session->isRunning()) {
            $this->warn("⚠️  Session {$sessionId} is already running");
            return Command::SUCCESS;
        }

        if ($session->isCompleted() || $session->isFailed()) {
            $this->warn("⚠️  Session {$sessionId} is already {$session->status}");
            return Command::SUCCESS;
        }

        $this->info("🚀 Processing backfill session: {$sessionId}");
        $this->info("📊 Priority: {$session->priority}, Status: {$session->status}");

        return $this->executeBackfill($session, $dryRun);
    }

    /**
     * Create and process a new backfill session
     */
    private function createAndProcessSession($userId, $accountId, $startTime, $endTime, $priority, $dryRun)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            $this->error("❌ User {$userId} not found");
            return Command::FAILURE;
        }

        $this->info("👤 Creating backfill session for user: {$user->email}");

        // Determine accounts to process
        if ($accountId) {
            $accounts = TradingAccount::where('id', $accountId)->where('user_id', $userId)->get();
        } else {
            $accounts = TradingAccount::where('user_id', $userId)->where('is_active', true)->get();
        }

        if ($accounts->isEmpty()) {
            $this->error("❌ No active accounts found for user {$userId}");
            return Command::FAILURE;
        }

        $totalSessions = 0;
        $totalFiles = 0;

        foreach ($accounts as $account) {
            // Count files to process
            $filesToProcess = $this->countFilesToProcess($account, $startTime, $endTime);
            
            if ($filesToProcess > 0) {
                $session = BackfillSession::create([
                    'user_id' => $userId,
                    'trading_account_id' => $account->id,
                    'trigger_type' => 'manual',
                    'priority' => $priority,
                    'status' => 'pending',
                    'gap_start_time' => $startTime ? Carbon::parse($startTime) : null,
                    'gap_end_time' => $endTime ? Carbon::parse($endTime) : null,
                    'total_files_to_process' => $filesToProcess,
                    'estimated_missing_snapshots' => $filesToProcess, // Rough estimate
                    'metadata' => [
                        'account_number' => $account->account_number ?: $account->account_hash,
                        'broker' => $account->broker_name,
                        'command_line' => implode(' ', $this->arguments()),
                        'options' => $this->options(),
                    ],
                ]);

                $this->info("📄 Created session {$session->session_id} for account " . ($account->account_number ?: $account->account_hash) . " ({$filesToProcess} files)");
                
                $totalSessions++;
                $totalFiles += $filesToProcess;
            }
        }

        if ($totalSessions === 0) {
            $this->info("✅ No files to backfill");
            return Command::SUCCESS;
        }

        $this->info("📊 Created {$totalSessions} sessions with {$totalFiles} total files");

        if (!$dryRun) {
            // Process sessions in priority order
            $sessions = BackfillSession::where('user_id', $userId)
                ->where('status', 'pending')
                ->byPriority()
                ->get();

            foreach ($sessions as $session) {
                $this->executeBackfill($session, false);
            }
        } else {
            $this->info("🔍 DRY RUN: Would process {$totalSessions} sessions");
        }

        return Command::SUCCESS;
    }

    /**
     * Execute the actual backfill
     */
    private function executeBackfill($session, $dryRun)
    {
        $this->info("🔄 Starting backfill for session: {$session->session_id}");

        if (!$dryRun) {
            $session->start();
        }

        try {
            $account = $session->tradingAccount;
            $filesProcessed = 0;
            $snapshotsCreated = 0;
            $errorsCount = 0;

            // Get files to process
            $files = $this->getFilesToProcess($account, $session->gap_start_time, $session->gap_end_time);
            
            $this->info("📁 Found {$files->count()} files to process");

            $progressBar = $this->output->createProgressBar($files->count());
            $progressBar->start();

            foreach ($files as $file) {
                try {
                    if (!$dryRun) {
                        $result = $this->processFile($session, $file);
                        
                        if ($result['success']) {
                            $snapshotsCreated += $result['snapshots_created'];
                        } else {
                            $session->addFailedFile($file, $result['error']);
                            $errorsCount++;
                        }
                    }

                    $filesProcessed++;
                    
                    // Update progress every 10 files
                    if ($filesProcessed % 10 === 0) {
                        if (!$dryRun) {
                            $session->updateProgress($filesProcessed, 0, 0);
                        }
                    }

                    $progressBar->advance();

                } catch (\Exception $e) {
                    $errorsCount++;
                    $session->addFailedFile($file, $e->getMessage());
                    $this->newLine();
                    $this->warn("⚠️  Error processing file {$file}: " . $e->getMessage());
                }
            }

            $progressBar->finish();
            $this->newLine(2);

            if (!$dryRun) {
                $session->updateProgress($filesProcessed, $snapshotsCreated, $errorsCount);
                
                if ($errorsCount === 0) {
                    $session->complete($snapshotsCreated);
                    $this->info("✅ Session {$session->session_id} completed successfully");
                    $this->info("📊 Processed: {$filesProcessed} files, Created: {$snapshotsCreated} snapshots");
                } else {
                    $session->fail("Completed with {$errorsCount} errors");
                    $this->warn("⚠️  Session {$session->session_id} completed with {$errorsCount} errors");
                }
            } else {
                $this->info("🔍 DRY RUN: Would process {$filesProcessed} files");
            }

        } catch (\Exception $e) {
            if (!$dryRun) {
                $session->fail($e->getMessage());
            }
            $this->error("❌ Session {$session->session_id} failed: " . $e->getMessage());
            Log::error('Smart backfill session failed', [
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Get files to process for an account
     */
    private function getFilesToProcess($account, $startTime = null, $endTime = null)
    {
        $disk = Storage::disk('trading_data');
        $allFiles = $disk->allFiles($account->user_id);
        
        // Filter JSON files
        $jsonFiles = array_filter($allFiles, function($file) {
            return str_ends_with($file, '.json');
        });

        // Filter by time range if specified
        if ($startTime || $endTime) {
            $jsonFiles = array_filter($jsonFiles, function($file) use ($startTime, $endTime) {
                // Extract timestamp from filename
                if (preg_match('/(\d{4}-\d{2}-\d{2}_\d{6})/', $file, $matches)) {
                    $fileTime = Carbon::createFromFormat('Y-m-d_His', $matches[1]);
                    
                    if ($startTime && $fileTime->lt(Carbon::parse($startTime))) {
                        return false;
                    }
                    
                    if ($endTime && $fileTime->gt(Carbon::parse($endTime))) {
                        return false;
                    }
                    
                    return true;
                }
                
                return false; // Skip files without valid timestamps
            });
        }

        return collect($jsonFiles);
    }

    /**
     * Count files to process
     */
    private function countFilesToProcess($account, $startTime = null, $endTime = null)
    {
        return $this->getFilesToProcess($account, $startTime, $endTime)->count();
    }

    /**
     * Process a single file
     */
    private function processFile($session, $filename)
    {
        try {
            $disk = Storage::disk('trading_data');
            $content = $disk->get($filename);
            $data = json_decode($content, true);

            if (!$data || !isset($data['account'])) {
                return ['success' => false, 'error' => 'Invalid data structure'];
            }

            // Check if this is a gap that needs backfill
            $isHistorical = $data['meta']['is_historical'] ?? false;
            $snapshotTime = null;

            if ($isHistorical && isset($data['meta']['history_date'])) {
                $dateStr = str_replace('.', '-', $data['meta']['history_date']);
                $snapshotTime = Carbon::parse($dateStr)->setTime(23, 59, 59);
            } elseif (isset($data['meta']['timestamp'])) {
                $timestampStr = str_replace('.', '-', $data['meta']['timestamp']);
                $snapshotTime = Carbon::parse($timestampStr);
            }

            if (!$snapshotTime) {
                return ['success' => false, 'error' => 'No valid timestamp found'];
            }

            // Check if snapshot already exists
            $exists = \App\Models\AccountSnapshot::where('trading_account_id', $session->trading_account_id)
                ->where('snapshot_time', $snapshotTime)
                ->exists();

            if ($exists) {
                return ['success' => true, 'snapshots_created' => 0]; // Already exists
            }

            // Create snapshot
            $accountData = $data['account'];
            \App\Models\AccountSnapshot::create([
                'user_id' => $session->user_id,
                'trading_account_id' => $session->trading_account_id,
                'balance' => $accountData['balance'] ?? 0,
                'equity' => $accountData['equity'] ?? 0,
                'margin' => $accountData['margin'] ?? 0,
                'free_margin' => $accountData['free_margin'] ?? 0,
                'margin_level' => $accountData['margin_level'] ?? null,
                'profit' => $accountData['profit'] ?? 0,
                'snapshot_time' => $snapshotTime,
                'is_historical' => $isHistorical,
                'source' => 'smart_backfill',
            ]);

            return ['success' => true, 'snapshots_created' => 1];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
