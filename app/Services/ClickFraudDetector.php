<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateClick;
use App\Models\AffiliateConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClickFraudDetector
{
    public function analyzeClick(Request $request, Affiliate $affiliate): array
    {
        $fraudScore = 0;
        $flags = [];
        
        // Check 1: IP-based detection (excessive clicks from same IP)
        $recentClicks = AffiliateClick::where('affiliate_id', $affiliate->id)
            ->where('ip_address', $request->ip())
            ->where('clicked_at', '>', now()->subHours(24))
            ->count();
        
        if ($recentClicks > 50) {
            $fraudScore += 30;
            $flags[] = 'excessive_clicks_same_ip';
        }
        
        // Check 2: Browser fingerprint duplication
        $fingerprint = $this->generateFingerprint($request);
        $duplicateFingerprints = AffiliateClick::where('fingerprint', $fingerprint)
            ->where('clicked_at', '>', now()->subHours(1))
            ->count();
        
        if ($duplicateFingerprints > 10) {
            $fraudScore += 25;
            $flags[] = 'duplicate_fingerprint';
        }
        
        // Check 3: Self-referral detection
        if ($affiliate->user_id && Auth::check() && Auth::id() === $affiliate->user_id) {
            $fraudScore += 50;
            $flags[] = 'self_referral';
        }
        
        // Check 4: Bot detection
        if ($this->isBot($request->userAgent())) {
            $fraudScore += 40;
            $flags[] = 'bot_detected';
        }
        
        // Check 5: Suspicious conversion pattern (rapid conversions)
        $quickConversions = AffiliateConversion::where('affiliate_id', $affiliate->id)
            ->where('converted_at', '>', now()->subMinutes(5))
            ->count();
        
        if ($quickConversions > 3) {
            $fraudScore += 35;
            $flags[] = 'rapid_conversions';
        }
        
        // Check 6: Empty or suspicious referrer
        if (empty($request->header('referer')) && $request->header('user-agent')) {
            $fraudScore += 10;
            $flags[] = 'no_referrer';
        }
        
        return [
            'fraud_score' => min($fraudScore, 100),
            'is_suspicious' => $fraudScore >= 50,
            'flags' => $flags,
            'fingerprint' => $fingerprint
        ];
    }
    
    public function generateFingerprint(Request $request): string
    {
        $data = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->header('Accept'),
            $request->header('DNT'),
        ];
        
        return hash('sha256', implode('|', array_filter($data)));
    }
    
    private function isBot(string $userAgent): bool
    {
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python', 'java', 'http', 'libwww', 'perl', 'headless',
            'phantom', 'selenium', 'puppeteer'
        ];
        
        $userAgentLower = strtolower($userAgent);
        
        foreach ($botPatterns as $pattern) {
            if (stripos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    public function analyzeConversion(AffiliateClick $click, int $userId): array
    {
        $fraudScore = $click->fingerprint ? 0 : 20;
        $flags = [];
        
        // Check 1: Time between click and conversion (too fast = suspicious)
        $timeDiff = now()->diffInMinutes($click->clicked_at);
        if ($timeDiff < 1) {
            $fraudScore += 30;
            $flags[] = 'instant_conversion';
        }
        
        // Check 2: IP mismatch between click and signup
        // This would need to be checked at registration time
        
        // Check 3: Multiple conversions from same IP
        $ipConversions = AffiliateConversion::whereHas('click', function($query) use ($click) {
            $query->where('ip_address', $click->ip_address);
        })->where('converted_at', '>', now()->subDays(7))->count();
        
        if ($ipConversions > 3) {
            $fraudScore += 25;
            $flags[] = 'multiple_conversions_same_ip';
        }
        
        return [
            'fraud_score' => min($fraudScore, 100),
            'is_suspicious' => $fraudScore >= 50,
            'flags' => $flags
        ];
    }
}
