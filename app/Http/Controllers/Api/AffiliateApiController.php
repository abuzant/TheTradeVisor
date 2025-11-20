<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Services\AffiliateAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateApiController extends Controller
{
    protected $analyticsService;

    public function __construct(AffiliateAnalyticsService $analyticsService)
    {
        $this->middleware('auth:affiliate');
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get affiliate statistics
     * GET /api/affiliate/stats
     */
    public function stats(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $days = $request->input('days', 30);

        $metrics = $this->analyticsService->getPerformanceMetrics($affiliate, $days);

        return response()->json([
            'success' => true,
            'data' => [
                'total_clicks' => $metrics['total_clicks'],
                'total_unique_clicks' => $metrics['total_unique_clicks'],
                'total_signups' => $metrics['total_signups'],
                'total_paid_signups' => $metrics['total_paid_signups'],
                'total_earnings' => $metrics['total_earnings'],
                'conversion_rate' => $metrics['avg_click_to_signup_rate'],
                'paid_conversion_rate' => $metrics['avg_signup_to_paid_rate'],
            ]
        ]);
    }

    /**
     * Get affiliate performance data
     * GET /api/affiliate/performance
     */
    public function performance(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $days = $request->input('days', 30);

        $metrics = $this->analyticsService->getPerformanceMetrics($affiliate, $days);

        return response()->json([
            'success' => true,
            'data' => $metrics['daily_data']
        ]);
    }

    /**
     * Get top campaigns
     * GET /api/affiliate/campaigns
     */
    public function campaigns(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $limit = $request->input('limit', 10);

        $campaigns = $this->analyticsService->getTopPerformingCampaigns($affiliate, $limit);

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Get geographic distribution
     * GET /api/affiliate/geo
     */
    public function geographic(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();

        $distribution = $this->analyticsService->getGeographicDistribution($affiliate);

        return response()->json([
            'success' => true,
            'data' => $distribution
        ]);
    }

    /**
     * Get recent clicks
     * GET /api/affiliate/clicks
     */
    public function clicks(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $limit = $request->input('limit', 50);

        $clicks = $affiliate->clicks()
            ->orderBy('clicked_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($click) {
                return [
                    'id' => $click->id,
                    'clicked_at' => $click->clicked_at->toIso8601String(),
                    'country_code' => $click->country_code,
                    'city' => $click->city,
                    'utm_source' => $click->utm_source,
                    'utm_campaign' => $click->utm_campaign,
                    'converted' => $click->converted,
                    'converted_at' => $click->converted_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $clicks
        ]);
    }

    /**
     * Get recent conversions
     * GET /api/affiliate/conversions
     */
    public function conversions(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $limit = $request->input('limit', 50);

        $conversions = $affiliate->conversions()
            ->orderBy('converted_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($conversion) {
                return [
                    'id' => $conversion->id,
                    'converted_at' => $conversion->converted_at->toIso8601String(),
                    'commission_amount' => $conversion->commission_amount,
                    'commission_currency' => $conversion->commission_currency,
                    'status' => $conversion->status,
                    'is_suspicious' => $conversion->is_suspicious,
                    'fraud_score' => $conversion->fraud_score,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversions
        ]);
    }

    /**
     * Get payout history
     * GET /api/affiliate/payouts
     */
    public function payouts(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();

        $payouts = $affiliate->payouts()
            ->orderBy('requested_at', 'desc')
            ->get()
            ->map(function ($payout) {
                return [
                    'id' => $payout->id,
                    'requested_at' => $payout->requested_at->toIso8601String(),
                    'amount' => $payout->amount,
                    'usdt_amount' => $payout->usdt_amount,
                    'currency' => $payout->currency,
                    'status' => $payout->status,
                    'processed_at' => $payout->processed_at?->toIso8601String(),
                    'transaction_hash' => $payout->transaction_hash,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payouts
        ]);
    }

    /**
     * Get affiliate profile
     * GET /api/affiliate/profile
     */
    public function profile()
    {
        $affiliate = Auth::guard('affiliate')->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $affiliate->id,
                'username' => $affiliate->username,
                'email' => $affiliate->email,
                'slug' => $affiliate->slug,
                'referral_url' => $affiliate->referral_url,
                'is_active' => $affiliate->is_active,
                'is_verified' => $affiliate->is_verified,
                'total_clicks' => $affiliate->total_clicks,
                'total_signups' => $affiliate->total_signups,
                'paid_signups' => $affiliate->paid_signups,
                'pending_earnings' => $affiliate->pending_earnings,
                'approved_earnings' => $affiliate->approved_earnings,
                'total_paid' => $affiliate->total_paid,
                'total_earnings' => $affiliate->total_earnings,
                'usdt_wallet_address' => $affiliate->usdt_wallet_address,
                'wallet_type' => $affiliate->wallet_type,
                'created_at' => $affiliate->created_at->toIso8601String(),
            ]
        ]);
    }
}
