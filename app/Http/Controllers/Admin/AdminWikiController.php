<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdminWikiController extends Controller
{
    public function index()
    {
        // Get all artisan commands
        $artisanCommands = $this->getArtisanCommands();
        
        // Get all scripts
        $scripts = $this->getScripts();
        
        // Get system information
        $systemInfo = $this->getSystemInfo();
        
        // Get scheduled tasks
        $scheduledTasks = $this->getScheduledTasks();
        
        // Get middleware information
        $middleware = $this->getMiddlewareInfo();
        
        // Get service information
        $services = $this->getServicesInfo();
        
        return view('admin.wiki.index', compact(
            'artisanCommands',
            'scripts',
            'systemInfo',
            'scheduledTasks',
            'middleware',
            'services'
        ));
    }
    
    private function getArtisanCommands()
    {
        return [
            'data_management' => [
                'title' => 'Data Management',
                'commands' => [
                    [
                        'name' => 'accounts:cleanup-inactive',
                        'description' => 'Delete trading accounts and their data after specified days of inactivity',
                        'usage' => 'php artisan accounts:cleanup-inactive [--days=180] [--dry-run]',
                        'options' => [
                            '--days=N' => 'Number of days of inactivity before deletion (default: 180)',
                            '--dry-run' => 'Preview what would be deleted without actually deleting',
                        ],
                        'examples' => [
                            'php artisan accounts:cleanup-inactive --dry-run' => 'Preview accounts that would be deleted',
                            'php artisan accounts:cleanup-inactive --days=90' => 'Delete accounts inactive for 90+ days',
                        ],
                        'schedule' => 'Daily at 3:00 AM',
                        'docs' => '/www/docs/INACTIVE_ACCOUNTS_CLEANUP.md',
                    ],
                    [
                        'name' => 'accounts:detect-platforms',
                        'description' => 'Detect and update platform type (MT4/MT5) for all trading accounts',
                        'usage' => 'php artisan accounts:detect-platforms',
                        'examples' => [
                            'php artisan accounts:detect-platforms' => 'Scan and update platform types for all accounts',
                        ],
                    ],
                ],
            ],
            'system_maintenance' => [
                'title' => 'System Maintenance',
                'commands' => [
                    [
                        'name' => 'geoip:update',
                        'description' => 'Download and update the GeoIP database from MaxMind',
                        'usage' => 'php artisan geoip:update',
                        'examples' => [
                            'php artisan geoip:update' => 'Update GeoIP database',
                        ],
                        'schedule' => 'Every 14 days at 2:00 AM',
                    ],
                    [
                        'name' => 'cache:clear',
                        'description' => 'Clear application cache',
                        'usage' => 'php artisan cache:clear',
                        'examples' => [
                            'php artisan cache:clear' => 'Clear all application cache',
                        ],
                    ],
                    [
                        'name' => 'config:clear',
                        'description' => 'Clear configuration cache',
                        'usage' => 'php artisan config:clear',
                        'examples' => [
                            'php artisan config:clear' => 'Clear configuration cache',
                        ],
                    ],
                    [
                        'name' => 'route:clear',
                        'description' => 'Clear route cache',
                        'usage' => 'php artisan route:clear',
                        'examples' => [
                            'php artisan route:clear' => 'Clear route cache',
                        ],
                    ],
                    [
                        'name' => 'view:clear',
                        'description' => 'Clear compiled view files',
                        'usage' => 'php artisan view:clear',
                        'examples' => [
                            'php artisan view:clear' => 'Clear compiled views',
                        ],
                    ],
                ],
            ],
            'queue_management' => [
                'title' => 'Queue Management',
                'commands' => [
                    [
                        'name' => 'horizon:terminate',
                        'description' => 'Terminate Horizon supervisor gracefully',
                        'usage' => 'php artisan horizon:terminate',
                        'examples' => [
                            'php artisan horizon:terminate' => 'Gracefully stop Horizon',
                        ],
                    ],
                    [
                        'name' => 'queue:work',
                        'description' => 'Start processing jobs on the queue',
                        'usage' => 'php artisan queue:work [--queue=default]',
                        'examples' => [
                            'php artisan queue:work' => 'Process default queue',
                            'php artisan queue:work --queue=high,default' => 'Process high priority first',
                        ],
                    ],
                    [
                        'name' => 'queue:failed',
                        'description' => 'List all failed queue jobs',
                        'usage' => 'php artisan queue:failed',
                        'examples' => [
                            'php artisan queue:failed' => 'Show all failed jobs',
                        ],
                    ],
                    [
                        'name' => 'queue:retry',
                        'description' => 'Retry a failed queue job',
                        'usage' => 'php artisan queue:retry [id|all]',
                        'examples' => [
                            'php artisan queue:retry all' => 'Retry all failed jobs',
                            'php artisan queue:retry 123' => 'Retry specific job by ID',
                        ],
                    ],
                ],
            ],
            'database' => [
                'title' => 'Database',
                'commands' => [
                    [
                        'name' => 'migrate',
                        'description' => 'Run database migrations',
                        'usage' => 'php artisan migrate [--force]',
                        'options' => [
                            '--force' => 'Force migrations in production',
                        ],
                        'examples' => [
                            'php artisan migrate' => 'Run pending migrations',
                            'php artisan migrate --force' => 'Force migrations in production',
                        ],
                    ],
                    [
                        'name' => 'migrate:status',
                        'description' => 'Show migration status',
                        'usage' => 'php artisan migrate:status',
                        'examples' => [
                            'php artisan migrate:status' => 'List all migrations and their status',
                        ],
                    ],
                    [
                        'name' => 'db:show',
                        'description' => 'Display database information',
                        'usage' => 'php artisan db:show',
                        'examples' => [
                            'php artisan db:show' => 'Show database connection info',
                        ],
                    ],
                ],
            ],
            'monitoring' => [
                'title' => 'Monitoring & Debugging',
                'commands' => [
                    [
                        'name' => 'schedule:list',
                        'description' => 'List all scheduled tasks',
                        'usage' => 'php artisan schedule:list',
                        'examples' => [
                            'php artisan schedule:list' => 'Show all scheduled tasks and next run time',
                        ],
                    ],
                    [
                        'name' => 'schedule:test',
                        'description' => 'Test scheduled tasks',
                        'usage' => 'php artisan schedule:test',
                        'examples' => [
                            'php artisan schedule:test' => 'Simulate schedule run',
                        ],
                    ],
                    [
                        'name' => 'route:list',
                        'description' => 'List all registered routes',
                        'usage' => 'php artisan route:list [--json]',
                        'examples' => [
                            'php artisan route:list' => 'Show all routes',
                            'php artisan route:list --json' => 'Output as JSON',
                        ],
                    ],
                ],
            ],
        ];
    }
    
    private function getScripts()
    {
        return [
            'monitoring' => [
                'title' => 'System Monitoring',
                'scripts' => [
                    [
                        'name' => 'monitor_system_health.sh',
                        'path' => '/www/scripts/monitor_system_health.sh',
                        'description' => 'Comprehensive system health monitoring script that checks CPU, memory, disk I/O, PostgreSQL queries, and PHP-FPM performance',
                        'usage' => '/www/scripts/monitor_system_health.sh',
                        'schedule' => 'Every 2 minutes via cron',
                        'logs' => '/var/log/thetradevisor/health_monitor.log',
                        'alerts' => '/var/log/thetradevisor/alerts.log',
                        'features' => [
                            'CPU usage monitoring (threshold: 80%)',
                            'Memory usage monitoring (threshold: 85%)',
                            'Disk I/O monitoring (threshold: 80%)',
                            'PostgreSQL long query detection (>5s)',
                            'PHP-FPM slow request tracking',
                            'Auto-recovery under high load',
                            'Alert notifications',
                        ],
                    ],
                    [
                        'name' => 'send_alert.sh',
                        'path' => '/www/scripts/send_alert.sh',
                        'description' => 'Send system alerts via email or logging',
                        'usage' => '/www/scripts/send_alert.sh "Alert Title" "Alert Message"',
                        'examples' => [
                            '/www/scripts/send_alert.sh "High CPU" "CPU usage at 95%"' => 'Send CPU alert',
                        ],
                    ],
                ],
            ],
            'maintenance' => [
                'title' => 'Maintenance Scripts',
                'scripts' => [
                    [
                        'name' => 'cleanup_backups.sh',
                        'path' => '/www/scripts/cleanup_backups.sh',
                        'description' => 'Clean up old backup files to save disk space',
                        'usage' => '/www/scripts/cleanup_backups.sh [days]',
                        'examples' => [
                            '/www/scripts/cleanup_backups.sh 30' => 'Delete backups older than 30 days',
                        ],
                    ],
                    [
                        'name' => 'extract_slow_queries.sh',
                        'path' => '/www/scripts/extract_slow_queries.sh',
                        'description' => 'Extract and analyze slow queries from PostgreSQL logs',
                        'usage' => '/www/scripts/extract_slow_queries.sh',
                        'output' => 'Displays slow queries with execution times',
                    ],
                ],
            ],
            'development' => [
                'title' => 'Development Tools',
                'scripts' => [
                    [
                        'name' => 'add_credits_to_docs.sh',
                        'path' => '/www/scripts/add_credits_to_docs.sh',
                        'description' => 'Add author credits to documentation files',
                        'usage' => '/www/scripts/add_credits_to_docs.sh [file]',
                    ],
                    [
                        'name' => 'fix_all_credits.sh',
                        'path' => '/www/scripts/fix_all_credits.sh',
                        'description' => 'Fix author credits in all documentation files',
                        'usage' => '/www/scripts/fix_all_credits.sh',
                    ],
                    [
                        'name' => 'create_github_issue.sh',
                        'path' => '/www/scripts/create_github_issue.sh',
                        'description' => 'Create GitHub issues from command line',
                        'usage' => '/www/scripts/create_github_issue.sh "Title" "Description"',
                    ],
                ],
            ],
        ];
    }
    
    private function getSystemInfo()
    {
        return [
            'environment' => [
                'App Environment' => config('app.env'),
                'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
                'PHP Version' => PHP_VERSION,
                'Laravel Version' => app()->version(),
            ],
            'database' => [
                'Driver' => config('database.default'),
                'Database' => config('database.connections.pgsql.database'),
                'Host' => config('database.connections.pgsql.host'),
            ],
            'cache' => [
                'Driver' => config('cache.default'),
            ],
            'queue' => [
                'Driver' => config('queue.default'),
            ],
            'paths' => [
                'Application' => base_path(),
                'Storage' => storage_path(),
                'Logs' => storage_path('logs'),
                'Scripts' => base_path('scripts'),
            ],
        ];
    }
    
    private function getScheduledTasks()
    {
        return [
            [
                'name' => 'Update Currency Rates',
                'schedule' => 'Hourly',
                'command' => 'CurrencyService::updateAllRates()',
                'description' => 'Updates exchange rates for all currencies',
            ],
            [
                'name' => 'Update GeoIP Database',
                'schedule' => 'Every 14 days at 2:00 AM',
                'command' => 'php artisan geoip:update',
                'description' => 'Downloads and updates MaxMind GeoIP database',
            ],
            [
                'name' => 'Cleanup Inactive Accounts',
                'schedule' => 'Daily at 3:00 AM',
                'command' => 'php artisan accounts:cleanup-inactive',
                'description' => 'Deletes accounts inactive for 180+ days',
            ],
            [
                'name' => 'System Health Monitor',
                'schedule' => 'Every 2 minutes',
                'command' => '/www/scripts/monitor_system_health.sh',
                'description' => 'Monitors system resources and performance',
            ],
        ];
    }
    
    private function getMiddlewareInfo()
    {
        return [
            'api.key' => [
                'name' => 'ValidateApiKey',
                'description' => 'Validates API key for API endpoints',
                'usage' => 'Applied to all /api routes',
            ],
            'admin' => [
                'name' => 'IsAdmin',
                'description' => 'Restricts access to admin users only',
                'usage' => 'Applied to /admin routes',
            ],
            'recaptcha' => [
                'name' => 'VerifyRecaptcha',
                'description' => 'Verifies reCAPTCHA on forms',
                'usage' => 'Applied to register, login, contact forms',
            ],
            'api.rate.limit' => [
                'name' => 'ApiRateLimiter',
                'description' => 'Rate limiting for API requests (1000/hour per key)',
                'usage' => 'Applied to API routes',
            ],
            'rate.limit.analytics' => [
                'name' => 'RateLimitAnalytics',
                'description' => 'Rate limiting for analytics (10 requests/minute)',
                'usage' => 'Applied to analytics routes',
            ],
            'circuit.breaker' => [
                'name' => 'CircuitBreakerMiddleware',
                'description' => 'Auto-disables expensive features under high load',
                'usage' => 'Applied to resource-intensive routes',
            ],
        ];
    }
    
    private function getServicesInfo()
    {
        return [
            'nginx' => [
                'name' => 'Nginx (Load Balancer)',
                'description' => 'Main web server and load balancer',
                'port' => '443 (HTTPS)',
                'status_command' => 'sudo systemctl status nginx',
                'restart_command' => 'sudo systemctl restart nginx',
                'logs' => '/var/log/nginx/error.log',
            ],
            'php-fpm' => [
                'name' => 'PHP 8.3-FPM',
                'description' => 'PHP FastCGI Process Manager (5 pools)',
                'status_command' => 'sudo systemctl status php8.3-fpm',
                'restart_command' => 'sudo systemctl restart php8.3-fpm',
                'logs' => '/var/log/php8.3-fpm.log',
                'slow_log' => '/var/log/php8.3-fpm-slow.log',
            ],
            'postgresql' => [
                'name' => 'PostgreSQL 16',
                'description' => 'Primary database server',
                'status_command' => 'sudo systemctl status postgresql@16-main',
                'restart_command' => 'sudo systemctl restart postgresql@16-main',
                'logs' => '/var/log/postgresql/postgresql-16-main.log',
            ],
            'horizon' => [
                'name' => 'Laravel Horizon',
                'description' => 'Queue worker supervisor',
                'url' => '/horizon',
                'terminate_command' => 'php artisan horizon:terminate',
                'description_full' => 'Manages queue workers and provides monitoring dashboard',
            ],
        ];
    }
}
