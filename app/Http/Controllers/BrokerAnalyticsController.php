<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BrokerAnalyticsService;
use App\Traits\Sortable;
use Illuminate\Support\Facades\DB;

class BrokerAnalyticsController extends Controller
{
    use Sortable;

    protected $brokerService;

    public function __construct(BrokerAnalyticsService $brokerService)
    {
        $this->brokerService = $brokerService;
    }

    /**
     * Show broker comparison analytics
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $days = $request->get('days', 30);
        $displayCurrency = $user->display_currency ?? 'USD';

        $analytics = $this->brokerService->getBrokerComparison($days, $displayCurrency);

        return view('broker-analytics.index', [
            'analytics' => $analytics,
            'days' => $days,
            'displayCurrency' => $displayCurrency,
        ]);
    }
}