@extends('layouts.app')

@section('title', 'Affiliate Settings')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Affiliate Program Settings</h2>
            <a href="{{ route('admin.affiliates.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Back to Affiliates
            </a>
        </div>

        <form method="POST" action="{{ route('admin.affiliates.settings.update') }}">
            @csrf
            @method('PUT')

            <!-- Commission Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Commission Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commission Amount (USD)</label>
                            <input type="number" step="0.01" name="commission_amount" value="{{ old('commission_amount', $settings['commission_amount']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Amount paid per successful conversion</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Payout (USD)</label>
                            <input type="number" step="0.01" name="minimum_payout" value="{{ old('minimum_payout', $settings['minimum_payout']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Minimum balance required for payout request</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔗 Tracking Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cookie Duration (Days)</label>
                            <input type="number" name="cookie_duration_days" value="{{ old('cookie_duration_days', $settings['cookie_duration_days']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">How long affiliate cookie remains valid</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cooling Period (Days)</label>
                            <input type="number" name="cooling_period_days" value="{{ old('cooling_period_days', $settings['cooling_period_days']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Days before conversion eligible for approval</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rate Limit (Clicks/Minute)</label>
                            <input type="number" name="rate_limit_clicks" value="{{ old('rate_limit_clicks', $settings['rate_limit_clicks']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Maximum clicks per minute per IP</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fraud Detection Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Fraud Detection Settings</h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fraud Threshold</label>
                        <input type="number" name="fraud_threshold" value="{{ old('fraud_threshold', $settings['fraud_threshold']) }}" 
                            class="w-full rounded-md border-gray-300" required>
                        <p class="text-xs text-gray-500 mt-1">Score above this value marks conversion as suspicious (0-100)</p>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <label class="flex items-center">
                            <input type="checkbox" name="auto_approve_enabled" value="1" 
                                {{ old('auto_approve_enabled', $settings['auto_approve_enabled']) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable Auto-Approval</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Automatically approve conversions below threshold</p>
                        
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Auto-Approve Threshold</label>
                            <input type="number" name="auto_approve_threshold" value="{{ old('auto_approve_threshold', $settings['auto_approve_threshold']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Conversions with fraud score below this are auto-approved</p>
                        </div>
                    </div>

                    <h4 class="text-md font-semibold text-gray-800 mb-3">Fraud Score Weights</h4>
                    <p class="text-sm text-gray-600 mb-4">Configure how much each fraud indicator contributes to the total score</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">IP-Based Detection</label>
                            <input type="number" name="ip_fraud_score" value="{{ old('ip_fraud_score', $settings['ip_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for excessive clicks from same IP</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fingerprint Duplication</label>
                            <input type="number" name="fingerprint_fraud_score" value="{{ old('fingerprint_fraud_score', $settings['fingerprint_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for duplicate browser fingerprints</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Self-Referral</label>
                            <input type="number" name="self_referral_fraud_score" value="{{ old('self_referral_fraud_score', $settings['self_referral_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for affiliates referring themselves</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bot Detection</label>
                            <input type="number" name="bot_fraud_score" value="{{ old('bot_fraud_score', $settings['bot_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for detected bot traffic</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rapid Conversions</label>
                            <input type="number" name="rapid_conversion_fraud_score" value="{{ old('rapid_conversion_fraud_score', $settings['rapid_conversion_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for suspiciously fast conversions</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No Referrer</label>
                            <input type="number" name="no_referrer_fraud_score" value="{{ old('no_referrer_fraud_score', $settings['no_referrer_fraud_score']) }}" 
                                class="w-full rounded-md border-gray-300" required>
                            <p class="text-xs text-gray-500 mt-1">Score for missing referrer header</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Configuration Summary -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <h3 class="text-lg font-semibold mb-4">📊 Current Configuration Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="opacity-90">Commission</p>
                        <p class="text-2xl font-bold">${{ number_format($settings['commission_amount'], 2) }}</p>
                    </div>
                    <div>
                        <p class="opacity-90">Min Payout</p>
                        <p class="text-2xl font-bold">${{ number_format($settings['minimum_payout'], 2) }}</p>
                    </div>
                    <div>
                        <p class="opacity-90">Cookie Days</p>
                        <p class="text-2xl font-bold">{{ $settings['cookie_duration_days'] }}</p>
                    </div>
                    <div>
                        <p class="opacity-90">Fraud Threshold</p>
                        <p class="text-2xl font-bold">{{ $settings['fraud_threshold'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                    💾 Save Settings
                </button>
            </div>
        </form>

        <!-- Warning Box -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Important:</strong> Changes to fraud detection settings take effect immediately. 
                        Existing conversions are not re-evaluated. Test changes carefully before applying.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
