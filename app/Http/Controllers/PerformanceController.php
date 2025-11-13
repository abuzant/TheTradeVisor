<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PerformanceMetricsService;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    protected $metricsService;

    public function __construct(PerformanceMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Show performance metrics for the authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $period = $request->get('period', '30d'); // Default to 30 days
        // Multi-account context: Always use USD
        $displayCurrency = 'USD';

        // Get all user's account IDs
        $accountIds = $user->tradingAccounts()->pluck('id')->toArray();

        if (empty($accountIds)) {
            return view('performance.index', [
                'hasAccounts' => false,
                'period' => $period,
            ]);
        }

        // Map period to days and cache duration
        $periodConfig = $this->getPeriodConfig($period);
        $days = $periodConfig['days'];
        $cacheDuration = $periodConfig['cache_duration'];

        // Cache key: user + session + IP + period for security
        $sessionId = session()->getId();
        $userIp = $request->ip();
        $cacheKey = "performance.{$user->id}.{$sessionId}.{$userIp}.{$period}.usd";

        // Get performance metrics with caching based on period (always USD for multi-account)
        $metrics = Cache::remember($cacheKey, $cacheDuration, function() use ($accountIds, $days, $displayCurrency) {
            return $this->metricsService->getPerformanceMetrics($accountIds, $days, $displayCurrency);
        });

        return view('performance.index', [
            'hasAccounts' => true,
            'metrics' => $metrics,
            'period' => $period,
            'days' => $days,
            'user' => $user,
        ]);
    }

    /**
     * Get period configuration (days and cache duration)
     */
    private function getPeriodConfig($period)
    {
        $configs = [
            'today' => [
                'days' => 1,
                'cache_duration' => 300, // 5 minutes
            ],
            '7d' => [
                'days' => 7,
                'cache_duration' => 3600, // 1 hour
            ],
            '30d' => [
                'days' => 30,
                'cache_duration' => 14400, // 4 hours
            ],
            'all' => [
                'days' => 36500, // 100 years (effectively all time)
                'cache_duration' => 86400, // 1 day
            ],
        ];

        return $configs[$period] ?? $configs['30d'];
    }
}
