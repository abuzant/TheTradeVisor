<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SecureLogAccessService
{
    /**
     * Securely read backup logs with proper access control.
     */
    public function getBackupLogs(string $type = 'database', int $lines = 100): array
    {
        // Only admins can access logs
        if (!Auth::user() || !Auth::user()->is_admin) {
            Log::warning('Unauthorized log access attempt', [
                'user_id' => Auth::id(),
                'type' => $type
            ]);
            throw new \Exception('Unauthorized access to logs');
        }

        $logFile = match($type) {
            'database' => '/var/log/backups/database.log',
            'application' => '/var/log/backups/application.log',
            'verification' => '/var/log/backups/verification.log',
            default => throw new \Exception('Invalid log type')
        };

        // Validate file path and existence
        if (!$this->isValidLogFile($logFile)) {
            throw new \Exception('Log file not accessible');
        }

        try {
            if (!File::exists($logFile)) {
                return [
                    'lines' => [],
                    'total_lines' => 0,
                    'file_size' => 0,
                    'last_modified' => null
                ];
            }

            // Read last N lines safely
            $content = $this->tailFile($logFile, $lines);
            $logLines = $this->parseLogLines($content);

            return [
                'lines' => $logLines,
                'total_lines' => count($logLines),
                'file_size' => File::size($logFile),
                'last_modified' => File::lastModified($logFile),
                'file_path' => basename($logFile)
            ];

        } catch (\Exception $e) {
            Log::error('Failed to read backup logs', [
                'file' => $logFile,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            throw new \Exception('Unable to read log file: ' . $e->getMessage());
        }
    }

    /**
     * Get Laravel application logs securely.
     */
    public function getLaravelLogs(int $lines = 100): array
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            throw new \Exception('Unauthorized access to logs');
        }

        $logFile = storage_path('logs/laravel.log');

        if (!$this->isValidLaravelLogFile($logFile)) {
            throw new \Exception('Laravel log file not accessible');
        }

        try {
            if (!File::exists($logFile)) {
                return [
                    'lines' => [],
                    'total_lines' => 0,
                    'file_size' => 0,
                    'last_modified' => null
                ];
            }

            $content = $this->tailFile($logFile, $lines);
            $logLines = $this->parseLaravelLogLines($content);

            return [
                'lines' => $logLines,
                'total_lines' => count($logLines),
                'file_size' => File::size($logFile),
                'last_modified' => File::lastModified($logFile),
                'file_path' => basename($logFile)
            ];

        } catch (\Exception $e) {
            Log::error('Failed to read Laravel logs', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            throw new \Exception('Unable to read Laravel log file');
        }
    }

    /**
     * Validate that the log file is within allowed paths.
     */
    private function isValidLogFile(string $path): bool
    {
        $allowedPaths = [
            '/var/log/backups/database.log',
            '/var/log/backups/application.log',
            '/var/log/backups/verification.log'
        ];

        return in_array(realpath($path), $allowedPaths) && File::exists($path);
    }

    /**
     * Validate Laravel log file path.
     */
    private function isValidLaravelLogFile(string $path): bool
    {
        $allowedPath = storage_path('logs/laravel.log');
        return realpath($path) === realpath($allowedPath);
    }

    /**
     * Safely read last N lines from a file.
     */
    private function tailFile(string $file, int $lines): string
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            throw new \Exception('Cannot open file for reading');
        }

        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = [];

        while ($linecounter > 0) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) break;
        }

        fclose($handle);
        return implode('', array_reverse($text));
    }

    /**
     * Parse backup log lines into structured format.
     */
    private function parseLogLines(string $content): array
    {
        $lines = explode("\n", trim($content));
        $parsed = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $parsed[] = [
                'timestamp' => $this->extractTimestamp($line),
                'level' => $this->extractLevel($line),
                'message' => $this->extractMessage($line),
                'raw' => $line
            ];
        }

        return array_filter($parsed);
    }

    /**
     * Parse Laravel log lines.
     */
    private function parseLaravelLogLines(string $content): array
    {
        $lines = explode("\n", trim($content));
        $parsed = [];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            // Laravel log format: [2025-11-25 10:02:17] production.ERROR: message
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
                $parsed[] = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => strtolower($matches[3]),
                    'message' => $matches[4],
                    'raw' => $line
                ];
            } else {
                $parsed[] = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'level' => 'info',
                    'message' => $line,
                    'raw' => $line
                ];
            }
        }

        return array_filter($parsed);
    }

    /**
     * Extract timestamp from backup log line.
     */
    private function extractTimestamp(string $line): string
    {
        if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
            return $matches[1];
        }
        return date('Y-m-d H:i:s');
    }

    /**
     * Extract log level from line.
     */
    private function extractLevel(string $line): string
    {
        if (preg_match('/(ERROR|WARNING|INFO|SUCCESS|FAILED)/i', $line, $matches)) {
            return strtolower($matches[1]);
        }
        return 'info';
    }

    /**
     * Extract message from log line.
     */
    private function extractMessage(string $line): string
    {
        if (preg_match('/- (.+)$/', $line, $matches)) {
            return $matches[1];
        }
        return $line;
    }
}
