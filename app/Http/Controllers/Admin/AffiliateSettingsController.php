<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AffiliateSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $settings = $this->getSettings();
        
        return view('admin.affiliates.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'commission_amount' => 'required|numeric|min:0|max:100',
            'minimum_payout' => 'required|numeric|min:10|max:1000',
            'cookie_duration_days' => 'required|integer|min:1|max:365',
            'cooling_period_days' => 'required|integer|min:0|max:90',
            'fraud_threshold' => 'required|integer|min:0|max:100',
            'auto_approve_enabled' => 'boolean',
            'auto_approve_threshold' => 'required|integer|min:0|max:100',
            'rate_limit_clicks' => 'required|integer|min:1|max:100',
            'ip_fraud_score' => 'required|integer|min:0|max:100',
            'fingerprint_fraud_score' => 'required|integer|min:0|max:100',
            'self_referral_fraud_score' => 'required|integer|min:0|max:100',
            'bot_fraud_score' => 'required|integer|min:0|max:100',
            'rapid_conversion_fraud_score' => 'required|integer|min:0|max:100',
            'no_referrer_fraud_score' => 'required|integer|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            Cache::forever("affiliate_setting_{$key}", $value);
        }

        return back()->with('success', 'Affiliate settings updated successfully');
    }

    private function getSettings()
    {
        return [
            'commission_amount' => Cache::get('affiliate_setting_commission_amount', 1.99),
            'minimum_payout' => Cache::get('affiliate_setting_minimum_payout', 50.00),
            'cookie_duration_days' => Cache::get('affiliate_setting_cookie_duration_days', 30),
            'cooling_period_days' => Cache::get('affiliate_setting_cooling_period_days', 7),
            'fraud_threshold' => Cache::get('affiliate_setting_fraud_threshold', 50),
            'auto_approve_enabled' => Cache::get('affiliate_setting_auto_approve_enabled', false),
            'auto_approve_threshold' => Cache::get('affiliate_setting_auto_approve_threshold', 25),
            'rate_limit_clicks' => Cache::get('affiliate_setting_rate_limit_clicks', 10),
            'ip_fraud_score' => Cache::get('affiliate_setting_ip_fraud_score', 30),
            'fingerprint_fraud_score' => Cache::get('affiliate_setting_fingerprint_fraud_score', 25),
            'self_referral_fraud_score' => Cache::get('affiliate_setting_self_referral_fraud_score', 50),
            'bot_fraud_score' => Cache::get('affiliate_setting_bot_fraud_score', 40),
            'rapid_conversion_fraud_score' => Cache::get('affiliate_setting_rapid_conversion_fraud_score', 35),
            'no_referrer_fraud_score' => Cache::get('affiliate_setting_no_referrer_fraud_score', 10),
        ];
    }
}
