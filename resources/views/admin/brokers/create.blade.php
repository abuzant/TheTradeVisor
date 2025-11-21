<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Enterprise Broker') }}
            </h2>
            <a href="{{ route('admin.brokers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
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
                    <form method="POST" action="{{ route('admin.brokers.store') }}" class="space-y-6">
                        @csrf

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
                                           value="{{ old('company_name') }}"
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
                                           value="{{ old('official_broker_name') }}"
                                           required
                                           placeholder="e.g., IC Markets, XM Global"
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
                                               value="{{ old('monthly_fee', 999) }}"
                                               required
                                               min="0"
                                               step="0.01"
                                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('monthly_fee') border-red-500 @enderror">
                                    </div>
                                    @error('monthly_fee')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Subscription Duration --}}
                                <div>
                                    <label for="subscription_months" class="block text-sm font-medium text-gray-700 mb-2">
                                        Initial Subscription (Months) <span class="text-red-500">*</span>
                                    </label>
                                    <select name="subscription_months"
                                            id="subscription_months"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('subscription_months') border-red-500 @enderror">
                                        <option value="1" {{ old('subscription_months') == 1 ? 'selected' : '' }}>1 Month</option>
                                        <option value="3" {{ old('subscription_months') == 3 ? 'selected' : '' }}>3 Months</option>
                                        <option value="6" {{ old('subscription_months') == 6 ? 'selected' : '' }}>6 Months</option>
                                        <option value="12" {{ old('subscription_months', 12) == 12 ? 'selected' : '' }}>12 Months</option>
                                        <option value="24" {{ old('subscription_months') == 24 ? 'selected' : '' }}>24 Months</option>
                                    </select>
                                    @error('subscription_months')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Admin User Information --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                Admin User Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Admin Name --}}
                                <div>
                                    <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="admin_name"
                                           id="admin_name"
                                           value="{{ old('admin_name') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('admin_name') border-red-500 @enderror">
                                    @error('admin_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Admin Email --}}
                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           name="admin_email"
                                           id="admin_email"
                                           value="{{ old('admin_email') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('admin_email') border-red-500 @enderror">
                                    @error('admin_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Admin Password --}}
                                <div class="md:col-span-2">
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password"
                                           name="admin_password"
                                           id="admin_password"
                                           required
                                           minlength="8"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 @error('admin_password') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                                    @error('admin_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>What happens when you create a broker:</strong>
                                    </p>
                                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                                        <li>A new admin user account will be created with enterprise admin privileges</li>
                                        <li>An initial API key will be generated automatically (prefix: ent_)</li>
                                        <li>The broker will be activated immediately</li>
                                        <li>All accounts matching the broker name will get 180-day data access</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.brokers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Enterprise Broker
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
