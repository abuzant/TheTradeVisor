<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class QueryLoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only log slow queries in production
        if (app()->environment('production')) {
            DB::listen(function ($query) {
                // Log queries that take longer than 1 second (1000ms)
                if ($query->time > 1000) {
                    $this->logSlowQuery($query);
                }
            });
        }
    }

    /**
     * Log slow query to dedicated file
     */
    private function logSlowQuery($query)
    {
        $logFile = '/var/log/thetradevisor/laravel_slow_queries.log';
        
        // Ensure directory exists
        $directory = dirname($logFile);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Format the log entry
        $logEntry = sprintf(
            "[%s] SLOW QUERY (%s ms)\nSQL: %s\nBindings: %s\n\n",
            now()->format('Y-m-d H:i:s'),
            number_format($query->time, 2),
            $query->sql,
            json_encode($query->bindings)
        );

        // Append to log file
        File::append($logFile, $logEntry);

        // Also log to Laravel log for alerting
        Log::warning('Slow database query detected', [
            'time' => $query->time . 'ms',
            'sql' => $query->sql,
            'bindings' => $query->bindings,
        ]);
    }
}
