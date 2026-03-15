<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SecureConfigService
{
    /**
     * Check if sensitive configuration files are properly secured.
     */
    public function auditConfigSecurity(): array
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            throw new \Exception('Unauthorized access to configuration audit');
        }

        $issues = [];
        $basePath = base_path();

        // Check .env file permissions
        $envFile = $basePath . '/.env';
        if (File::exists($envFile)) {
            $perms = substr(sprintf('%o', fileperms($envFile)), -4);
            if ($perms !== '0640') {
                $issues[] = [
                    'type' => 'critical',
                    'file' => '.env',
                    'issue' => 'Insecure file permissions',
                    'current' => $perms,
                    'recommended' => '0640'
                ];
            }
        }

        // Check for exposed .env files in web-accessible directories
        $publicEnvFiles = [
            $basePath . '/public/.env',
            $basePath . '/storage/.env',
            $basePath . '/bootstrap/cache/.env'
        ];

        foreach ($publicEnvFiles as $file) {
            if (File::exists($file)) {
                $issues[] = [
                    'type' => 'critical',
                    'file' => str_replace($basePath, '', $file),
                    'issue' => 'Environment file in web-accessible directory',
                    'current' => 'EXPOSED',
                    'recommended' => 'REMOVE IMMEDIATELY'
                ];
            }
        }

        // Check config file permissions
        $configFiles = [
            'config/database.php',
            'config/mail.php',
            'config/services.php',
            'config/app.php'
        ];

        foreach ($configFiles as $configFile) {
            $fullPath = $basePath . '/' . $configFile;
            if (File::exists($fullPath)) {
                $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
                if ($perms !== '0644') {
                    $issues[] = [
                        'type' => 'warning',
                        'file' => $configFile,
                        'issue' => 'Non-standard file permissions',
                        'current' => $perms,
                        'recommended' => '0644'
                    ];
                }
            }
        }

        // Check for backup scripts permissions
        $backupScripts = [
            '/usr/local/bin/backup_database.sh',
            '/usr/local/bin/backup_application.sh',
            '/usr/local/bin/verify_backups.sh'
        ];

        foreach ($backupScripts as $script) {
            if (File::exists($script)) {
                $perms = substr(sprintf('%o', fileperms($script)), -4);
                if ($perms !== '0750') {
                    $issues[] = [
                        'type' => 'warning',
                        'file' => $script,
                        'issue' => 'Backup script permissions',
                        'current' => $perms,
                        'recommended' => '0750'
                    ];
                }
            }
        }

        // Check log file permissions
        $logDirs = [
            '/var/log/backups',
            storage_path('logs')
        ];

        foreach ($logDirs as $logDir) {
            if (is_dir($logDir)) {
                $perms = substr(sprintf('%o', fileperms($logDir)), -4);
                if (!in_array($perms, ['0750', '0755'])) {
                    $issues[] = [
                        'type' => 'warning',
                        'file' => $logDir,
                        'issue' => 'Log directory permissions',
                        'current' => $perms,
                        'recommended' => '0750'
                    ];
                }
            }
        }

        return [
            'issues' => $issues,
            'critical_count' => count(array_filter($issues, fn($i) => $i['type'] === 'critical')),
            'warning_count' => count(array_filter($issues, fn($i) => $i['type'] === 'warning')),
            'total_issues' => count($issues),
            'audit_time' => now()->toDateTimeString()
        ];
    }

    /**
     * Secure configuration files (requires appropriate system permissions).
     */
    public function secureConfigFiles(): array
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            throw new \Exception('Unauthorized access to configuration security');
        }

        $results = [];
        $basePath = base_path();

        // Secure .env file
        $envFile = $basePath . '/.env';
        if (File::exists($envFile)) {
            try {
                chmod($envFile, 0640);
                $results[] = [
                    'file' => '.env',
                    'action' => 'secured',
                    'status' => 'success',
                    'permissions' => '0640'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'file' => '.env',
                    'action' => 'failed',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        // Secure config files
        $configFiles = [
            'config/database.php',
            'config/mail.php',
            'config/services.php'
        ];

        foreach ($configFiles as $configFile) {
            $fullPath = $basePath . '/' . $configFile;
            if (File::exists($fullPath)) {
                try {
                    chmod($fullPath, 0644);
                    $results[] = [
                        'file' => $configFile,
                        'action' => 'secured',
                        'status' => 'success',
                        'permissions' => '0644'
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'file' => $configFile,
                        'action' => 'failed',
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }
        }

        Log::info('Configuration files security audit completed', [
            'user_id' => Auth::id(),
            'results_count' => count($results)
        ]);

        return $results;
    }

    /**
     * Check for exposed sensitive information in web-accessible directories.
     */
    public function scanForExposedFiles(): array
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            throw new \Exception('Unauthorized access to file scan');
        }

        $exposedFiles = [];
        $publicPath = public_path();
        $storagePath = storage_path();

        // Patterns for sensitive files
        $sensitivePatterns = [
            '/\.env/',
            '/\.env\./',
            '/config\.php$/',
            '/database\.php$/',
            '/.*key\.php$/',
            '/.*secret\.php$/',
            '/\.pem$/',
            '/\.key$/',
            '/\.crt$/',
            '/\.p12$/',
            '/\.pfx$/'
        ];

        // Scan public directory
        $this->scanDirectory($publicPath, $sensitivePatterns, $exposedFiles, 'public');

        // Scan storage directory (excluding framework directories)
        $this->scanDirectory($storagePath, $sensitivePatterns, $exposedFiles, 'storage', [
            'framework',
            'app',
            'logs'
        ]);

        return [
            'exposed_files' => $exposedFiles,
            'total_count' => count($exposedFiles),
            'scan_time' => now()->toDateTimeString()
        ];
    }

    /**
     * Recursively scan directory for sensitive files.
     */
    private function scanDirectory(string $directory, array $patterns, array &$results, string $type, array $excludeDirs = []): void
    {
        if (!is_dir($directory)) return;

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            if (in_array($item, $excludeDirs)) continue;

            $path = $directory . '/' . $item;
            
            if (is_dir($path)) {
                $this->scanDirectory($path, $patterns, $results, $type, $excludeDirs);
            } else {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $item)) {
                        $results[] = [
                            'file' => str_replace(base_path(), '', $path),
                            'type' => $type,
                            'full_path' => $path,
                            'size' => filesize($path),
                            'modified' => filemtime($path)
                        ];
                        break;
                    }
                }
            }
        }
    }
}
