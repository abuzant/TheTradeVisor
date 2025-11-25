<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SecureLogAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupManagerController extends Controller
{
    /**
     * Display the backup manager dashboard.
     */
    public function index()
    {
        try {
            $backupData = $this->getBackupData();
            $recentJobs = $this->getRecentBackupJobs();
            $storageStats = $this->getStorageStatistics();
            
            return view('admin.backup.manager', compact('backupData', 'recentJobs', 'storageStats'));
        } catch (\Exception $e) {
            Log::error('Backup Manager Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load backup manager: ' . $e->getMessage());
        }
    }
    
    /**
     * Display backup logs.
     */
    public function logs(Request $request)
    {
        try {
            $logType = $request->get('type', 'database');
            $logService = new SecureLogAccessService();
            
            // Get logs securely
            $logData = $logService->getBackupLogs($logType, 100);
            $logs = $logData['lines'];
            
            return view('admin.backup.logs', compact('logs', 'logType'));
        } catch (\Exception $e) {
            Log::error('Backup Logs Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load backup logs: ' . $e->getMessage());
        }
    }
    
    /**
     * Download a backup file.
     */
    public function download(Request $request)
    {
        try {
            $filename = $request->get('file');
            $type = $request->get('type', 'database');
            
            // Validate filename to prevent directory traversal
            if (!$this->isValidBackupFilename($filename, $type)) {
                abort(403, 'Invalid file request');
            }
            
            $backupDir = $type === 'database' 
                ? '/var/backups/thetradevisor/database'
                : '/var/backups/thetradevisor/application';
            
            $filePath = $backupDir . '/' . $filename;
            
            if (!file_exists($filePath)) {
                abort(404, 'Backup file not found');
            }
            
            // Log download
            Log::info("Backup downloaded by admin: {$filename}");
            
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/octet-stream',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Backup Download Error: ' . $e->getMessage());
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get backup data for dashboard.
     */
    private function getBackupData()
    {
        $data = [
            'database' => $this->getBackupFiles('database'),
            'application' => $this->getBackupFiles('application'),
            'config' => $this->getBackupFiles('config'),
        ];
        
        return $data;
    }
    
    /**
     * Get backup files for a specific type.
     */
    private function getBackupFiles($type)
    {
        $directories = [
            'database' => '/var/backups/thetradevisor/database',
            'application' => '/var/backups/thetradevisor/application',
            'config' => '/var/backups/thetradevisor/application',
        ];
        
        $patterns = [
            'database' => 'thetradevisor-full-*.sql.gz',
            'application' => 'thetradevisor-app-*.tar.gz',
            'config' => 'thetradevisor-config-*.tar.gz',
        ];
        
        $directory = $directories[$type] ?? null;
        $pattern = $patterns[$type] ?? null;
        
        if (!$directory || !is_dir($directory)) {
            return [];
        }
        
        $files = glob($directory . '/' . $pattern);
        $backupFiles = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            $stat = stat($file);
            
            $backupFiles[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => $this->formatBytes($stat['size']),
                'size_bytes' => $stat['size'],
                'created_at' => date('Y-m-d H:i:s', $stat['mtime']),
                'timestamp' => $stat['mtime'],
            ];
        }
        
        // Sort by creation date (newest first)
        usort($backupFiles, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return $backupFiles;
    }
    
    /**
     * Get recent backup jobs from logs.
     */
    private function getRecentBackupJobs()
    {
        $jobs = [];
        $logFiles = [
            '/var/log/backups/database.log' => 'Database',
            '/var/log/backups/application.log' => 'Application',
        ];
        
        foreach ($logFiles as $logFile => $type) {
            if (!file_exists($logFile)) {
                continue;
            }
            
            // Read last 50 lines of log
            $lines = $this->tailFile($logFile, 50);
            
            foreach ($lines as $line) {
                if (strpos($line, 'completed successfully') !== false) {
                    $jobs[] = [
                        'type' => $type,
                        'status' => 'success',
                        'message' => 'Backup completed successfully',
                        'timestamp' => $this->extractTimestampFromLog($line),
                    ];
                } elseif (strpos($line, 'BACKUP FAILED') !== false) {
                    $jobs[] = [
                        'type' => $type,
                        'status' => 'failed',
                        'message' => 'Backup failed',
                        'timestamp' => $this->extractTimestampFromLog($line),
                    ];
                }
            }
        }
        
        // Sort by timestamp and get last 7
        usort($jobs, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return array_slice($jobs, 0, 7);
    }
    
    /**
     * Get storage statistics.
     */
    private function getStorageStatistics()
    {
        $stats = [
            'database' => $this->getDirectoryStats('/var/backups/thetradevisor/database'),
            'application' => $this->getDirectoryStats('/var/backups/thetradevisor/application'),
            'total' => $this->getDirectoryStats('/var/backups/thetradevisor'),
        ];
        
        return $stats;
    }
    
    /**
     * Get directory statistics.
     */
    private function getDirectoryStats($directory)
    {
        if (!is_dir($directory)) {
            return [
                'total_size' => '0 B',
                'total_size_bytes' => 0,
                'file_count' => 0,
            ];
        }
        
        $files = glob($directory . '/*');
        $totalSize = 0;
        $fileCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $fileCount++;
            }
        }
        
        return [
            'total_size' => $this->formatBytes($totalSize),
            'total_size_bytes' => $totalSize,
            'file_count' => $fileCount,
        ];
    }
    
    /**
     * Get backup logs for the past 7 days.
     */
    private function getBackupLogs($type)
    {
        $logFile = $type === 'database' 
            ? '/var/log/backups/database.log'
            : '/var/log/backups/application.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        // Get last 7 days of logs
        $lines = $this->tailFile($logFile, 1000);
        $logs = [];
        $cutoffTime = time() - (7 * 24 * 60 * 60); // 7 days ago
        
        foreach ($lines as $line) {
            $timestamp = $this->extractTimestampFromLog($line);
            
            if ($timestamp && $timestamp >= $cutoffTime) {
                $logs[] = [
                    'timestamp' => $timestamp,
                    'datetime' => date('Y-m-d H:i:s', $timestamp),
                    'message' => $this->extractMessageFromLog($line),
                    'level' => $this->extractLogLevel($line),
                ];
            }
        }
        
        return array_reverse($logs); // Show newest first
    }
    
    /**
     * Check if backup is currently running.
     */
    private function isBackupRunning()
    {
        // Check for running backup processes
        $process = new Process(['pgrep', '-f', 'backup_database.sh']);
        $process->run();
        
        if ($process->isSuccessful()) {
            return true;
        }
        
        $process = new Process(['pgrep', '-f', 'backup_application.sh']);
        $process->run();
        
        return $process->isSuccessful();
    }
    
    /**
     * Validate backup filename to prevent directory traversal.
     */
    private function isValidBackupFilename($filename, $type)
    {
        $patterns = [
            'database' => '/^thetradevisor-full-\d{8}-\d{6}\.sql\.gz$/',
            'application' => '/^thetradevisor-app-\d{8}-\d{6}\.tar\.gz$/',
            'config' => '/^thetradevisor-config-\d{8}-\d{6}\.tar\.gz$/',
        ];
        
        $pattern = $patterns[$type] ?? null;
        
        if (!$pattern) {
            return false;
        }
        
        return preg_match($pattern, $filename) === 1;
    }
    
    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Read last N lines of a file.
     */
    private function tailFile($filename, $lines)
    {
        $handle = fopen($filename, "r");
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
            
            if ($beginning) {
                break;
            }
        }
        
        fclose($handle);
        
        return array_reverse($text);
    }
    
    /**
     * Extract timestamp from log line.
     */
    private function extractTimestampFromLog($line)
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
            return strtotime($matches[1]);
        }
        
        return time(); // Default to current time if no timestamp found
    }
    
    /**
     * Extract message from log line.
     */
    private function extractMessageFromLog($line)
    {
        if (preg_match('/- (.+)$/', $line, $matches)) {
            return $matches[1];
        }
        
        return $line;
    }
    
    /**
     * Extract log level from log line.
     */
    private function extractLogLevel($line)
    {
        if (preg_match('/(✅|❌|⚠️|🔍|🚀|🔄|📊|🗑️|💾)/', $line, $matches)) {
            $icon = $matches[1];
            
            $levels = [
                '✅' => 'success',
                '❌' => 'error',
                '⚠️' => 'warning',
                '🔍' => 'info',
                '🚀' => 'info',
                '🔄' => 'info',
                '📊' => 'info',
                '🗑️' => 'info',
                '💾' => 'info',
            ];
            
            return $levels[$icon] ?? 'info';
        }
        
        return 'info';
    }
}
