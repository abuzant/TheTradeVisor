<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    private $logPaths = [
        'laravel' => '/var/www/thetradevisor.com/storage/logs/laravel.log',
        'worker' => '/var/www/thetradevisor.com/storage/logs/worker.log',
        'horizon' => '/var/www/thetradevisor.com/storage/logs/horizon.log',
        'nginx_access' => '/var/log/nginx/thetradevisor-access.log',
        'nginx_error' => '/var/log/nginx/thetradevisor-error.log',
        'nginx_api_access' => '/var/log/nginx/api-thetradevisor-access.log',
        'nginx_api_error' => '/var/log/nginx/api-thetradevisor-error.log',
        'postgresql' => '/var/log/postgresql/postgresql-16-main.log',
        'redis' => '/var/log/redis/redis-server.log',
        'php_fpm' => '/var/log/php8.3-fpm.log',
        'php_fpm_slow' => '/var/log/php8.3-fpm-slow.log',
        'system_alerts' => '/var/log/thetradevisor/alerts.log',
        'health_monitor' => '/var/log/thetradevisor/health_monitor.log',
    ];
    
    public function index(Request $request)
    {
        $logType = $request->get('type', 'laravel');
        $lines = $request->get('lines', 100);
        
        $availableLogs = [];
        foreach ($this->logPaths as $key => $path) {
            if (File::exists($path) && File::isReadable($path)) {
                $availableLogs[$key] = [
                    'name' => ucwords(str_replace('_', ' ', $key)),
                    'path' => $path,
                    'size' => $this->formatBytes(File::size($path)),
                    'modified' => File::lastModified($path),
                ];
            }
        }
        
        $logContent = '';
        $error = null;
        
        if (isset($this->logPaths[$logType])) {
            $path = $this->logPaths[$logType];
            
            if (File::exists($path) && File::isReadable($path)) {
                // Get last N lines efficiently
                $logContent = $this->tailFile($path, $lines);
            } else {
                $error = "Log file not found or not readable: {$path}";
            }
        }
        
        return view('admin.logs', compact('availableLogs', 'logContent', 'logType', 'lines', 'error'));
    }
    
    private function tailFile($file, $lines = 100)
    {
        $handle = fopen($file, "r");
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
        
        return implode("", array_reverse($text));
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    public function download(Request $request)
    {
        $logType = $request->get('type');
        
        if (!isset($this->logPaths[$logType])) {
            abort(404, 'Log file not found');
        }
        
        $path = $this->logPaths[$logType];
        
        if (!File::exists($path) || !File::isReadable($path)) {
            abort(404, 'Log file not accessible');
        }
        
        return response()->download($path, basename($path));
    }
}
