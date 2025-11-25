<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackfillSession;
use Illuminate\Support\Facades\Log;

class MonitorBackfillProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backfill:monitor {--status=} {--priority=} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor backfill session progress and status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->option('status');
        $priority = $this->option('priority');
        $userId = $this->option('user_id');

        $this->info('📊 Backfill Session Monitor');

        // Build query
        $query = BackfillSession::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sessions = $query->orderBy('created_at', 'desc')->get();

        if ($sessions->isEmpty()) {
            $this->info('✅ No backfill sessions found matching criteria');
            return Command::SUCCESS;
        }

        // Display summary
        $summary = [
            'Total Sessions' => $sessions->count(),
            'Pending' => $sessions->where('status', 'pending')->count(),
            'Running' => $sessions->where('status', 'running')->count(),
            'Completed' => $sessions->where('status', 'completed')->count(),
            'Failed' => $sessions->where('status', 'failed')->count(),
            'Critical Priority' => $sessions->where('priority', 'critical')->count(),
        ];

        $this->table(['Metric', 'Count'], array_map(fn($k, $v) => [$k, $v], array_keys($summary), $summary));

        // Display sessions
        $this->newLine();
        $this->info('📋 Session Details:');

        $sessionData = [];
        foreach ($sessions as $session) {
            $accountInfo = $session->tradingAccount 
                ? ($session->tradingAccount->account_number ?: $session->tradingAccount->account_hash)
                : 'N/A';
                
            $sessionData[] = [
                'ID' => substr($session->session_id, 0, 8) . '...',
                'User' => $session->user->email,
                'Account' => $accountInfo,
                'Priority' => $session->priority,
                'Status' => $session->status,
                'Progress' => $session->completion_percentage . '%',
                'Files' => $session->files_processed . '/' . $session->total_files_to_process,
                'Snapshots' => $session->snapshots_created,
                'Errors' => $session->errors_count,
                'Duration' => $session->getHumanDuration(),
                'Created' => $session->created_at->diffForHumans(),
            ];
        }

        $this->table(
            ['ID', 'User', 'Account', 'Priority', 'Status', 'Progress', 'Files', 'Snapshots', 'Errors', 'Duration', 'Created'],
            $sessionData
        );

        // Show active sessions details
        $activeSessions = $sessions->whereIn('status', ['pending', 'running']);
        if ($activeSessions->isNotEmpty()) {
            $this->newLine();
            $this->warn('⚠️  Active Sessions (' . $activeSessions->count() . '):');
            
            foreach ($activeSessions as $session) {
                $this->line("  • {$session->session_id} - {$session->status} ({$session->completion_percentage}% complete)");
                
                if ($session->isRunning()) {
                    $elapsed = $session->started_at ? $session->started_at->diffForHumans(null, true) : 'N/A';
                    $this->line("    Running for: {$elapsed}");
                }
                
                if ($session->failed_files && count($session->failed_files) > 0) {
                    $this->line("    Failed files: " . count($session->failed_files));
                }
            }
        }

        // Show failed sessions details
        $failedSessions = $sessions->where('status', 'failed');
        if ($failedSessions->isNotEmpty()) {
            $this->newLine();
            $this->error('❌ Failed Sessions (' . $failedSessions->count() . '):');
            
            foreach ($failedSessions as $session) {
                $this->line("  • {$session->session_id}: {$session->error_message}");
                if ($session->failed_files && count($session->failed_files) > 0) {
                    $this->line("    Failed files: " . count($session->failed_files));
                }
            }
        }

        // Performance metrics
        $completedSessions = $sessions->where('status', 'completed');
        if ($completedSessions->isNotEmpty()) {
            $this->newLine();
            $this->info('📈 Performance Metrics:');
            
            $totalFiles = $completedSessions->sum('total_files_to_process');
            $totalSnapshots = $completedSessions->sum('snapshots_created');
            $avgDuration = $completedSessions->avg('duration_seconds');
            $totalDuration = $completedSessions->sum('duration_seconds');

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Files Processed', number_format($totalFiles)],
                    ['Total Snapshots Created', number_format($totalSnapshots)],
                    ['Average Duration', $this->formatDuration($avgDuration)],
                    ['Total Processing Time', $this->formatDuration($totalDuration)],
                    ['Files per Second', $avgDuration > 0 ? round($totalFiles / $avgDuration, 2) : 'N/A'],
                    ['Success Rate', round(($completedSessions->count() / $sessions->count()) * 100, 1) . '%'],
                ]
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Format duration for display
     */
    private function formatDuration($seconds)
    {
        if (!$seconds) {
            return 'N/A';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        } else {
            return sprintf('%ds', $secs);
        }
    }
}
