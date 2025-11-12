<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CircuitBreakerService;
use Symfony\Component\HttpFoundation\Response;

class CircuitBreakerMiddleware
{
    protected $circuitBreaker;

    public function __construct(CircuitBreakerService $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
    }

    /**
     * Handle an incoming request.
     * Block expensive operations when circuit breaker is open.
     */
    public function handle(Request $request, Closure $next, string $feature = 'default'): Response
    {
        // Check if circuit breaker is open
        if ($this->circuitBreaker->isOpen()) {
            // Check if this specific feature should be disabled
            if ($this->circuitBreaker->shouldDisableFeature($feature)) {
                return $this->handleCircuitOpen($request, $feature);
            }
        }

        return $next($request);
    }

    /**
     * Handle request when circuit is open
     */
    protected function handleCircuitOpen(Request $request, string $feature): Response
    {
        $metrics = $this->circuitBreaker->getMetrics();
        
        // For AJAX/API requests, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Service temporarily unavailable due to high server load',
                'message' => 'The system is currently under heavy load. Please try again in a few minutes.',
                'feature' => $feature,
                'retry_after' => 300,
                'metrics' => [
                    'cpu_usage' => $metrics['cpu_usage'] ?? 'N/A',
                    'memory_usage' => $metrics['memory_usage'] ?? 'N/A',
                ],
            ], 503);
        }

        // For web requests, show a friendly page
        return response()->view('errors.circuit-breaker', [
            'feature' => $feature,
            'metrics' => $metrics,
            'retry_after' => 300,
        ], 503);
    }
}
