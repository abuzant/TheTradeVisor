<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateClick;
use App\Models\AffiliateConversion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AffiliateTrackingService
{
    protected $fraudDetector;
    
    public function __construct(ClickFraudDetector $fraudDetector)
    {
        $this->fraudDetector = $fraudDetector;
    }
    
    public function trackClick(Request $request, Affiliate $affiliate): AffiliateClick
    {
        // Run fraud detection
        $fraudAnalysis = $this->fraudDetector->analyzeClick($request, $affiliate);
        
        // Get geolocation data (if MaxMind is available)
        $geoData = $this->getGeolocation($request->ip());
        
        // Create click record
        $click = AffiliateClick::create([
            'affiliate_id' => $affiliate->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'landing_page' => $request->fullUrl(),
            'country_code' => $geoData['country_code'] ?? null,
            'city' => $geoData['city'] ?? null,
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content' => $request->query('utm_content'),
            'utm_term' => $request->query('utm_term'),
            'session_id' => $this->getOrCreateSessionId($request),
            'fingerprint' => $fraudAnalysis['fingerprint'],
        ]);
        
        // Update affiliate statistics
        $affiliate->increment('total_clicks');
        
        return $click;
    }
    
    public function setAffiliateCookie(string $affiliateSlug): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::make(
            'affiliate_ref',
            $affiliateSlug,
            60 * 24 * 30, // 30 days
            '/',
            null,
            true, // secure
            true, // httpOnly
            false,
            'lax'
        );
    }
    
    public function getAffiliateFromCookie(Request $request): ?Affiliate
    {
        $slug = $request->cookie('affiliate_ref');
        
        if (!$slug) {
            return null;
        }
        
        return Affiliate::where('slug', $slug)->where('is_active', true)->first();
    }
    
    public function trackConversion(User $user, string $subscriptionTier): ?AffiliateConversion
    {
        // Check if user was referred by an affiliate
        if (!$user->referred_by_affiliate_id) {
            return null;
        }
        
        $affiliate = Affiliate::find($user->referred_by_affiliate_id);
        
        if (!$affiliate) {
            return null;
        }
        
        // Find the original click
        $click = AffiliateClick::where('affiliate_id', $affiliate->id)
            ->where('conversion_user_id', $user->id)
            ->first();
        
        if (!$click) {
            return null;
        }
        
        // Run fraud detection on conversion
        $fraudAnalysis = $this->fraudDetector->analyzeConversion($click, $user->id);
        
        // Create conversion record
        $conversion = AffiliateConversion::create([
            'affiliate_id' => $affiliate->id,
            'click_id' => $click->id,
            'user_id' => $user->id,
            'subscription_tier' => $subscriptionTier,
            'commission_amount' => 1.99, // Fixed commission
            'commission_currency' => 'USD',
            'status' => $fraudAnalysis['is_suspicious'] ? 'pending' : 'pending', // All start as pending
            'is_suspicious' => $fraudAnalysis['is_suspicious'],
            'fraud_score' => $fraudAnalysis['fraud_score'],
            'fraud_notes' => !empty($fraudAnalysis['flags']) ? implode(', ', $fraudAnalysis['flags']) : null,
        ]);
        
        // Update click as converted
        $click->update([
            'converted' => true,
            'converted_at' => now(),
        ]);
        
        // Update affiliate statistics
        $affiliate->increment('total_signups');
        $affiliate->increment('pending_earnings', 1.99);
        
        return $conversion;
    }
    
    protected function getOrCreateSessionId(Request $request): string
    {
        $sessionId = $request->session()->getId();
        
        if (!$sessionId) {
            $sessionId = Str::random(64);
        }
        
        return $sessionId;
    }
    
    protected function getGeolocation(string $ip): array
    {
        // This would integrate with MaxMind GeoIP2
        // For now, return empty array
        // TODO: Implement MaxMind integration
        
        return [
            'country_code' => null,
            'city' => null,
        ];
    }
}
