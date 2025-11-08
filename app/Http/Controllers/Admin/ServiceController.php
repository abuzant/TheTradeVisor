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
        'nginx' => 'Nginx Web Server',
        'php8.3-fpm' => 'PHP-FPM',
        'postgresql' => 'PostgreSQL Database',
        'redis' => 'Redis Cache',
        'supervisor' => 'Supervisor (Queue Workers)',
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
        
        // Add Horizon status (supervisor-based)
        $horizonStatus = $this->getHorizonStatus();
        
        return view('admin.services', compact('serviceStatuses', 'horizonStatus'));
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
            
            // Restart PHP-FPM
            Process::run('sudo systemctl restart php8.3-fpm');
            
            // Reload Nginx
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
