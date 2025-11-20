<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateConversion;
use App\Models\AffiliatePayout;
use Illuminate\Http\Request;

class AffiliateManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Affiliate::query()->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $affiliates = $query->orderBy('created_at', 'desc')->paginate(50);

        $stats = [
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::where('is_active', true)->count(),
            'total_clicks' => Affiliate::sum('total_clicks'),
            'total_conversions' => AffiliateConversion::count(),
            'pending_payouts' => AffiliatePayout::where('status', 'pending')->count(),
        ];

        return view('admin.affiliates.index', compact('affiliates', 'stats'));
    }

    public function show(Affiliate $affiliate)
    {
        $affiliate->load(['clicks' => function($q) {
            $q->orderBy('clicked_at', 'desc')->limit(50);
        }, 'conversions' => function($q) {
            $q->orderBy('converted_at', 'desc')->limit(50);
        }, 'payouts' => function($q) {
            $q->orderBy('requested_at', 'desc');
        }]);

        return view('admin.affiliates.show', compact('affiliate'));
    }

    public function toggleStatus(Affiliate $affiliate)
    {
        $affiliate->update(['is_active' => !$affiliate->is_active]);

        return back()->with('success', 'Affiliate status updated successfully');
    }

    public function conversions(Request $request)
    {
        $query = AffiliateConversion::with(['affiliate', 'user', 'click']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('suspicious')) {
            $query->where('is_suspicious', true);
        }

        $conversions = $query->orderBy('converted_at', 'desc')->paginate(50);

        $stats = [
            'pending' => AffiliateConversion::where('status', 'pending')->count(),
            'approved' => AffiliateConversion::where('status', 'approved')->count(),
            'rejected' => AffiliateConversion::where('status', 'rejected')->count(),
            'suspicious' => AffiliateConversion::where('is_suspicious', true)->count(),
        ];

        return view('admin.affiliates.conversions', compact('conversions', 'stats'));
    }

    public function approveConversion(AffiliateConversion $conversion)
    {
        $conversion->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Update affiliate earnings
        $conversion->affiliate->increment('approved_earnings', $conversion->commission_amount);
        $conversion->affiliate->decrement('pending_earnings', $conversion->commission_amount);

        return back()->with('success', 'Conversion approved successfully');
    }

    public function rejectConversion(AffiliateConversion $conversion, Request $request)
    {
        $conversion->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'fraud_notes' => $request->input('reason', 'Rejected by admin'),
        ]);

        // Update affiliate earnings
        $conversion->affiliate->decrement('pending_earnings', $conversion->commission_amount);

        return back()->with('success', 'Conversion rejected');
    }

    public function payouts(Request $request)
    {
        $query = AffiliatePayout::with('affiliate');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->orderBy('requested_at', 'desc')->paginate(50);

        $stats = [
            'pending' => AffiliatePayout::where('status', 'pending')->count(),
            'processing' => AffiliatePayout::where('status', 'processing')->count(),
            'completed' => AffiliatePayout::where('status', 'completed')->count(),
            'total_amount' => AffiliatePayout::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.affiliates.payouts', compact('payouts', 'stats'));
    }

    public function processPayout(AffiliatePayout $payout, Request $request)
    {
        $request->validate([
            'transaction_hash' => 'required|string|max:255',
        ]);

        $payout->update([
            'status' => 'completed',
            'transaction_hash' => $request->transaction_hash,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Update affiliate earnings
        $payout->affiliate->decrement('approved_earnings', $payout->amount);
        $payout->affiliate->increment('total_paid', $payout->amount);

        return back()->with('success', 'Payout processed successfully');
    }

    public function rejectPayout(AffiliatePayout $payout, Request $request)
    {
        $payout->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason', 'Rejected by admin'),
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payout rejected');
    }
}
