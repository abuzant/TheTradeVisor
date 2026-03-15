<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CircuitBreaker;
use Illuminate\Http\Request;

class CircuitBreakerController extends Controller
{
    /**
     * List of services to monitor
     */
    private array $services = [
        'redis' => 'Redis Cache',
        'database' => 'Database',
        'currency_api' => 'Currency API',
        'geoip' => 'GeoIP Service',
        'email_service' => 'Email Service',
    ];

    /**
     * Display circuit breaker status dashboard
     */
    public function index()
    {
        $statuses = CircuitBreaker::getAllStatus(array_keys($this->services));

        // Add friendly names
        foreach ($statuses as $key => $status) {
            $statuses[$key]['friendly_name'] = $this->services[$key] ?? $key;
        }

        return view('admin.circuit-breakers.index', compact('statuses'));
    }

    /**
     * Reset a specific circuit breaker
     */
    public function reset(Request $request, string $service)
    {
        if (!isset($this->services[$service])) {
            return redirect()->back()->with('error', 'Invalid service name');
        }

        $breaker = new CircuitBreaker($service);
        $breaker->reset();

        return redirect()->back()->with('success', "Circuit breaker reset for {$this->services[$service]}");
    }

    /**
     * Reset all circuit breakers
     */
    public function resetAll()
    {
        foreach (array_keys($this->services) as $service) {
            $breaker = new CircuitBreaker($service);
            $breaker->reset();
        }

        return redirect()->back()->with('success', 'All circuit breakers have been reset');
    }
}
