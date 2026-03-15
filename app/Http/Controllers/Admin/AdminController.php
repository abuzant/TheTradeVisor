<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Deal;
use App\Models\EnterpriseBroker;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        // Get unique broker names from trading accounts
        $knownBrokers = TradingAccount::distinct('broker_name')
            ->whereNotNull('broker_name')
            ->count('broker_name');
        
        // Get enterprise brokers count
        $enterpriseBrokers = EnterpriseBroker::where('is_active', true)->count();
        
        // Get next enterprise broker expiry
        $nextExpiry = EnterpriseBroker::where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->orderBy('subscription_ends_at', 'asc')
            ->first();
        
        // Get active terminals (sent data within last hour)
        $activeTerminals = TradingAccount::where('last_sync_at', '>=', now()->subHour())
            ->count();

        // Get 24-hour statistics
        $twentyFourHoursAgo = now()->subHours(24);
        
        // Users added in last 24 hours
        $usersLast24Hours = User::where('created_at', '>=', $twentyFourHoursAgo)->count();
        
        // Accounts created in last 24 hours
        $accountsLast24Hours = TradingAccount::where('created_at', '>=', $twentyFourHoursAgo)->count();
        
        // Positions opened/closed in last 24 hours
        $positionsOpenedLast24Hours = Deal::where('time', '>=', $twentyFourHoursAgo)
            ->where('entry', 'in')
            ->whereIn('type', ['buy', 'sell'])
            ->count();
            
        $positionsClosedLast24Hours = Deal::where('time', '>=', $twentyFourHoursAgo)
            ->where('entry', 'out')
            ->whereIn('type', ['buy', 'sell'])
            ->count();
        
        // New brokers in last 24 hours (first time seeing this broker)
        $newBrokersLast24Hours = TradingAccount::where('created_at', '>=', $twentyFourHoursAgo)
            ->whereNotNull('broker_name')
            ->distinct('broker_name')
            ->count('broker_name');
        
        // Uninstall feedback statistics
        $uninstallsLast24Hours = \App\Models\UninstallFeedback::where('submitted_at', '>=', $twentyFourHoursAgo)->count();
        $uninstallsWithCommentsLast24Hours = \App\Models\UninstallFeedback::where('submitted_at', '>=', $twentyFourHoursAgo)
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->count();
        $uninstallsWithEmailLast24Hours = \App\Models\UninstallFeedback::where('submitted_at', '>=', $twentyFourHoursAgo)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();
        
        // Backup statistics
        $databaseBackups = count(glob('/var/backups/thetradevisor/database/thetradevisor-full-*.sql.gz'));
        $filesystemBackups = count(glob('/var/backups/thetradevisor/application/thetradevisor-app-*.tar.gz'));
        $totalBackups = $databaseBackups + $filesystemBackups;
        
        // Error metrics for the last hour
        $errorMetrics = $this->getErrorMetrics();
        $errorRateThreshold = (float) (\Illuminate\Support\Facades\Redis::get('monitoring:error_rate_threshold') ?? 5.0);
        
        // Terminal locations for map
        $terminalLocations = $this->getTerminalLocations();

        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_accounts' => TradingAccount::count(),
            'active_accounts' => TradingAccount::where('is_active', true)->count(),
            'total_positions' => Position::where('is_open', true)->count(),
            'total_trades_today' => Deal::whereDate('time', today())->count(),
            'total_volume_today' => Deal::whereDate('time', today())->sum('volume'),
            'known_brokers' => $knownBrokers,
            'enterprise_brokers' => $enterpriseBrokers,
            'active_terminals' => $activeTerminals,
            // 24-hour stats
            'users_last_24_hours' => $usersLast24Hours,
            'accounts_last_24_hours' => $accountsLast24Hours,
            'positions_opened_last_24_hours' => $positionsOpenedLast24Hours,
            'positions_closed_last_24_hours' => $positionsClosedLast24Hours,
            'new_brokers_last_24_hours' => $newBrokersLast24Hours,
            'uninstalls_last_24_hours' => $uninstallsLast24Hours,
            'uninstalls_with_comments_last_24_hours' => $uninstallsWithCommentsLast24Hours,
            'uninstalls_with_email_last_24_hours' => $uninstallsWithEmailLast24Hours,
            'total_backups' => $totalBackups,
            'database_backups' => $databaseBackups,
            'filesystem_backups' => $filesystemBackups,
            // Error metrics
            'error_rate_last_hour' => $errorMetrics['error_rate'],
            'error_rate_threshold' => $errorRateThreshold,
            'errors_last_hour' => $errorMetrics['errors_last_hour'],
        ];

        // Get list of enterprise broker names for star indicator
        $enterpriseBrokerNames = EnterpriseBroker::where('is_active', true)
            ->pluck('official_broker_name')
            ->toArray();

        // Sortable columns for users
        $userSortableColumns = ['name', 'email', 'created_at'];
        $usersQuery = User::with(['tradingAccounts' => function($query) {
            $query->select('user_id', 'broker_name', 'id');
        }]);

        if ($request->has('users_sort_by')) {
            $usersQuery = $this->applySorting($usersQuery, $request, $userSortableColumns, 'created_at', 'desc', 'users_');
        } else {
            $usersQuery->latest();
        }

        $recentUsers = $usersQuery->paginate(10, ['*'], 'users_page');

        // Sortable columns for accounts
        $accountSortableColumns = ['broker_name', 'account_number', 'balance', 'equity', 'last_sync_at'];
        $accountsQuery = TradingAccount::with('user');

        if ($request->has('accounts_sort_by')) {
            $accountsQuery = $this->applySorting($accountsQuery, $request, $accountSortableColumns, 'last_sync_at', 'desc', 'accounts_');
        } else {
            $accountsQuery->latest('last_sync_at');
        }

        $recentAccounts = $accountsQuery->paginate(10, ['*'], 'accounts_page');

        // Pass sorting parameters to view
        $usersSortBy = $request->get('users_sort_by', 'created_at');
        $usersSortDirection = $request->get('users_sort_direction', 'desc');
        $accountsSortBy = $request->get('accounts_sort_by', 'last_sync_at');
        $accountsSortDirection = $request->get('accounts_sort_direction', 'desc');

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentAccounts',
            'usersSortBy',
            'usersSortDirection',
            'accountsSortBy',
            'accountsSortDirection',
            'nextExpiry',
            'enterpriseBrokerNames',
            'terminalLocations'
        ));
    }


    /**
     * Get terminal locations for map visualization
     */
    private function getTerminalLocations()
    {
        // Get last 50 terminal connections with location data
        $terminals = TradingAccount::whereNotNull('last_sync_at')
            ->whereNotNull('country_name')
            ->latest('last_sync_at')
            ->take(50)
            ->get(['last_sync_at', 'country_name', 'detected_country', 'last_seen_ip', 'broker_name']);

        // Group by country and get counts
        $countryGroups = $terminals->groupBy('country_name')->map(function ($group, $countryName) {
            return [
                'country' => $countryName,
                'country_code' => $group->first()->detected_country,
                'connections' => $group->count(),
                'ips' => $group->pluck('last_seen_ip')->unique()->values(),
                'last_connection' => $group->first()->last_sync_at,
                'brokers' => $group->pluck('broker_name')->unique()->values(),
            ];
        })->values();

        // Country coordinates for major countries
        $countryCoordinates = [
            'United Arab Emirates' => [23.4241, 53.8478],
            'United States' => [37.0902, -95.7129],
            'United Kingdom' => [55.3781, -3.4360],
            'Germany' => [51.1657, 10.4515],
            'France' => [46.2276, 2.2137],
            'Italy' => [41.8719, 12.5674],
            'Spain' => [40.4637, -3.7492],
            'Netherlands' => [52.1326, 5.2913],
            'Switzerland' => [46.8182, 8.2275],
            'Australia' => [-25.2744, 133.7751],
            'Canada' => [56.1304, -106.3468],
            'Japan' => [36.2048, 138.2529],
            'China' => [35.8617, 104.1954],
            'India' => [20.5937, 78.9629],
            'Singapore' => [1.3521, 103.8198],
            'Turkey' => [38.9637, 35.2433],
            'Russia' => [61.5240, 105.3188],
            'Brazil' => [-14.2350, -51.9253],
            'South Africa' => [-30.5595, 22.9375],
            'Mexico' => [23.6345, -102.5528],
            'Argentina' => [-38.4161, -63.6167],
            'Egypt' => [26.8206, 30.8025],
            'Saudi Arabia' => [23.8859, 45.0792],
            'Malaysia' => [4.2105, 101.9758],
            'Thailand' => [15.8700, 100.9925],
            'Indonesia' => [-0.7893, 113.9213],
            'Philippines' => [12.8797, 121.7740],
            'Vietnam' => [14.0583, 108.2772],
            'Poland' => [51.9194, 19.1451],
            'Sweden' => [60.1282, 18.6435],
            'Norway' => [60.4720, 8.4689],
            'Denmark' => [56.2639, 9.5018],
            'Finland' => [61.9241, 25.7482],
            'Belgium' => [50.5039, 4.4699],
            'Austria' => [47.5162, 14.5501],
            'Ireland' => [53.4129, -8.2439],
            'Portugal' => [39.3999, -8.2245],
            'Greece' => [39.0742, 21.8243],
            'Czech Republic' => [49.8175, 15.4730],
            'Hungary' => [47.1625, 19.5033],
            'Romania' => [45.9432, 24.9668],
            'Bulgaria' => [42.7339, 25.4858],
            'Croatia' => [45.1, 15.2],
            'Slovakia' => [48.6690, 19.6990],
            'Lithuania' => [55.1694, 23.8813],
            'Latvia' => [56.8796, 24.6032],
            'Estonia' => [58.5953, 25.0136],
            'Slovenia' => [46.1512, 14.9955],
            'Cyprus' => [35.1264, 33.4299],
            'Luxembourg' => [49.8153, 6.1296],
            'Malta' => [35.9375, 14.3754],
        ];

        // Add coordinates to country data
        $locations = $countryGroups->map(function ($country) use ($countryCoordinates) {
            $coords = $countryCoordinates[$country['country']] ?? [0, 0];
            return array_merge($country, [
                'lat' => $coords[0],
                'lng' => $coords[1],
                'has_coordinates' => $coords !== [0, 0],
            ]);
        })->filter(function ($location) {
            return $location['has_coordinates'];
        })->values();

        return [
            'locations' => $locations,
            'total_connections' => $terminals->count(),
            'unique_countries' => $locations->count(),
            'latest_connection' => $terminals->first()->last_sync_at ?? null,
        ];
    }


    /**
     * Error metrics from logs (copied from MonitoringController)
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
        $twentyFourHoursAgo = now()->subDay(24);
        
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
        
        // Calculate error rate (errors per 1000 requests)
        $errorRate = $totalLines > 0 ? round(($errorsLast24h / max($totalLines, 1)) * 1000, 2) : 0;
        
        return [
            'errors_last_hour' => $errorsLastHour,
            'errors_last_24h' => $errorsLast24h,
            'error_rate' => $errorRate,
            'recent_errors' => $recentErrors,
        ];
    }

    /**
     * Extract log timestamp (copied from MonitoringController)
     */
    private function extractLogTimestamp($logLine)
    {
        try {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $logLine, $matches)) {
                return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $matches[1]);
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }


}
