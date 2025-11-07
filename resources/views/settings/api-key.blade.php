<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('API Key Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your API Key</h3>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                        <div class="flex">
                            <input type="text"
                                   id="api-key"
                                   value="{{ session('new_key') ?? $user->api_key }}"
                                   readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm font-mono">
                            <button onclick="copyApiKey()"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 text-sm font-medium">
                                Copy
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Use this API key in your MT5 Expert Advisor to connect your trading account.</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">MT5 Expert Advisor Configuration</h4>
                        <div class="bg-gray-50 rounded-md p-4 font-mono text-sm">
                            <p class="text-gray-700">API_URL: <span class="text-indigo-600">https://api.thetradevisor.com/api/v1/data/collect</span></p>
                            <p class="text-gray-700">API_KEY: <span class="text-indigo-600">{{ $user->api_key }}</span></p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Regenerate API Key</h4>
                        <p class="text-sm text-gray-500 mb-4">Warning: Regenerating your API key will disconnect all your MT5 terminals until you update them with the new key.</p>

                        <form method="POST" action="{{ route('settings.api-key.regenerate') }}" onsubmit="return confirm('Are you sure you want to regenerate your API key? This will disconnect all your MT5 terminals.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                Regenerate API Key
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copyApiKey() {
            const input = document.getElementById('api-key');
            input.select();
            document.execCommand('copy');

            // Show feedback
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-green-600');
            btn.classList.remove('bg-indigo-600');

            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-600');
                btn.classList.add('bg-indigo-600');
            }, 2000);
        }
    </script>
</x-app-layout>
