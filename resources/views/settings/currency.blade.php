<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Currency Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.currency.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="display_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Currency
                            </label>
                            <select name="display_currency" id="display_currency"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}" {{ $currentCurrency === $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">
                                All account balances and profits will be converted to this currency for display.
                            </p>
                            @error('display_currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Note:</strong> Currency conversion uses real-time exchange rates updated hourly.
                                        This is for display purposes only - your actual trading account currencies remain unchanged.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Back to Dashboard
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Currency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
