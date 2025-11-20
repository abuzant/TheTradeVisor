<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Services\AffiliateTrackingService;
use Illuminate\Http\Request;

class AffiliateTrackingController extends Controller
{
    protected $trackingService;
    
    public function __construct(AffiliateTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }
    
    public function track(Request $request, string $slug)
    {
        // Find affiliate by slug
        $affiliate = Affiliate::where('slug', $slug)
            ->where('is_active', true)
            ->first();
        
        if (!$affiliate) {
            abort(404, 'Affiliate link not found');
        }
        
        // Track the click
        $click = $this->trackingService->trackClick($request, $affiliate);
        
        // Set affiliate cookie
        $cookie = $this->trackingService->setAffiliateCookie($slug);
        
        // Redirect to main registration page
        $redirectUrl = route('register', ['ref' => $slug]);
        
        return redirect($redirectUrl)->cookie($cookie);
    }
}
