<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Services\AffiliateAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateDashboardController extends Controller
{
    protected $analyticsService;
    
    public function __construct(AffiliateAnalyticsService $analyticsService)
    {
        $this->middleware('auth:affiliate');
        $this->analyticsService = $analyticsService;
    }
    
    public function index(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        // Get performance metrics for last 30 days
        $metrics = $this->analyticsService->getPerformanceMetrics($affiliate, 30);
        
        // Get recent clicks (last 10)
        $recentClicks = $affiliate->clicks()
            ->with('conversionUser')
            ->orderBy('clicked_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent conversions (last 10)
        $recentConversions = $affiliate->conversions()
            ->with('user')
            ->orderBy('converted_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get top campaigns
        $topCampaigns = $this->analyticsService->getTopPerformingCampaigns($affiliate, 5);
        
        return view('affiliate.dashboard', compact(
            'affiliate',
            'metrics',
            'recentClicks',
            'recentConversions',
            'topCampaigns'
        ));
    }
    
    public function analytics(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        $days = $request->input('days', 30);
        
        $metrics = $this->analyticsService->getPerformanceMetrics($affiliate, $days);
        $geoDistribution = $this->analyticsService->getGeographicDistribution($affiliate);
        $topCampaigns = $this->analyticsService->getTopPerformingCampaigns($affiliate, 10);
        
        return view('affiliate.analytics', compact(
            'affiliate',
            'metrics',
            'geoDistribution',
            'topCampaigns',
            'days'
        ));
    }
    
    public function links(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        return view('affiliate.links', compact('affiliate'));
    }
    
    public function payouts(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        $payouts = $affiliate->payouts()
            ->orderBy('requested_at', 'desc')
            ->paginate(20);
        
        $pendingConversions = $affiliate->conversions()
            ->approved()
            ->whereDoesntHave('payout')
            ->get();
        
        return view('affiliate.payouts', compact('affiliate', 'payouts', 'pendingConversions'));
    }
    
    public function requestPayout(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        if (!$affiliate->canRequestPayout()) {
            return back()->with('error', 'You do not meet the minimum payout threshold or have not set up your wallet.');
        }
        
        // Get all approved conversions that haven't been paid
        $conversions = $affiliate->conversions()
            ->where('status', 'approved')
            ->get();
        
        if ($conversions->isEmpty()) {
            return back()->with('error', 'No approved conversions available for payout.');
        }
        
        $totalAmount = $conversions->sum('commission_amount');
        
        $payout = $affiliate->payouts()->create([
            'amount' => $totalAmount,
            'currency' => 'USD',
            'usdt_amount' => $totalAmount, // 1:1 for now
            'wallet_address' => $affiliate->usdt_wallet_address,
            'wallet_type' => $affiliate->wallet_type,
            'conversion_ids' => $conversions->pluck('id')->toArray(),
            'conversion_count' => $conversions->count(),
            'status' => 'pending',
        ]);
        
        // Update conversions to paid status
        $conversions->each->update(['status' => 'paid']);
        
        // Update affiliate earnings
        $affiliate->updateStatistics();
        
        return back()->with('success', 'Payout request submitted successfully!');
    }
    
    public function settings(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        return view('affiliate.settings', compact('affiliate'));
    }
    
    public function updateSettings(Request $request)
    {
        $affiliate = Auth::guard('affiliate')->user();
        
        $validated = $request->validate([
            'usdt_wallet_address' => ['nullable', 'string', 'max:255'],
            'wallet_type' => ['nullable', 'in:TRC20,ERC20'],
            'payment_threshold' => ['nullable', 'numeric', 'min:50', 'max:1000'],
        ]);
        
        $affiliate->update($validated);
        
        return back()->with('success', 'Settings updated successfully!');
    }
}
