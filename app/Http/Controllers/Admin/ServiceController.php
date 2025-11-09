<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    private $services = [
        'nginx' => 'Nginx Load Balancer',
        'php8.3-fpm' => 'PHP-FPM (All Pools)',
        'postgresql' => 'PostgreSQL Database',
        'redis' => 'Redis Cache',
        'supervisor' => 'Supervisor (Queue Workers)',
    ];
    
    private $backendInstances = [
        'backend-1' => 'Backend Instance 1 (Port 8081)',
        'backend-2' => 'Backend Instance 2 (Port 8082)',
        'backend-3' => 'Backend Instance 3 (Port 8083)',
        'backend-4' => 'Backend Instance 4 (Port 8084)',
    ];
    
    private $horizonService = 'horizon';
    
    public function index()
    {
        $serviceStatuses = [];
        
        foreach ($this->services as $service => $name) {
            $serviceStatuses[$service] = [
                'name' => $name,
                'status' => $this->getServiceStatus($service),
                'can_restart' => true,
            ];
        }
        
        // Add backend instance statuses
        $backendStatuses = [];
        foreach ($this->backendInstances as $instance => $name) {
            $backendStatuses[$instance] = [
                'name' => $name,
                'status' => $this->getBackendStatus($instance),
                'can_restart' => true,
            ];
        }
        
        // Add Horizon status (supervisor-based)
        $horizonStatus = $this->getHorizonStatus();
        
        return view('admin.services', compact('serviceStatuses', 'backendStatuses', 'horizonStatus'));
    }
    
    private function getHorizonStatus()
    {
        $result = Process::run("sudo -n supervisorctl status horizon 2>&1");
        $output = trim($result->output());
        
        // If sudo fails or output is empty, try without sudo
        if (empty($output) || str_contains($output, 'sudo')) {
            $result = Process::run("supervisorctl status horizon 2>&1");
            $output = trim($result->output());
        }
        
        // Match the same structure as getServiceStatus()
        return [
            'name' => 'Laravel Horizon',
            'status' => [
                'active' => str_contains($output, 'RUNNING'),
                'status' => !empty($output) ? $output : 'Unable to check status',
            ],
            'can_restart' => true,
        ];
    }
    
    private function getServiceStatus($service)
    {
        $result = Process::run("systemctl is-active {$service}");
        $status = trim($result->output());
        
        return [
            'active' => $status === 'active',
            'status' => $status,
        ];
    }
    
    private function getBackendStatus($instance)
    {
        // Extract instance number from backend-N format
        $instanceNum = str_replace('backend-', '', $instance);
        $pidFile = "/run/nginx-backend-{$instanceNum}.pid";
        
        // Check if PID file exists and process is running
        $isRunning = false;
        if (file_exists($pidFile)) {
            $pid = trim(file_get_contents($pidFile));
            if ($pid && posix_kill((int)$pid, 0)) {
                $isRunning = true;
            }
        }
        
        return [
            'active' => $isRunning,
            'status' => $isRunning ? 'running' : 'stopped',
        ];
    }
    
    public function restart(Request $request, $service)
    {
        if (!isset($this->services[$service])) {
            return redirect()->route('admin.services')
                ->with('error', 'Invalid service');
        }
        
        try {
            $result = Process::run("sudo systemctl restart {$service}");
            
            if ($result->successful()) {
                return redirect()->route('admin.services')
                    ->with('success', "{$this->services[$service]} restarted successfully");
            } else {
                return redirect()->route('admin.services')
                    ->with('error', "Failed to restart {$this->services[$service]}: " . $result->errorOutput());
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.services')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function restartBackend(Request $request, $instance)
    {
        if (!isset($this->backendInstances[$instance])) {
            return redirect()->route('admin.services')
                ->with('error', 'Invalid backend instance');
        }
        
        try {
            // Extract instance number from backend-N format
            $instanceNum = str_replace('backend-', '', $instance);
            $pidFile = "/run/nginx-backend-{$instanceNum}.pid";
            
            // Stop existing process gracefully
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                if ($pid) {
                    Process::run("sudo kill -QUIT {$pid}");
                    usleep(500000); // Wait 0.5 seconds
                }
            }
            
            // Start new process
            $result = Process::run("sudo nginx -c /etc/nginx/backends/nginx-backend-{$instanceNum}-master.conf");
            
            if ($result->successful()) {
                return redirect()->route('admin.services')
                    ->with('success', "{$this->backendInstances[$instance]} restarted successfully");
            } else {
                return redirect()->route('admin.services')
                    ->with('error', "Failed to restart {$this->backendInstances[$instance]}: " . $result->errorOutput());
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.services')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function horizonControl(Request $request, $action)
    {
        $validActions = ['start', 'stop', 'restart'];
        
        if (!in_array($action, $validActions)) {
            return redirect()->route('admin.services')
                ->with('error', 'Invalid action');
        }
        
        try {
            $result = Process::run("sudo supervisorctl {$action} horizon");
            
            if ($result->successful()) {
                return redirect()->route('admin.services')
                    ->with('success', "Horizon {$action}ed successfully");
            } else {
                return redirect()->route('admin.services')
                    ->with('error', "Failed to {$action} Horizon: " . $result->errorOutput());
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.services')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function clearAllCaches(Request $request)
    {
        try {
            // Clear Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('event:clear');
            
            // Flush Redis
            Cache::flush();
            
            // Clear Nginx cache
            Process::run('sudo rm -rf /var/cache/nginx/fastcgi/*');
            
            // Restart PHP-FPM (all pools)
            Process::run('sudo systemctl restart php8.3-fpm');
            
            // Restart backend instances
            for ($i = 1; $i <= 4; $i++) {
                $pidFile = "/run/nginx-backend-{$i}.pid";
                if (file_exists($pidFile)) {
                    $pid = trim(file_get_contents($pidFile));
                    if ($pid) {
                        Process::run("sudo kill -QUIT {$pid}");
                    }
                }
                usleep(100000); // Wait 0.1 seconds between stops
            }
            usleep(500000); // Wait 0.5 seconds before starting
            for ($i = 1; $i <= 4; $i++) {
                Process::run("sudo nginx -c /etc/nginx/backends/nginx-backend-{$i}-master.conf");
            }
            
            // Reload Load Balancer Nginx
            Process::run('sudo systemctl reload nginx');
            
            // Rebuild optimized caches
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            Artisan::call('event:cache');
            
            return redirect()->route('admin.services')
                ->with('success', 'All caches cleared and rebuilt successfully! Laravel, Redis, Nginx, and PHP-FPM refreshed.');
        } catch (\Exception $e) {
            return redirect()->route('admin.services')
                ->with('error', 'Error clearing caches: ' . $e->getMessage());
        }
    }
}
