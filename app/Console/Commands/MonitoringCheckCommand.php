<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonitoringAlertService;
use ReflectionClass;

class MonitoringCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check system metrics and send alerts if needed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting monitoring check...');
        
        try {
            $alertService = new MonitoringAlertService();
            $alerts = $alertService->checkAndSendAlerts();
            
            // Get metrics for display
            $metrics = $this->getMetricsForDisplay($alertService);
            
            $this->info('📊 System Metrics:');
            $this->line("  • Error Rate: {$metrics['error_rate']}%");
            $this->line("  • Memory Usage: {$metrics['memory_usage']}%");
            $this->line("  • Queue Wait: {$metrics['queue_wait_time']}s");
            $this->line("  • DB Connections: {$metrics['database_connections']}");
            $this->line("  • Disk Usage: {$metrics['disk_usage']}%");
            $this->line("  • System Load: {$metrics['system_load']}");
            $this->line("  • Failed Jobs (1h): {$metrics['failed_jobs']}");
            
            if (empty($alerts)) {
                $this->info('✅ All systems normal - no alerts generated');
            } else {
                $this->warn('⚠️  ' . count($alerts) . ' alert(s) generated:');
                
                foreach ($alerts as $alert) {
                    $this->line("  • [{$alert['severity']}] {$alert['type']}: {$alert['message']}");
                }
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Monitoring check failed: ' . $e->getMessage());
            \Log::error('Monitoring command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getMetricsForDisplay($alertService)
    {
        // Use reflection to access private method
        $reflection = new ReflectionClass($alertService);
        $method = $reflection->getMethod('collectMetrics');
        $method->setAccessible(true);
        
        return $method->invoke($alertService);
    }
}
