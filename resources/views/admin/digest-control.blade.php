<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Digest Control</h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">Enable/disable digest generation and LLM integration for the entire site.</p>

                    @if(session('status'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Global Digest Toggle -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Digest Feature</h3>
                        <form method="POST" action="{{ route('admin.digest-control.toggle') }}">
                            @csrf
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="enabled" value="1" {{ $enabled ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-indigo-600">
                                <span class="ml-2 text-gray-700">Enable digest generation and sending</span>
                            </label>
                            <button type="submit" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
                        </form>
                    </div>

                    <!-- LLM Toggle -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">LLM Integration (Ollama)</h3>
                        <form method="POST" action="{{ route('admin.digest-control.toggle-llm') }}">
                            @csrf
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="llm_enabled" value="1" {{ $llmEnabled ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-indigo-600">
                                <span class="ml-2 text-gray-700">Enable natural language generation via Ollama</span>
                            </label>
                            <button type="submit" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
                        </form>
                    </div>

                    <!-- Service Status -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Service Status</h3>
                        <p class="text-sm text-gray-700">Digest generation: <span class="font-semibold {{ $enabled ? 'text-green-600' : 'text-red-600' }}">{{ $enabled ? 'Enabled' : 'Disabled' }}</span></p>
                        <p class="text-sm text-gray-700">LLM integration: <span class="font-semibold {{ $llmEnabled ? 'text-green-600' : 'text-gray-500' }}">{{ $llmEnabled ? 'Enabled' : 'Disabled (template-only)' }}</span></p>
                        <p class="text-sm text-gray-700">LLM Endpoint: <span class="font-mono text-xs">{{ $llmEndpoint }}</span></p>
                    </div>

                    <!-- Info Section -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">About This Feature</h3>
                        <p class="text-sm text-blue-700 mb-2">
                            <strong>Digest</strong> generates a concise trading summary for each user (daily/weekly). It aggregates PnL, win rate, top pairs, volume trends, best/worst times, risky trades, and long-running positions.
                        </p>
                        <p class="text-sm text-blue-700 mb-2">
                            <strong>Insights</strong> are rendered using lightweight templates—no external LLM or heavy services required. Everything stays deterministic and fast.
                        </p>
                        <p class="text-sm text-blue-700">
                            <strong>Admin Control</strong>: Enable/disable the feature globally, test generation, and view activity from this panel.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Actions</h3>
                        <form method="POST" action="{{ route('admin.digest-control.test') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Test Generation</button>
                        </form>
                    </div>

                    <!-- Test Output -->
                    @if(session('test_output'))
                        <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2">Test Output</h3>
                            <pre class="text-xs whitespace-pre-wrap">{{ session('test_output') }}</pre>
                        </div>
                    @endif

                    @if(session('test_error'))
                        <div class="mb-6 p-4 bg-red-100 rounded-lg">
                            <h3 class="text-sm font-semibold text-red-800 mb-2">Test Error</h3>
                            <pre class="text-xs whitespace-pre-wrap">{{ session('test_error') }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
