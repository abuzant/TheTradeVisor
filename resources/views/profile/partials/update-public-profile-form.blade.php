<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Public Profile Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Configure your public trading profile. Share your performance with friends, family, or the trading community.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.public.update') }}" class="mt-6 space-y-6" x-data="publicProfileForm" @submit.prevent="handleSubmit">
        @csrf
        @method('patch')

        {{-- Username Selection --}}
        <div>
            <x-input-label for="public_username" :value="__('Public Username')" />
            
            @if(auth()->user()->public_username)
                {{-- Username already set --}}
                <div class="mt-2 flex items-center space-x-2 flex-wrap">
                    <span class="text-lg font-bold text-indigo-600 break-all">{{ '@' . auth()->user()->public_username }}</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">✓ Set</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Username was set on {{ auth()->user()->public_username_set_at->format('M d, Y') }}. 
                    <span class="font-semibold text-red-600">This cannot be changed.</span>
                </p>
            @else
                {{-- Username not set yet --}}
                <div class="mt-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-500 font-medium">@</span>
                        <x-text-input 
                            id="public_username" 
                            name="public_username" 
                            type="text" 
                            class="block w-full !max-w-full" 
                            :value="old('public_username')"
                            placeholder="your_username"
                            maxlength="50"
                            pattern="[a-zA-Z0-9_]+"
                            x-model="username"
                            @input="checkUsername"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        3-50 characters. Letters, numbers, and underscores only. 
                        <span class="font-semibold text-red-600">⚠ Cannot be changed after setting!</span>
                    </p>
                    
                    {{-- Username availability indicator --}}
                    <div x-show="username.length >= 3" class="mt-2">
                        <div x-show="usernameChecking" class="text-sm text-gray-500">
                            <span class="inline-block animate-spin">⏳</span> Checking availability...
                        </div>
                        <div x-show="!usernameChecking && usernameAvailable" class="text-sm text-green-600">
                            ✓ Username available!
                        </div>
                        <div x-show="!usernameChecking && !usernameAvailable && username.length >= 3" class="text-sm text-red-600">
                            ✗ Username taken or invalid
                        </div>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('public_username')" />
            @endif
        </div>

        {{-- Display Mode --}}
        <div>
            <x-input-label :value="__('Display Mode')" />
            <p class="mt-1 text-sm text-gray-600">How should your name appear on public profiles?</p>
            
            <div class="mt-3 space-y-3">
                <label class="flex items-center">
                    <input type="radio" name="public_display_mode" value="username" 
                           {{ old('public_display_mode', auth()->user()->public_display_mode) === 'username' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Username</strong> - Show as 
                        <span class="text-indigo-600 font-mono break-all">{{ '@' . (auth()->user()->public_username ?? 'your_username') }}</span>
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="radio" name="public_display_mode" value="anonymous" 
                           {{ old('public_display_mode', auth()->user()->public_display_mode) === 'anonymous' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Anonymous</strong> - Show as 
                        <span class="text-gray-600 font-mono">@anonymous</span>
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="radio" name="public_display_mode" value="custom_name" 
                           {{ old('public_display_mode', auth()->user()->public_display_mode) === 'custom_name' ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                           x-model="displayMode">
                    <span class="ml-2 text-sm text-gray-700">
                        <strong>Custom Name</strong> - Use a custom display name
                    </span>
                </label>

                <div x-show="displayMode === 'custom_name'" class="ml-6 mt-2">
                    <x-text-input 
                        id="public_display_name" 
                        name="public_display_name" 
                        type="text" 
                        class="block w-full !max-w-full" 
                        :value="old('public_display_name', auth()->user()->public_display_name)"
                        placeholder="e.g., Pro Trader, Forex Master"
                        maxlength="100"
                    />
                    <p class="mt-1 text-xs text-gray-500">Max 100 characters</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('public_display_mode')" />
        </div>

        {{-- Leaderboard Settings --}}
        <div class="border-t pt-6">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="show_on_leaderboard" 
                        name="show_on_leaderboard" 
                        type="checkbox" 
                        value="1"
                        {{ old('show_on_leaderboard', auth()->user()->show_on_leaderboard) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        x-model="showOnLeaderboard"
                    >
                </div>
                <div class="ml-3">
                    <label for="show_on_leaderboard" class="font-medium text-gray-700">
                        Show on "Top Traders" Leaderboard
                    </label>
                    <p class="text-sm text-gray-600">
                        Opt-in to appear on the public leaderboard. Your best-performing account will be ranked.
                    </p>
                </div>
            </div>

            <div x-show="showOnLeaderboard" class="mt-4 ml-8">
                <x-input-label :value="__('Rank me by:')" />
                <select name="leaderboard_rank_by" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="total_profit" {{ old('leaderboard_rank_by', auth()->user()->leaderboard_rank_by) === 'total_profit' ? 'selected' : '' }}>
                        Total Profit (30 days)
                    </option>
                    <option value="roi" {{ old('leaderboard_rank_by', auth()->user()->leaderboard_rank_by) === 'roi' ? 'selected' : '' }}>
                        ROI % (30 days)
                    </option>
                    <option value="win_rate" {{ old('leaderboard_rank_by', auth()->user()->leaderboard_rank_by) === 'win_rate' ? 'selected' : '' }}>
                        Win Rate %
                    </option>
                    <option value="profit_factor" {{ old('leaderboard_rank_by', auth()->user()->leaderboard_rank_by) === 'profit_factor' ? 'selected' : '' }}>
                        Profit Factor
                    </option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Profile Settings') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>

        {{-- Warning Modal for Username Confirmation --}}
        <div x-show="showUsernameWarning" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" @click="showUsernameWarning = false"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-md w-full">
                <!-- Header with Icon -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 px-6 pt-6 pb-4 rounded-t-2xl">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                Permanent Username
                            </h3>
                            <p class="text-sm text-amber-700 font-medium">This action cannot be undone</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-5">
                    <div class="space-y-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                Once you set your username, <strong class="text-red-600 font-semibold">it cannot be changed or modified</strong>. 
                                This will be permanent and part of your public profile URL.
                            </p>
                        </div>

                        <div class="text-center py-2">
                            <p class="text-sm text-gray-600 mb-2">You're about to set your username as:</p>
                            <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg px-4 py-3">
                                <p class="text-2xl font-bold text-indigo-600">
                                    @<span x-text="username"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button" @click="showUsernameWarning = false" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="confirmUsername" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition-all">
                        ✓ Confirm & Set Username
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('publicProfileForm', () => ({
                username: '{{ old('public_username', '') }}',
                displayMode: '{{ old('public_display_mode', auth()->user()->public_display_mode) }}',
                showOnLeaderboard: {{ old('show_on_leaderboard', auth()->user()->show_on_leaderboard) ? 'true' : 'false' }},
                usernameChecking: false,
                usernameAvailable: false,
                showUsernameWarning: false,
                usernameAlreadySet: {{ auth()->user()->public_username ? 'true' : 'false' }},

                checkUsername() {
                    // Basic client-side validation
                    if (this.username.length < 3) {
                        this.usernameAvailable = false;
                        return;
                    }

                    // Check format
                    if (!/^[a-zA-Z0-9_]+$/.test(this.username)) {
                        this.usernameAvailable = false;
                        return;
                    }

                    // In production, add AJAX call to check availability
                    this.usernameChecking = true;
                    setTimeout(() => {
                        this.usernameChecking = false;
                        this.usernameAvailable = true; // Placeholder
                    }, 500);
                },

                handleSubmit(event) {
                    // If username is being set for first time and has value, show warning
                    if (!this.usernameAlreadySet && this.username && this.username.length >= 3) {
                        this.showUsernameWarning = true;
                    } else {
                        // Submit directly - allow default form submission
                        event.target.submit();
                    }
                },

                confirmUsername() {
                    this.showUsernameWarning = false;
                    // Remove the Alpine event listener and submit normally
                    const form = this.$el;
                    // Use setTimeout to let Alpine finish processing
                    setTimeout(() => {
                        form.submit();
                    }, 100);
                }
        }));
    });
</script>
