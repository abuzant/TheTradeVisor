<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Public Profile Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Configure your public trading profile. Share your performance with friends, family, or the trading community.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.public.update') }}" class="mt-6 space-y-6" x-data="publicProfileForm()" @submit.prevent="handleSubmit">
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
    </form>
</section>

{{-- Warning Modal for Username Confirmation (outside section to prevent layout breaking) --}}
<div x-show="showUsernameWarning" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showUsernameWarning = false"></div>

            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-4">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                ⚠️ Username Cannot Be Changed
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Once you set your username, <strong class="text-red-600">it cannot be changed or modified</strong>. 
                                    This is permanent and will be part of your public profile URL.
                                </p>
                                <p class="mt-2 text-sm text-gray-500">
                                    Are you sure you want to set your username as:
                                </p>
                                <p class="mt-2 text-lg font-bold text-indigo-600">
                                    @<span x-text="username"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="confirmUsername" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Yes, Set Username
                    </button>
                    <button type="button" @click="showUsernameWarning = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function publicProfileForm() {
            return {
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
                        // Submit directly
                        this.$el.submit();
                    }
                },

                confirmUsername() {
                    this.showUsernameWarning = false;
                    // Submit the form
                    this.$el.submit();
                }
            }
        }
    </script>
</div>
