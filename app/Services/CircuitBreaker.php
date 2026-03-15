<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    private string $serviceName;
    private int $failureThreshold;
    private int $timeout;
    private int $retryTimeout;

    /**
     * Create a new Circuit Breaker instance
     *
     * @param string $serviceName Unique name for the service
     * @param int $failureThreshold Number of failures before opening circuit
     * @param int $timeout Timeout for service calls in seconds
     * @param int $retryTimeout Time to wait before retrying in seconds
     */
    public function __construct(
        string $serviceName,
        int $failureThreshold = 5,
        int $timeout = 10,
        int $retryTimeout = 60
    ) {
        $this->serviceName = $serviceName;
        $this->failureThreshold = $failureThreshold;
        $this->timeout = $timeout;
        $this->retryTimeout = $retryTimeout;
    }

    /**
     * Execute a callback with circuit breaker protection
     *
     * @param callable $callback Primary function to execute
     * @param callable|null $fallback Fallback function if circuit is open
     * @return mixed
     * @throws \Exception
     */
    public function call(callable $callback, callable $fallback = null)
    {
        $state = $this->getState();

        // If circuit is open, use fallback or return null
        if ($state === 'open') {
            Log::warning("Circuit breaker OPEN for {$this->serviceName}", [
                'failures' => $this->getFailureCount(),
                'open_until' => $this->getOpenUntil(),
            ]);

            if ($fallback) {
                return $fallback();
            }

            throw new \Exception("Circuit breaker is OPEN for service: {$this->serviceName}");
        }

        // If circuit is half-open, try the call
        if ($state === 'half-open') {
            Log::info("Circuit breaker HALF-OPEN for {$this->serviceName}, attempting call");
        }

        try {
            // Execute the callback
            $result = $callback();

            // Success! Reset the circuit
            $this->recordSuccess();

            return $result;
        } catch (\Exception $e) {
            // Failure! Record it
            $this->recordFailure();

            Log::error("Circuit breaker failure for {$this->serviceName}", [
                'error' => $e->getMessage(),
                'failures' => $this->getFailureCount(),
                'threshold' => $this->failureThreshold,
            ]);

            // Use fallback if available
            if ($fallback) {
                Log::info("Using fallback for {$this->serviceName}");
                return $fallback();
            }

            // Re-throw the exception
            throw $e;
        }
    }

    /**
     * Get the current state of the circuit breaker
     *
     * @return string 'closed', 'open', or 'half-open'
     */
    private function getState(): string
    {
        $failures = $this->getFailureCount();
        $openUntil = $this->getOpenUntil();

        // If open_until is set and not expired, circuit is open
        if ($openUntil && time() < $openUntil) {
            return 'open';
        }

        // If open_until expired, circuit is half-open (trying to recover)
        if ($openUntil && time() >= $openUntil) {
            return 'half-open';
        }

        // If failures exceed threshold, open the circuit
        if ($failures >= $this->failureThreshold) {
            $this->openCircuit();
            return 'open';
        }

        // Otherwise, circuit is closed (normal operation)
        return 'closed';
    }

    /**
     * Open the circuit breaker
     */
    private function openCircuit(): void
    {
        Cache::put(
            "circuit_breaker:{$this->serviceName}:open_until",
            time() + $this->retryTimeout,
            $this->retryTimeout + 60
        );

        Log::warning("Circuit breaker OPENED for {$this->serviceName}", [
            'failures' => $this->getFailureCount(),
            'retry_in' => $this->retryTimeout,
        ]);
    }

    /**
     * Record a successful call
     */
    private function recordSuccess(): void
    {
        Cache::forget("circuit_breaker:{$this->serviceName}:failures");
        Cache::forget("circuit_breaker:{$this->serviceName}:open_until");

        Log::info("Circuit breaker SUCCESS for {$this->serviceName}, circuit closed");
    }

    /**
     * Record a failed call
     */
    private function recordFailure(): void
    {
        $failures = $this->getFailureCount() + 1;

        Cache::put(
            "circuit_breaker:{$this->serviceName}:failures",
            $failures,
            300 // Keep failure count for 5 minutes
        );
    }

    /**
     * Get current failure count
     */
    private function getFailureCount(): int
    {
        return Cache::get("circuit_breaker:{$this->serviceName}:failures", 0);
    }

    /**
     * Get timestamp when circuit will attempt to close
     */
    private function getOpenUntil(): ?int
    {
        return Cache::get("circuit_breaker:{$this->serviceName}:open_until");
    }

    /**
     * Get the current status of the circuit breaker
     *
     * @return array
     */
    public function getStatus(): array
    {
        $state = $this->getState();
        $failures = $this->getFailureCount();
        $openUntil = $this->getOpenUntil();

        return [
            'service' => $this->serviceName,
            'state' => $state,
            'failures' => $failures,
            'threshold' => $this->failureThreshold,
            'open_until' => $openUntil,
            'open_until_human' => $openUntil ? date('Y-m-d H:i:s', $openUntil) : null,
            'retry_timeout' => $this->retryTimeout,
            'healthy' => $state === 'closed',
        ];
    }

    /**
     * Manually reset the circuit breaker
     */
    public function reset(): void
    {
        Cache::forget("circuit_breaker:{$this->serviceName}:failures");
        Cache::forget("circuit_breaker:{$this->serviceName}:open_until");

        Log::info("Circuit breaker MANUALLY RESET for {$this->serviceName}");
    }

    /**
     * Get all circuit breakers status
     *
     * @param array $services List of service names to check
     * @return array
     */
    public static function getAllStatus(array $services): array
    {
        $statuses = [];

        foreach ($services as $service) {
            $breaker = new self($service);
            $statuses[$service] = $breaker->getStatus();
        }

        return $statuses;
    }
}
