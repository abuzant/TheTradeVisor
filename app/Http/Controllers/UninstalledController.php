<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UninstalledController extends Controller
{
    /**
     * Show the uninstall page
     */
    public function index()
    {
        // Track page view
        Log::info('Uninstall page viewed', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'timestamp' => now()->toIso8601String()
        ]);

        return view('uninstalled');
    }

    /**
     * Handle uninstall feedback submission
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'would_return' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'comments' => 'nullable|string|max:1000'
        ]);

        // Log the feedback for analysis
        Log::info('Uninstall feedback received', [
            'reason' => $validated['reason'],
            'experience' => $validated['experience'],
            'would_return' => $validated['would_return'],
            'has_email' => !empty($validated['email']),
            'has_comments' => !empty($validated['comments']),
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String()
        ]);

        // Store in database for future analysis
        \App\Models\UninstallFeedback::create([
            'reason' => $validated['reason'],
            'experience_rating' => $validated['experience'],
            'would_return' => $validated['would_return'],
            'email' => $validated['email'],
            'comments' => $validated['comments'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback. We\'ll use it to improve our service.'
        ]);
    }

    /**
     * Track special offer clicks
     */
    public function trackOfferClick(Request $request)
    {
        $offer = $request->input('offer', 'unknown');
        
        Log::info('Uninstall page offer clicked', [
            'offer' => $offer,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String()
        ]);

        return response()->json(['success' => true]);
    }
}
