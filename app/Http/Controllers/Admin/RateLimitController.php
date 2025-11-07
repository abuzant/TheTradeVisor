<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RateLimitSetting;
use App\Services\RateLimiterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateLimitController extends Controller
{
    protected RateLimiterService $rateLimiter;

    public function __construct(RateLimiterService $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Display rate limit settings
     */
    public function index()
    {
        $settings = RateLimitSetting::orderBy('type')->orderBy('key')->get();
        
        return view('admin.rate-limits.index', compact('settings'));
    }

    /**
     * Update a rate limit setting
     */
    public function update(Request $request, RateLimitSetting $setting)
    {
        $request->validate([
            'value' => 'required|integer|min:1|max:10000',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $setting->update([
            'value' => $request->value,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Clear cache to apply new limits immediately
        Cache::flush();

        return redirect()
            ->route('admin.rate-limits.index')
            ->with('success', 'Rate limit updated successfully');
    }

    /**
     * Create a new rate limit setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:rate_limit_settings,key|max:255',
            'value' => 'required|integer|min:1|max:10000',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:global,ip,api_key,user',
        ]);

        RateLimitSetting::create($request->all());

        return redirect()
            ->route('admin.rate-limits.index')
            ->with('success', 'Rate limit created successfully');
    }

    /**
     * Delete a rate limit setting
     */
    public function destroy(RateLimitSetting $setting)
    {
        // Prevent deletion of core settings
        $coreSettings = ['global_ip_limit', 'global_api_key_limit', 'burst_limit'];
        
        if (in_array($setting->key, $coreSettings)) {
            return redirect()
                ->route('admin.rate-limits.index')
                ->with('error', 'Cannot delete core rate limit settings');
        }

        $setting->delete();

        return redirect()
            ->route('admin.rate-limits.index')
            ->with('success', 'Rate limit deleted successfully');
    }

    /**
     * Toggle rate limit active status
     */
    public function toggle(RateLimitSetting $setting)
    {
        $setting->update(['is_active' => !$setting->is_active]);

        return redirect()
            ->route('admin.rate-limits.index')
            ->with('success', 'Rate limit status updated');
    }

    /**
     * Clear all rate limit caches
     */
    public function clearCache()
    {
        Cache::flush();

        return redirect()
            ->route('admin.rate-limits.index')
            ->with('success', 'Rate limit cache cleared successfully');
    }

    /**
     * View rate limit statistics
     */
    public function statistics()
    {
        // Get rate limit hits from logs
        $stats = [
            'total_requests' => 0,
            'blocked_requests' => 0,
            'top_ips' => [],
            'top_api_keys' => [],
        ];

        return view('admin.rate-limits.statistics', compact('stats'));
    }
}
