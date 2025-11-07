<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class ServiceController extends Controller
{
    private $services = [
        'nginx' => 'Nginx Web Server',
        'php8.3-fpm' => 'PHP-FPM',
        'postgresql' => 'PostgreSQL Database',
        'redis' => 'Redis Cache',
        'supervisor' => 'Supervisor (Queue Workers)',
    ];
    
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
        
        return view('admin.services', compact('serviceStatuses'));
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
        
        // For security, we need to set up sudo permissions
        // This will be done in the next step
        
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
}
