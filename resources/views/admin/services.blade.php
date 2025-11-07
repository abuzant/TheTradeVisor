<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Service Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
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

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
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

            {{-- Warning Notice --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Warning:</strong> Restarting services will cause brief downtime. Ensure no critical operations are running.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($serviceStatuses as $serviceName => $service)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $service['name'] }}</h3>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $service['status']['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $service['status']['active'] ? 'Running' : 'Stopped' }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm text-gray-500">
                                    Status: <span class="font-medium text-gray-900">{{ $service['status']['status'] }}</span>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    Service: <span class="font-mono text-xs">{{ $serviceName }}</span>
                                </div>
                            </div>

                            @if($service['can_restart'])
                                <form method="POST" action="{{ route('admin.services.restart', $serviceName) }}"
                                      onsubmit="return confirm('Are you sure you want to restart {{ $service['name'] }}? This will cause brief downtime.')">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                        Restart Service
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-md text-sm font-medium cursor-not-allowed">
                                    Restart Disabled
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- System Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Server Info --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Server Details</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Server Time:</dt>
                                    <dd class="text-gray-900 font-medium">{{ now()->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Timezone:</dt>
                                    <dd class="text-gray-900 font-medium">{{ config('app.timezone') }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">PHP Version:</dt>
                                    <dd class="text-gray-900 font-medium">{{ PHP_VERSION }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Laravel Version:</dt>
                                    <dd class="text-gray-900 font-medium">{{ app()->version() }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Resource Usage --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Resource Usage</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Memory Usage:</dt>
                                    <dd class="text-gray-900 font-medium">{{ round(memory_get_usage() / 1024 / 1024, 2) }} MB</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Memory Limit:</dt>
                                    <dd class="text-gray-900 font-medium">{{ ini_get('memory_limit') }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Max Execution Time:</dt>
                                    <dd class="text-gray-900 font-medium">{{ ini_get('max_execution_time') }}s</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-500">Upload Max Filesize:</dt>
                                    <dd class="text-gray-900 font-medium">{{ ini_get('upload_max_filesize') }}</dd>
                                </div>
                            </dl>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Quick Commands --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Useful Commands</h3>
                    <div class="bg-gray-900 rounded-md p-4 space-y-2">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Clear all Laravel caches:</p>
                            <code class="text-sm text-green-400 font-mono">php artisan optimize:clear</code>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Restart queue workers:</p>
                            <code class="text-sm text-green-400 font-mono">sudo supervisorctl restart thetradevisor-worker:*</code>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Check failed queue jobs:</p>
                            <code class="text-sm text-green-400 font-mono">php artisan queue:failed</code>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">View real-time logs:</p>
                            <code class="text-sm text-green-400 font-mono">tail -f storage/logs/laravel.log</code>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
