<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateAnalytic;
use App\Models\AffiliateClick;
use App\Models\AffiliateConversion;
use Illuminate\Support\Facades\DB;

class AffiliateAnalyticsService
{
    public function aggregateDailyAnalytics(Affiliate $affiliate, \DateTime $date): AffiliateAnalytic
    {
        $dateString = $date->format('Y-m-d');
        
        // Get clicks for the day
        $clicks = AffiliateClick::where('affiliate_id', $affiliate->id)
            ->whereDate('clicked_at', $dateString)
            ->get();
        
        $totalClicks = $clicks->count();
        $uniqueClicks = $clicks->unique('ip_address')->count();
        
        // Get conversions for the day
        $conversions = AffiliateConversion::where('affiliate_id', $affiliate->id)
            ->whereDate('converted_at', $dateString)
            ->get();
        
        $signups = $clicks->where('converted', true)->count();
        $paidSignups = $conversions->whereIn('status', ['approved', 'paid'])->count();
        $earnings = $conversions->whereIn('status', ['approved', 'paid'])->sum('commission_amount');
        
        // Calculate conversion rates
        $clickToSignupRate = $totalClicks > 0 ? round(($signups / $totalClicks) * 100, 2) : 0;
        $signupToPaidRate = $signups > 0 ? round(($paidSignups / $signups) * 100, 2) : 0;
        
        // Get top country and UTM source
        $topCountry = $clicks->whereNotNull('country_code')
            ->groupBy('country_code')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();
        
        $topUtmSource = $clicks->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();
        
        // Create or update analytics record
        return AffiliateAnalytic::updateOrCreate(
            [
                'affiliate_id' => $affiliate->id,
                'date' => $dateString,
            ],
            [
                'clicks' => $totalClicks,
                'unique_clicks' => $uniqueClicks,
                'signups' => $signups,
                'paid_signups' => $paidSignups,
                'earnings' => $earnings,
                'click_to_signup_rate' => $clickToSignupRate,
                'signup_to_paid_rate' => $signupToPaidRate,
                'top_country_code' => $topCountry,
                'top_utm_source' => $topUtmSource,
            ]
        );
    }
    
    public function getPerformanceMetrics(Affiliate $affiliate, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $analytics = AffiliateAnalytic::where('affiliate_id', $affiliate->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date', 'asc')
            ->get();
        
        return [
            'total_clicks' => $analytics->sum('clicks'),
            'total_unique_clicks' => $analytics->sum('unique_clicks'),
            'total_signups' => $analytics->sum('signups'),
            'total_paid_signups' => $analytics->sum('paid_signups'),
            'total_earnings' => $analytics->sum('earnings'),
            'avg_click_to_signup_rate' => $analytics->avg('click_to_signup_rate'),
            'avg_signup_to_paid_rate' => $analytics->avg('signup_to_paid_rate'),
            'daily_data' => $analytics->map(function ($analytic) {
                return [
                    'date' => $analytic->date->format('Y-m-d'),
                    'clicks' => $analytic->clicks,
                    'signups' => $analytic->signups,
                    'paid_signups' => $analytic->paid_signups,
                    'earnings' => $analytic->earnings,
                ];
            }),
        ];
    }
    
    public function getTopPerformingCampaigns(Affiliate $affiliate, int $limit = 10): array
    {
        return AffiliateClick::where('affiliate_id', $affiliate->id)
            ->whereNotNull('utm_campaign')
            ->select('utm_campaign', 'utm_source', 'utm_medium')
            ->selectRaw('COUNT(*) as clicks')
            ->selectRaw('SUM(CASE WHEN converted = true THEN 1 ELSE 0 END) as conversions')
            ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
            ->orderByDesc('conversions')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    
    public function getGeographicDistribution(Affiliate $affiliate): array
    {
        return AffiliateClick::where('affiliate_id', $affiliate->id)
            ->whereNotNull('country_code')
            ->select('country_code')
            ->selectRaw('COUNT(*) as clicks')
            ->selectRaw('SUM(CASE WHEN converted = true THEN 1 ELSE 0 END) as conversions')
            ->groupBy('country_code')
            ->orderByDesc('clicks')
            ->limit(20)
            ->get()
            ->toArray();
    }
}
