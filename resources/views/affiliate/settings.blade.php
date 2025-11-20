@extends('layouts.affiliate')

@section('title', 'Settings')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Account Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Username</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $affiliate->username }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $affiliate->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Affiliate ID</label>
                        <p class="text-sm text-gray-900 font-mono mt-1">{{ $affiliate->id }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Referral Slug</label>
                        <p class="text-sm text-gray-900 font-mono mt-1">{{ $affiliate->slug }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Member Since</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $affiliate->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payout Settings -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout Settings</h3>
                
                <form method="POST" action="{{ route('affiliate.settings.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- USDT Wallet Address -->
                    <div class="mb-4">
                        <x-input-label for="usdt_wallet_address" value="USDT Wallet Address" />
                        <x-text-input 
                            id="usdt_wallet_address" 
                            name="usdt_wallet_address" 
                            type="text" 
                            class="mt-1 block w-full font-mono text-sm" 
                            :value="old('usdt_wallet_address', $affiliate->usdt_wallet_address)"
                            placeholder="Enter your USDT wallet address"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('usdt_wallet_address')" />
                        <p class="text-xs text-gray-500 mt-1">Make sure this address is correct. We cannot recover funds sent to wrong addresses.</p>
                    </div>

                    <!-- Wallet Type -->
                    <div class="mb-4">
                        <x-input-label for="wallet_type" value="Wallet Type" />
                        <select 
                            id="wallet_type" 
                            name="wallet_type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Select wallet type</option>
                            <option value="TRC20" {{ old('wallet_type', $affiliate->wallet_type) === 'TRC20' ? 'selected' : '' }}>TRC20 (Tron Network)</option>
                            <option value="ERC20" {{ old('wallet_type', $affiliate->wallet_type) === 'ERC20' ? 'selected' : '' }}>ERC20 (Ethereum Network)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('wallet_type')" />
                        <p class="text-xs text-gray-500 mt-1">TRC20 recommended for lower fees</p>
                    </div>

                    <!-- Payment Threshold -->
                    <div class="mb-6">
                        <x-input-label for="payment_threshold" value="Automatic Payout Threshold (Optional)" />
                        <x-text-input 
                            id="payment_threshold" 
                            name="payment_threshold" 
                            type="number" 
                            step="0.01"
                            min="50"
                            max="1000"
                            class="mt-1 block w-full" 
                            :value="old('payment_threshold', $affiliate->payment_threshold ?? 50)"
                            placeholder="50.00"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('payment_threshold')" />
                        <p class="text-xs text-gray-500 mt-1">Automatically request payout when earnings reach this amount (minimum $50)</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            Save Settings
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Account Status</span>
                        @if($affiliate->is_active)
                            <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Suspended</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Email Verified</span>
                        @if($affiliate->is_verified)
                            <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Verified</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Not Verified</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Last Login</span>
                        <span class="text-sm text-gray-900">{{ $affiliate->last_login_at ? $affiliate->last_login_at->diffForHumans() : 'Never' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
            <h3 class="text-lg font-semibold mb-4">Your Performance</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm opacity-90">Total Clicks</p>
                    <p class="text-2xl font-bold">{{ number_format($affiliate->total_clicks) }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-90">Total Signups</p>
                    <p class="text-2xl font-bold">{{ number_format($affiliate->total_signups) }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-90">Paid Signups</p>
                    <p class="text-2xl font-bold">{{ number_format($affiliate->paid_signups) }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-90">Total Earnings</p>
                    <p class="text-2xl font-bold">${{ number_format($affiliate->total_earnings, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-900 mb-2">Danger Zone</h3>
            <p class="text-sm text-red-700 mb-4">Need help or want to close your affiliate account? Contact support.</p>
            <a href="mailto:support@thetradevisor.com" class="inline-block px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition">
                Contact Support
            </a>
        </div>
    </div>
</div>
@endsection
