<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Email Digests') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choose how often you would like to receive performance summaries for your accounts.') }}
        </p>
    </header>

    @php
        $subscriptions = $user->digestSubscriptions->keyBy(function($sub) {
            return ($sub->trading_account_id ?? 'global') . '|' . $sub->frequency;
        });
        $globalDaily = $subscriptions->has('global|daily');
        $globalWeekly = $subscriptions->has('global|weekly');
    @endphp

    <form method="post" action="{{ route('profile.digests.update') }}" class="mt-6 space-y-4">
        @csrf

        <div class="border rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('All Accounts') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Receive a single digest that summarizes all of your connected trading accounts.') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-6 text-sm">
                <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" name="digest[global][daily]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $globalDaily ? 'checked' : '' }}>
                    <span>{{ __('Daily digest') }}</span>
                </label>
                <label class="inline-flex items-center space-x-2">
                    <input type="checkbox" name="digest[global][weekly]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $globalWeekly ? 'checked' : '' }}>
                    <span>{{ __('Weekly digest') }}</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save digests') }}</x-primary-button>

            @if (session('status') === 'digest-preferences-updated')
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
