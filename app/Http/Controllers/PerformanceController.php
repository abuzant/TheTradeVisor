<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PerformanceMetricsService;

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
        $days = $request->get('days', 30); // Default to 30 days
        $displayCurrency = $user->display_currency ?? 'USD';

        // Get all user's account IDs
        $accountIds = $user->tradingAccounts()->pluck('id')->toArray();

        if (empty($accountIds)) {
            return view('performance.index', [
                'hasAccounts' => false,
                'days' => $days,
                'displayCurrency' => $displayCurrency,
            ]);
        }

        // Get performance metrics with display currency
        $metrics = $this->metricsService->getPerformanceMetrics($accountIds, $days, $displayCurrency);

        return view('performance.index', [
            'hasAccounts' => true,
            'metrics' => $metrics,
            'days' => $days,
            'user' => $user,
            'displayCurrency' => $displayCurrency,
        ]);
    }
}
