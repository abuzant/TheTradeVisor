@section('title', 'Manage Public Profiles - TheTradeVisor')
@section('description', 'Configure public profile settings for your trading accounts')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Manage Public Profiles
                </h1>
                <p class="mt-1 text-sm text-gray-600">Configure which accounts to share publicly and customize their display</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(!auth()->user()->public_username)
                {{-- Username not set warning --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Username Required:</strong> You must set your public username first before configuring account profiles.
                                <a href="{{ route('profile.edit') }}" class="underline font-semibold">Go to Profile Settings →</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Accounts List --}}
            @forelse($accounts as $account)
                <div class="bg-white shadow-card rounded-xl p-6" x-data="accountProfileForm({{ $account->id }}, {{ json_encode($account->publicProfileAccount ?? null) }})">
                    {{-- Account Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                    {{ substr($account->broker_name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    <x-broker-name :broker="$account->broker_name" />
                                    #{{ $account->account_number }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    <x-platform-badge :account="$account" />
                                    {{ $account->account_currency }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Public/Private Toggle --}}
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Private</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    class="sr-only peer" 
                                    x-model="isPublic"
                                    @change="togglePublic"
                                    {{ !auth()->user()->public_username ? 'disabled' : '' }}
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                            <span class="text-sm text-gray-600">Public</span>
                        </div>
                    </div>

                    {{-- Configuration Form (shown when public) --}}
                    <div x-show="isPublic" x-collapse style="display: none;">
                        <form @submit.prevent="saveSettings" class="space-y-6 border-t pt-6">
                            
                            {{-- Account Slug --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Slug
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-gray-500 text-sm">/@{{ auth()->user()->public_username ?? 'username' }}/</span>
                                    <input 
                                        type="text" 
                                        x-model="slug"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="account-slug"
                                        maxlength="100"
                                        pattern="[a-z0-9-]+"
                                        :readonly="slugSet"
                                    >
                                    <span class="text-gray-500 text-sm">/{{ $account->account_number }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    <template x-if="!slugSet">
                                        Leave blank to auto-generate. <span class="text-red-600 font-semibold">Cannot be changed after saving.</span>
                                    </template>
                                    <template x-if="slugSet">
                                        <span class="text-green-600">✓ Slug is set and cannot be changed</span>
                                    </template>
                                </p>
                            </div>

                            {{-- Custom Title --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Custom Title (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    x-model="customTitle"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="e.g., My Scalping Strategy, EURUSD Expert"
                                    maxlength="150"
                                >
                                <p class="mt-1 text-xs text-gray-500">Shown as the profile headline (max 150 characters)</p>
                            </div>

                            {{-- Widget Preset --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Widget Preset
                                </label>
                                <select 
                                    x-model="widgetPreset"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="minimal">Minimal (Privacy-Focused)</option>
                                    <option value="full_stats">Full Stats (Comprehensive)</option>
                                    <option value="trader_showcase">Trader Showcase (Maximum Transparency)</option>
                                    <option value="custom">Custom (Choose Widgets)</option>
                                </select>
                                
                                {{-- Preset Descriptions --}}
                                <div class="mt-2 text-xs text-gray-600">
                                    <template x-if="widgetPreset === 'minimal'">
                                        <p>✓ Basic performance cards, equity curve, health score. No symbol breakdown or recent trades.</p>
                                    </template>
                                    <template x-if="widgetPreset === 'full_stats'">
                                        <p>✓ All stats, symbol breakdown, trading hours, monthly calendar. No recent trades.</p>
                                    </template>
                                    <template x-if="widgetPreset === 'trader_showcase'">
                                        <p>✓ Everything including recent trades timeline and best/worst trades.</p>
                                    </template>
                                    <template x-if="widgetPreset === 'custom'">
                                        <p>✓ Manually select which widgets to display below.</p>
                                    </template>
                                </div>
                            </div>

                            {{-- Privacy Options --}}
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        x-model="showSymbols"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Show symbol performance breakdown</span>
                                </label>

                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        x-model="showRecentTrades"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Show recent trades timeline (last 10 trades)</span>
                                </label>
                            </div>

                            {{-- Public URL Preview --}}
                            <div x-show="isPublic && slug" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <p class="text-sm font-medium text-indigo-900 mb-2">Public Profile URL:</p>
                                <div class="flex items-center space-x-2">
                                    <code class="flex-1 text-sm text-indigo-700 bg-white px-3 py-2 rounded border border-indigo-200" x-text="getPublicUrl()"></code>
                                    <button 
                                        type="button"
                                        @click="copyUrl"
                                        class="px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </div>

                            {{-- Save Button --}}
                            <div class="flex items-center justify-end space-x-3">
                                <span x-show="saving" class="text-sm text-gray-600">
                                    <span class="inline-block animate-spin">⏳</span> Saving...
                                </span>
                                <span x-show="saved" x-transition class="text-sm text-green-600">
                                    ✓ Saved successfully!
                                </span>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                                    :disabled="saving"
                                >
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 rounded-xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No trading accounts</h3>
                    <p class="mt-1 text-sm text-gray-500">Connect a trading account to start sharing your performance.</p>
                </div>
            @endforelse

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            window.accountProfileForm = function(accountId, existingProfile) {
                return {
                accountId: accountId,
                isPublic: existingProfile?.is_public || false,
                slug: existingProfile?.account_slug || '',
                slugSet: !!existingProfile?.account_slug,
                customTitle: existingProfile?.custom_title || '',
                widgetPreset: existingProfile?.widget_preset || 'minimal',
                showSymbols: existingProfile?.show_symbols ?? true,
                showRecentTrades: existingProfile?.show_recent_trades || false,
                saving: false,
                saved: false,

                togglePublic() {
                    if (this.isPublic && !this.slug) {
                        // Auto-generate slug if empty
                        this.slug = 'account-' + Math.random().toString(36).substring(2, 7);
                    }
                },

                getPublicUrl() {
                    const username = '{{ auth()->user()->public_username ?? "username" }}';
                    const accountNumber = '{{ $account->account_number ?? "000000" }}';
                    return `https://thetradevisor.com/@${username}/${this.slug}/${accountNumber}`;
                },

                async copyUrl() {
                    try {
                        await navigator.clipboard.writeText(this.getPublicUrl());
                        alert('URL copied to clipboard!');
                    } catch (err) {
                        console.error('Failed to copy:', err);
                    }
                },

                async saveSettings() {
                    this.saving = true;
                    this.saved = false;

                    try {
                        const response = await fetch(`/accounts/${this.accountId}/public-profile`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                is_public: this.isPublic,
                                account_slug: this.slug,
                                custom_title: this.customTitle,
                                widget_preset: this.widgetPreset,
                                show_symbols: this.showSymbols,
                                show_recent_trades: this.showRecentTrades
                            })
                        });

                        if (response.ok) {
                            this.saved = true;
                            this.slugSet = true;
                            setTimeout(() => this.saved = false, 3000);
                        } else {
                            const error = await response.json();
                            alert('Error: ' + (error.message || 'Failed to save settings'));
                        }
                    } catch (error) {
                        console.error('Save error:', error);
                        alert('Failed to save settings. Please try again.');
                    } finally {
                        this.saving = false;
                    }
                }
            }
        };
        });
    </script>
</x-app-layout>
