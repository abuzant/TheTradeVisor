<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class MonitoringAlertService
{
    private $settings;
    private $lastAlertCheck;

    public function __construct()
    {
        $this->settings = $this->getAlertSettings();
        $this->lastAlertCheck = Redis::get('monitoring:last_alert_check') ?? now()->subHour();
    }

    /**
     * Check all monitoring metrics and send alerts if needed
     */
    public function checkAndSendAlerts()
    {
        $metrics = $this->collectMetrics();
        $alerts = $this->evaluateAlerts($metrics);
        
        if (!empty($alerts)) {
            $this->sendAlerts($alerts);
        }

        // Update last check time
        Redis::set('monitoring:last_alert_check', now());
        
        return $alerts;
    }

    /**
     * Collect all monitoring metrics
     */
    private function collectMetrics()
    {
        return [
            'error_rate' => $this->getErrorRate(),
            'memory_usage' => $this->getRedisMemoryUsage(),
            'queue_wait_time' => $this->getQueueWaitTime(),
            'database_connections' => $this->getDatabaseConnections(),
            'disk_usage' => $this->getDiskUsage(),
            'system_load' => $this->getSystemLoad(),
            'failed_jobs' => $this->getFailedJobsCount(),
        ];
    }

    /**
     * Evaluate metrics against thresholds and generate alerts
     */
    private function evaluateAlerts($metrics)
    {
        $alerts = [];

        // Check error rate
        if ($metrics['error_rate'] > $this->settings['error_rate_threshold']) {
            $alerts[] = [
                'type' => 'error_rate',
                'severity' => $metrics['error_rate'] > $this->settings['error_rate_threshold'] * 2 ? 'critical' : 'warning',
                'message' => "Error rate ({$metrics['error_rate']}%) exceeds threshold ({$this->settings['error_rate_threshold']}%)",
                'metric_value' => $metrics['error_rate'],
                'threshold' => $this->settings['error_rate_threshold'],
            ];
        }

        // Check Redis memory usage
        if ($metrics['memory_usage'] > $this->settings['memory_threshold']) {
            $alerts[] = [
                'type' => 'memory',
                'severity' => $metrics['memory_usage'] > 90 ? 'critical' : 'warning',
                'message' => "Redis memory usage ({$metrics['memory_usage']}%) exceeds threshold ({$this->settings['memory_threshold']}%)",
                'metric_value' => $metrics['memory_usage'],
                'threshold' => $this->settings['memory_threshold'],
            ];
        }

        // Check queue wait time
        if ($metrics['queue_wait_time'] > $this->settings['queue_wait_threshold']) {
            $alerts[] = [
                'type' => 'queue',
                'severity' => $metrics['queue_wait_time'] > $this->settings['queue_wait_threshold'] * 2 ? 'critical' : 'warning',
                'message' => "Queue wait time ({$metrics['queue_wait_time']}s) exceeds threshold ({$this->settings['queue_wait_threshold']}s)",
                'metric_value' => $metrics['queue_wait_time'],
                'threshold' => $this->settings['queue_wait_threshold'],
            ];
        }

        // Check database connections
        if ($metrics['database_connections'] > 80) {
            $alerts[] = [
                'type' => 'database',
                'severity' => $metrics['database_connections'] > 90 ? 'critical' : 'warning',
                'message' => "Database connections ({$metrics['database_connections']}) are critically high",
                'metric_value' => $metrics['database_connections'],
                'threshold' => 80,
            ];
        }

        // Check disk usage
        if ($metrics['disk_usage'] > 85) {
            $alerts[] = [
                'type' => 'disk',
                'severity' => $metrics['disk_usage'] > 95 ? 'critical' : 'warning',
                'message' => "Disk usage ({$metrics['disk_usage']}%) is critically high",
                'metric_value' => $metrics['disk_usage'],
                'threshold' => 85,
            ];
        }

        // Check system load
        $load = sys_getloadavg();
        if ($load[0] > 2.0) {
            $alerts[] = [
                'type' => 'system_load',
                'severity' => $load[0] > 4.0 ? 'critical' : 'warning',
                'message' => "System load ({$load[0]}) is critically high",
                'metric_value' => $load[0],
                'threshold' => 2.0,
            ];
        }

        // Check failed jobs
        if ($metrics['failed_jobs'] > $this->settings['failed_jobs_threshold']) {
            $alerts[] = [
                'type' => 'failed_jobs',
                'severity' => $metrics['failed_jobs'] > $this->settings['failed_jobs_threshold'] * 3 ? 'critical' : 'warning',
                'message' => "Failed jobs ({$metrics['failed_jobs']}) exceeds threshold ({$this->settings['failed_jobs_threshold']}) in the last hour",
                'metric_value' => $metrics['failed_jobs'],
                'threshold' => $this->settings['failed_jobs_threshold'],
            ];
        }

        return $alerts;
    }

    /**
     * Send alerts through configured channels
     */
    private function sendAlerts($alerts)
    {
        foreach ($alerts as $alert) {
            // Check if we should send this alert (avoid spam)
            if ($this->shouldSendAlert($alert)) {
                $this->sendNotification($alert);
                $this->logAlert($alert);
            }
        }
    }

    /**
     * Send notification based on configured channel
     */
    private function sendNotification($alert)
    {
        $alertChannel = $this->settings['alert_channel'] ?? 'email';
        
        switch ($alertChannel) {
            case 'slack':
                $this->sendSlackAlert($alert);
                break;
            case 'none':
                // Fallback to support email
                $this->sendEmailAlert($alert, config('monitoring.support_email'));
                break;
            case 'email':
            default:
                // Use alert email or fallback to support email
                $recipient = config('monitoring.alert_email', config('monitoring.support_email'));
                $this->sendEmailAlert($alert, $recipient);
                break;
        }
    }

    /**
     * Send email alert
     */
    private function sendEmailAlert($alert, $recipient = null)
    {
        if (!$this->settings['email_notifications']) {
            return;
        }

        try {
            $recipient = $recipient ?? config('monitoring.alert_email', 'hello@thetradevisor.com');
            
            Mail::raw($this->formatAlertEmail($alert), function ($message) use ($alert, $recipient) {
                $message->to($recipient)
                    ->subject("TheTradeVisor Alert: {$alert['type']}")
                    ->from('alerts@thetradevisor.com', 'TheTradeVisor Monitoring');
            });

            Log::info("Alert email sent for {$alert['type']} to {$recipient}");
        } catch (\Exception $e) {
            Log::error("Failed to send alert email: " . $e->getMessage());
        }
    }

    /**
     * Send Slack alert
     */
    private function sendSlackAlert($alert)
    {
        if (!$this->settings['slack_notifications']) {
            return;
        }

        try {
            $webhookUrl = config('monitoring.slack_webhook_url');
            
            if (!$webhookUrl) {
                Log::warning("Slack webhook URL not configured, falling back to email");
                $this->sendEmailAlert($alert, config('monitoring.alert_email'));
                return;
            }

            $payload = [
                'text' => "🚨 TheTradeVisor Alert: {$alert['type']}",
                'attachments' => [
                    [
                        'color' => $alert['severity'] === 'critical' ? 'danger' : 'warning',
                        'fields' => [
                            [
                                'title' => 'Type',
                                'value' => ucfirst(str_replace('_', ' ', $alert['type'])),
                                'short' => true
                            ],
                            [
                                'title' => 'Severity',
                                'value' => ucfirst($alert['severity']),
                                'short' => true
                            ],
                            [
                                'title' => 'Message',
                                'value' => $alert['message'],
                                'short' => false
                            ],
                            [
                                'title' => 'Time',
                                'value' => $alert['timestamp']->format('Y-m-d H:i:s'),
                                'short' => true
                            ]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($webhookUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                Log::info("Alert sent to Slack for {$alert['type']}");
            } else {
                Log::error("Failed to send Slack alert. HTTP code: {$httpCode}, Response: {$response}");
                // Fallback to email
                $this->sendEmailAlert($alert, config('monitoring.alert_email'));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send Slack alert: " . $e->getMessage());
            // Fallback to email
            $this->sendEmailAlert($alert, config('monitoring.alert_email'));
        }
    }

    /**
     * Format alert email content
     */
    private function formatAlertEmail($alert)
    {
        $severity = strtoupper($alert['severity']);
        $time = now()->format('Y-m-d H:i:s');
        
        return "
TheTradeVisor System Alert
==========================

Severity: {$severity}
Type: {$alert['type']}
Time: {$time}

Message: {$alert['message']}

Current Value: {$alert['metric_value']}
Threshold: {$alert['threshold']}

This is an automated alert from TheTradeVisor monitoring system.
Please check the monitoring dashboard for more details.

--
TheTradeVisor Monitoring Team
        ";
    }

    /**
     * Log alert to database
     */
    private function logAlert($alert)
    {
        $alertData = [
            'type' => $alert['type'],
            'severity' => $alert['severity'],
            'message' => $alert['message'],
            'metric_value' => $alert['metric_value'],
            'threshold' => $alert['threshold'],
            'created_at' => now(),
        ];

        // Store in Redis for quick access
        $key = "monitoring:alerts:" . now()->format('Y-m-d');
        Redis::lpush($key, json_encode($alertData));
        Redis::expire($key, 86400 * 30); // Keep for 30 days

        // Also log to Laravel log
        Log::warning("Monitoring Alert: {$alert['type']} - {$alert['message']}");
    }

    /**
     * Check if we should send this alert (avoid spam)
     */
    private function shouldSendAlert($alert)
    {
        $key = "monitoring:alert_sent:{$alert['type']}";
        $lastSent = Redis::get($key);
        
        if ($lastSent) {
            $lastSentTime = Carbon::parse($lastSent);
            
            // Don't send same alert type within 15 minutes for warnings, 5 minutes for critical
            $cooldown = $alert['severity'] === 'critical' ? 5 : 15;
            
            if ($lastSentTime->diffInMinutes(now()) < $cooldown) {
                return false;
            }
        }

        // Mark as sent
        Redis::set($key, now());
        Redis::expire($key, 3600); // Expire in 1 hour
        
        return true;
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
            'failed_jobs_threshold' => (int) (Redis::get('monitoring:failed_jobs_threshold') ?? 5),
            'email_notifications' => (bool) (Redis::get('monitoring:email_notifications') ?? true),
            'slack_notifications' => (bool) (Redis::get('monitoring:slack_notifications') ?? true),
            'alert_channel' => Redis::get('monitoring:alert_channel') ?? $defaultChannel,
        ];
    }

    /**
     * Helper methods to collect metrics
     */
    private function getErrorRate()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return 0;
        }

        $oneHourAgo = now()->subHour();
        $errorCount = 0;
        $totalLines = 0;
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recentLines = array_slice($lines, -1000); // Last 1000 lines
        
        foreach ($recentLines as $line) {
            $totalLines++;
            if (strpos($line, 'ERROR') !== false) {
                try {
                    $timestamp = $this->extractLogTimestamp($line);
                    if ($timestamp && $timestamp >= $oneHourAgo) {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    // Skip malformed lines
                }
            }
        }
        
        return $totalLines > 0 ? round(($errorCount / $totalLines) * 100, 2) : 0;
    }

    private function getRedisMemoryUsage()
    {
        try {
            $info = Redis::info();
            $used = $info['used_memory'];
            $max = $info['maxmemory'];
            return round(($used / $max) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getQueueWaitTime()
    {
        try {
            $stats = Redis::hgetall('horizon:stats');
            // This is a simplified calculation - you'd need to implement proper queue wait time tracking
            return 5.2; // Placeholder
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getDatabaseConnections()
    {
        try {
            $count = \DB::select("SELECT count(*) as count FROM pg_stat_activity")[0]->count;
            return (int) $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getDiskUsage()
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        return round((($total - $free) / $total) * 100, 2);
    }

    private function getSystemLoad()
    {
        $load = sys_getloadavg();
        return $load[0] ?? 0;
    }

    private function getFailedJobsCount()
    {
        try {
            // Check if Horizon failed_jobs table exists
            if (!\Schema::hasTable('failed_jobs')) {
                return 0;
            }

            $oneHourAgo = now()->subHour();
            
            // Count failed jobs in the last hour, focusing on critical data processing jobs
            $criticalJobs = [
                'App\\Jobs\\ProcessTradingData',
                'App\\Jobs\\ProcessHistoricalData',
            ];

            $count = \DB::table('failed_jobs')
                ->whereIn('queue', ['default', 'historical'])
                ->where('failed_at', '>=', $oneHourAgo)
                ->where(function($query) use ($criticalJobs) {
                    $query->where('exception', 'like', '%ProcessTradingData%')
                          ->orWhere('exception', 'like', '%ProcessHistoricalData%')
                          ->orWhere('payload', 'like', '%ProcessTradingData%')
                          ->orWhere('payload', 'like', '%ProcessHistoricalData%');
                })
                ->count();

            Log::debug('Failed jobs check', [
                'count' => $count,
                'since' => $oneHourAgo->toDateTimeString(),
            ]);

            return $count;
        } catch (\Exception $e) {
            Log::warning('Failed to check failed jobs count: ' . $e->getMessage());
            return 0;
        }
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
