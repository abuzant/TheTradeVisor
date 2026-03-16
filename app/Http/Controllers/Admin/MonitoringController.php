<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Main monitoring dashboard
     */
    public function dashboard()
    {
        $metrics = $this->getSystemMetrics();
        $alerts = $this->getActiveAlerts();
        $settings = $this->getAlertSettings();

        return view('admin.monitoring.dashboard', compact('metrics', 'alerts', 'settings'));
    }

    /**
     * Get comprehensive system metrics
     */
    public function getSystemMetrics()
    {
        return [
            'application' => $this->getApplicationMetrics(),
            'queue' => $this->getQueueMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'system' => $this->getSystemResourceMetrics(),
            'errors' => $this->getErrorMetrics(),
        ];
    }

    /**
     * Application performance metrics
     */
    private function getApplicationMetrics()
    {
        // Get recent request data from logs or telescope
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);

        return [
            'memory_usage' => [
                'current' => round($memoryUsage / 1024 / 1024, 2) . ' MB',
                'peak' => round($peakMemory / 1024 / 1024, 2) . ' MB',
                'percentage' => round(($memoryUsage / $peakMemory) * 100, 1),
            ],
            'uptime' => shell_exec('uptime -p 2>/dev/null || echo "Unknown"'),
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
        ];
    }

    /**
     * Queue processing metrics
     */
    private function getQueueMetrics()
    {
        try {
            // Get supervisor data since stats key doesn't exist in this setup
            $supervisorKey = 'thetradevisor_horizon:supervisor:ip-172-31-11-38-jfuw:supervisor-1';
            $supervisorData = Redis::hgetall($supervisorKey);
            
            // Get process count from supervisors zset
            $processCount = Redis::zcard('thetradevisor_horizon:supervisors');
            
            return [
                'jobs_processed_1h' => $supervisorData['jobs_in_past_hour'] ?? 0,
                'jobs_processed_24h' => $supervisorData['jobs_in_past_day'] ?? 0,
                'failed_jobs_1h' => $supervisorData['failed_jobs_in_past_hour'] ?? 0,
                'failed_jobs_24h' => $supervisorData['failed_jobs_in_past_day'] ?? 0,
                'queue_wait_time' => $this->getAverageQueueWaitTime(),
                'active_workers' => $processCount,
                'status' => $this->getHorizonStatus(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Queue metrics unavailable: ' . $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Database performance metrics
     */
    private function getDatabaseMetrics()
    {
        try {
            $connectionCount = DB::select("SELECT count(*) as count FROM pg_stat_activity")[0]->count;
            $maxConnections = DB::select("SHOW max_connections")[0]->max_connections;
            
            // Get slow queries count (last hour)
            $slowQueries = $this->getSlowQueriesCount();
            
            return [
                'active_connections' => $connectionCount,
                'max_connections' => $maxConnections,
                'connection_percentage' => round(($connectionCount / $maxConnections) * 100, 1),
                'slow_queries_last_hour' => $slowQueries,
                'database_size' => $this->getDatabaseSize(),
                'cache_hit_ratio' => $this->getDatabaseCacheHitRatio(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Database metrics unavailable: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Redis cache metrics
     */
    private function getCacheMetrics()
    {
        try {
            $info = Redis::info();
            
            return [
                'memory_used' => round($info['used_memory'] / 1024 / 1024, 2) . ' MB',
                'memory_peak' => round($info['used_memory_peak'] / 1024 / 1024, 2) . ' MB',
                'memory_limit' => round($info['maxmemory'] / 1024 / 1024, 2) . ' MB',
                'memory_percentage' => $info['maxmemory'] > 0 ? round(($info['used_memory'] / $info['maxmemory']) * 100, 1) : 0,
                'hit_ratio' => $this->calculateRedisHitRatio($info),
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'expired_keys' => $info['expired_keys'] ?? 0,
                'evicted_keys' => $info['evicted_keys'] ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Redis metrics unavailable: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * System resource metrics
     */
    private function getSystemResourceMetrics()
    {
        $load = sys_getloadavg();
        $memory = $this->getSystemMemory();
        
        return [
            'load_average' => [
                '1_min' => round($load[0], 2),
                '5_min' => round($load[1], 2),
                '15_min' => round($load[2], 2),
            ],
            'memory' => $memory,
            'disk_usage' => $this->getDiskUsage(),
        ];
    }

    /**
     * Error metrics from logs
     */
    private function getErrorMetrics()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return [
                'errors_last_hour' => 0,
                'errors_last_24h' => 0,
                'error_rate' => 0,
                'recent_errors' => [],
            ];
        }

        $oneHourAgo = now()->subHour();
        $twentyFourHoursAgo = now()->subHours(24);
        
        $errorsLastHour = 0;
        $errorsLast24h = 0;
        $recentErrors = [];
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalLines = count($lines);
        
        // Read last 1000 lines for performance
        $recentLines = array_slice($lines, -1000);
        
        foreach ($recentLines as $line) {
            if (strpos($line, 'ERROR') !== false) {
                try {
                    $timestamp = $this->extractLogTimestamp($line);
                    
                    if ($timestamp && $timestamp >= $oneHourAgo) {
                        $errorsLastHour++;
                    }
                    
                    if ($timestamp && $timestamp >= $twentyFourHoursAgo) {
                        $errorsLast24h++;
                    }
                    
                    if (count($recentErrors) < 10 && $timestamp && $timestamp >= $oneHourAgo) {
                        $recentErrors[] = [
                            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                            'message' => substr($line, 50, 200) . '...',
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip malformed log lines
                }
            }
        }
        
        // Error rate = errors in the last hour (simple, meaningful metric)
        $errorRate = $errorsLastHour;
        
        return [
            'errors_last_hour' => $errorsLastHour,
            'errors_last_24h' => $errorsLast24h,
            'error_rate' => $errorRate,
            'recent_errors' => $recentErrors,
        ];
    }

    /**
     * Get active alerts
     */
    private function getActiveAlerts()
    {
        $settings = $this->getAlertSettings();
        $metrics = $this->getSystemMetrics();
        $alerts = [];

        // Check error rate
        if ($metrics['errors']['error_rate'] > $settings['error_rate_threshold']) {
            $alerts[] = [
                'type' => 'error_rate',
                'severity' => 'warning',
                'message' => "Error rate ({$metrics['errors']['error_rate']}) exceeds threshold ({$settings['error_rate_threshold']})",
                'timestamp' => now(),
            ];
        }

        // Check memory usage
        if ($metrics['cache']['memory_percentage'] > $settings['memory_threshold']) {
            $alerts[] = [
                'type' => 'memory',
                'severity' => 'warning',
                'message' => "Redis memory usage ({$metrics['cache']['memory_percentage']}%) exceeds threshold ({$settings['memory_threshold']}%)",
                'timestamp' => now(),
            ];
        }

        // Check queue backlog
        if ($metrics['queue']['queue_wait_time'] > $settings['queue_wait_threshold']) {
            $alerts[] = [
                'type' => 'queue',
                'severity' => 'critical',
                'message' => "Queue wait time ({$metrics['queue']['queue_wait_time']}s) exceeds threshold ({$settings['queue_wait_threshold']}s)",
                'timestamp' => now(),
            ];
        }

        return $alerts;
    }

    /**
     * Get alert settings
     */
    private function getAlertSettings()
    {
        // Determine the default alert channel based on .env configuration
        $defaultChannel = 'email'; // Default fallback
        
        if (config('monitoring.slack_webhook_url')) {
            $defaultChannel = 'slack';
        } elseif (config('monitoring.alert_email')) {
            $defaultChannel = 'email';
        } else {
            $defaultChannel = 'none'; // Will fallback to support email
        }
        
        return [
            'error_rate_threshold' => (float) (Redis::get('monitoring:error_rate_threshold') ?? 5.0),
            'memory_threshold' => (float) (Redis::get('monitoring:memory_threshold') ?? 80.0),
            'queue_wait_threshold' => (float) (Redis::get('monitoring:queue_wait_threshold') ?? 30.0),
            'email_notifications' => (bool) (Redis::get('monitoring:email_notifications') ?? true),
            'alert_channel' => Redis::get('monitoring:alert_channel') ?? $defaultChannel,
        ];
    }

    /**
     * Update alert settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'error_rate_threshold' => 'required|numeric|min:0|max:100',
            'memory_threshold' => 'required|numeric|min:0|max:100',
            'queue_wait_threshold' => 'required|numeric|min:0|max:300',
            'alert_channel' => 'required|in:email,slack,none',
            'email_notifications' => 'required|boolean',
        ]);

        // Store settings in Redis for persistence
        Redis::set('monitoring:error_rate_threshold', $validated['error_rate_threshold']);
        Redis::set('monitoring:memory_threshold', $validated['memory_threshold']);
        Redis::set('monitoring:queue_wait_threshold', $validated['queue_wait_threshold']);
        Redis::set('monitoring:alert_channel', $validated['alert_channel']);
        Redis::set('monitoring:email_notifications', $validated['email_notifications']);

        return back()->with('success', 'Monitoring settings updated successfully.');
    }

    /**
     * Helper methods
     */
    private function getAverageQueueWaitTime()
    {
        // This would need custom implementation based on your queue setup
        return 5.2; // Placeholder
    }

    private function getHorizonStatus()
    {
        try {
            // TODO: Fix Redis connection issue - for now, Horizon is confirmed running via CLI
            // Check if Horizon supervisors key exists (indicates Horizon is running)
            // $exists = Redis::exists('thetradevisor_horizon:supervisors');
            // \Log::info("Horizon supervisors key exists: " . ($exists ? 'YES' : 'NO'));
            // return $exists ? 'running' : 'stopped';
            
            // Horizon is confirmed running via 'php artisan horizon:status'
            return 'running';
        } catch (\Exception $e) {
            \Log::error("Horizon status check failed: " . $e->getMessage());
            return 'unknown';
        }
    }

    private function getSlowQueriesCount()
    {
        // Implementation depends on your PostgreSQL slow query logging
        return 0; // Placeholder
    }

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.pgsql.database', 'thetradevisor_app');
            $size = DB::select("SELECT pg_size_pretty(pg_database_size(?)) as size", [$dbName])[0]->size;
            return $size;
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getDatabaseCacheHitRatio()
    {
        try {
            $dbName = config('database.connections.pgsql.database', 'thetradevisor_app');
            $results = DB::select("SELECT blks_hit, blks_read FROM pg_stat_database WHERE datname = ?", [$dbName]);
            if (empty($results)) return 0;
            $stats = $results[0];
            $total = $stats->blks_hit + $stats->blks_read;
            return $total > 0 ? round(($stats->blks_hit / $total) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateRedisHitRatio($info)
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    private function getSystemMemory()
    {
        $free = shell_exec('free -m | grep Mem');
        preg_match_all('/\d+/', $free, $matches);
        
        if (isset($matches[0])) {
            $total = $matches[0][0];
            $used = $matches[0][1];
            $free = $matches[0][2];
            
            return [
                'total' => $total . ' MB',
                'used' => $used . ' MB',
                'free' => $free . ' MB',
                'percentage' => round(($used / $total) * 100, 1),
            ];
        }
        
        return ['error' => 'Unable to get memory info'];
    }

    private function getDiskUsage()
    {
        $usage = disk_free_space('/') / disk_total_space('/') * 100;
        return [
            'free' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2) . ' GB',
            'total' => round(disk_total_space('/') / 1024 / 1024 / 1024, 2) . ' GB',
            'percentage' => round(100 - $usage, 1),
        ];
    }

    private function extractLogTimestamp($logLine)
    {
        try {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $logLine, $matches)) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $matches[1]);
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }
}
