<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Enterprise Broker') }}
            </h2>
            <a href="{{ route('admin.brokers.show', $broker->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.brokers.update', $broker->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Broker Information --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Broker Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Company Name --}}
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="company_name"
                                           id="company_name"
                                           value="{{ old('company_name', $broker->company_name) }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('company_name') border-red-500 @enderror">
                                    @error('company_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Official Broker Name --}}
                                <div>
                                    <label for="official_broker_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Official Broker Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="official_broker_name"
                                           id="official_broker_name"
                                           value="{{ old('official_broker_name', $broker->official_broker_name) }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('official_broker_name') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">This must match the broker name from MT4/MT5</p>
                                    @error('official_broker_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Monthly Fee --}}
                                <div>
                                    <label for="monthly_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                        Monthly Fee (USD) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number"
                                               name="monthly_fee"
                                               id="monthly_fee"
                                               value="{{ old('monthly_fee', $broker->monthly_fee) }}"
                                               required
                                               min="0"
                                               step="0.01"
                                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('monthly_fee') border-red-500 @enderror">
                                    </div>
                                    @error('monthly_fee')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="is_active"
                                            id="is_active"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('is_active') border-red-500 @enderror">
                                        <option value="1" {{ old('is_active', $broker->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $broker->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Subscription Information --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Subscription Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Subscription Ends At --}}
                                <div>
                                    <label for="subscription_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Subscription Ends At
                                    </label>
                                    <input type="date"
                                           name="subscription_ends_at"
                                           id="subscription_ends_at"
                                           value="{{ old('subscription_ends_at', $broker->subscription_ends_at ? $broker->subscription_ends_at->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('subscription_ends_at') border-red-500 @enderror">
                                    @error('subscription_ends_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Grace Period Ends At --}}
                                <div>
                                    <label for="grace_period_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Grace Period Ends At
                                    </label>
                                    <input type="date"
                                           name="grace_period_ends_at"
                                           id="grace_period_ends_at"
                                           value="{{ old('grace_period_ends_at', $broker->grace_period_ends_at ? $broker->grace_period_ends_at->format('Y-m-d') : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('grace_period_ends_at') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Leave empty to clear grace period</p>
                                    @error('grace_period_ends_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Admin User Information (Read-only) --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Admin User Information (Read-only)
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Name</label>
                                    <p class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                        {{ $broker->user->name ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                                    <p class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-900">
                                        {{ $broker->user->email ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            To change admin user details, edit the user directly in the User Management section.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.brokers.show', $broker->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Broker
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
