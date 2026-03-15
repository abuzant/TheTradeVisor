<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BrokerAnalyticsService;
use App\Services\CurrencyService;
use App\Models\SymbolMapping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WarmAnalyticsCache extends Command
{
    protected $signature = 'analytics:warm-cache
                            {--flush : Clear existing cache before warming}
                            {--days=* : Specific day periods to warm (default: 7,30)}';

    protected $description = 'Pre-warm analytics caches so first page loads are instant';

    public function handle()
    {
        $this->info('Starting analytics cache warming...');
        $startTime = microtime(true);

        if ($this->option('flush')) {
            $this->warn('Flushing existing cache...');
            Cache::flush();
        }

        // Preload static caches
        CurrencyService::preloadRates();
        SymbolMapping::preloadMappings();

        $dayPeriods = $this->option('days');
        if (empty($dayPeriods)) {
            $dayPeriods = [7, 30];
        }

        // Warm broker analytics
        $brokerService = app(BrokerAnalyticsService::class);
        foreach ($dayPeriods as $days) {
            $this->info("  Warming broker analytics ({$days} days)...");
            $start = microtime(true);
            try {
                $result = $brokerService->getBrokerComparison((int) $days, 'USD');
                $elapsed = round(microtime(true) - $start, 2);
                $brokerCount = count($result['brokers'] ?? []);
                $this->info("    ✓ {$brokerCount} brokers in {$elapsed}s");
            } catch (\Throwable $e) {
                $this->error("    ✗ Failed: " . $e->getMessage());
                Log::error('Cache warming failed for broker analytics', [
                    'days' => $days,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Warm global analytics
        foreach ($dayPeriods as $days) {
            $this->info("  Warming global analytics ({$days} days)...");
            $start = microtime(true);
            $cacheKey = "global_analytics_{$days}";
            try {
                // Check if already cached
                if (Cache::has($cacheKey)) {
                    $this->info("    ✓ Already cached, skipping");
                    continue;
                }

                $controller = app(\App\Http\Controllers\AnalyticsController::class);
                $method = new \ReflectionMethod($controller, 'getAnalyticsData');
                $method->setAccessible(true);
                $result = $method->invoke($controller, (int) $days);
                $elapsed = round(microtime(true) - $start, 2);
                $keyCount = is_array($result) ? count($result) : 0;
                $this->info("    ✓ {$keyCount} analytics keys in {$elapsed}s");
            } catch (\Throwable $e) {
                $this->error("    ✗ Failed: " . $e->getMessage());
                Log::error('Cache warming failed for global analytics', [
                    'days' => $days,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $totalTime = round(microtime(true) - $startTime, 2);
        $memoryMB = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $this->info("Cache warming completed in {$totalTime}s (peak memory: {$memoryMB}MB)");

        Log::info('Analytics cache warming completed', [
            'total_time' => $totalTime,
            'memory_mb' => $memoryMB,
            'periods' => $dayPeriods,
        ]);

        return Command::SUCCESS;
    }
}
