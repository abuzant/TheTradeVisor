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
                
                {{-- Horizon Control Card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Laravel Horizon</h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $horizonStatus['status']['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $horizonStatus['status']['active'] ? 'Running' : 'Stopped' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500">
                                Status: <span class="font-medium text-gray-900">{{ $horizonStatus['status']['status'] }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                Service: <span class="font-mono text-xs">supervisor → horizon</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <form method="POST" action="{{ route('admin.services.horizon', 'start') }}"
                                  onsubmit="return confirm('Start Horizon queue workers?')">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                    ▶️ Start
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.services.horizon', 'stop') }}"
                                  onsubmit="return confirm('Stop Horizon? Queued jobs will not be processed.')">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                    ⏹️ Stop
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.services.horizon', 'restart') }}"
                                  onsubmit="return confirm('Restart Horizon?')">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                    🔄 Restart
                                </button>
                            </form>
                        </div>
                        
                        <a href="/horizon" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            📊 Open Dashboard →
                        </a>
                    </div>
                </div>
                
                {{-- Cache Management Card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="background: linear-gradient(to right, #a855f7, #4f46e5);">
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-white">🧹 Clear All Caches</h3>
                            <p class="text-sm text-purple-100 mt-1">Laravel • Redis • Nginx • PHP-FPM</p>
                        </div>
                        <p class="text-sm mb-4 text-purple-100">
                            Clears all caches, rebuilds optimized caches, and restarts services.
                        </p>
                        <form method="POST" action="{{ route('admin.services.clear-caches') }}"
                              onsubmit="return confirm('This will clear ALL caches and restart PHP-FPM. Continue?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-white text-purple-600 rounded-md hover:bg-purple-50 font-bold shadow-md">
                                🚀 Clear All Caches
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Backend Instances --}}
            @if(isset($backendStatuses))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">🔀 Backend Instances (Multi-Instance Architecture)</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            Load Balanced
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        @foreach($backendStatuses as $instanceName => $instance)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $instance['name'] }}</h4>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $instance['status']['active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $instance['status']['active'] ? '●' : '○' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">
                                    Status: <span class="font-medium">{{ $instance['status']['status'] }}</span>
                                </div>
                                @if($instance['can_restart'])
                                    <form method="POST" action="{{ route('admin.services.backend.restart', $instanceName) }}"
                                          onsubmit="return confirm('Restart {{ $instance['name'] }}?')">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-xs font-medium">
                                            🔄 Restart
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>ℹ️ Info:</strong> Traffic is distributed across 4 backend instances using least-connections algorithm. 
                            Each instance has its own PHP-FPM pool (25 workers) for better load distribution and fault tolerance.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Multi-Instance Management Commands --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🎛️ Multi-Instance Management</h3>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-1 rounded mr-2">Backend</span>
                            Backend Instance Control
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🚀 Start all backend instances:</p>
                                <code class="text-sm text-green-400 font-mono">./start-backends.sh</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🛑 Stop all backend instances:</p>
                                <code class="text-sm text-green-400 font-mono">./stop-backends.sh</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📊 Check backend instances status:</p>
                                <code class="text-sm text-green-400 font-mono">./status-backends.sh</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🔄 Restart individual backend (example for backend 1):</p>
                                <code class="text-sm text-green-400 font-mono">sudo kill -QUIT $(cat /run/nginx-backend-1.pid) && sudo nginx -c /etc/nginx/backends/nginx-backend-1-master.conf</code>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded mr-2">Logs</span>
                            Multi-Instance Logging
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📋 View all backend error logs:</p>
                                <code class="text-sm text-green-400 font-mono">tail -f /var/log/nginx/backend-*-error.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📋 View all PHP-FPM pool logs:</p>
                                <code class="text-sm text-green-400 font-mono">tail -f /var/log/php8.3-fpm-pool*.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📋 View specific backend logs (example for backend 1):</p>
                                <code class="text-sm text-green-400 font-mono">tail -f /var/log/nginx/backend-1-error.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📋 View load balancer logs:</p>
                                <code class="text-sm text-green-400 font-mono">sudo tail -f /var/log/nginx/thetradevisor-error.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🔍 Monitor load distribution (watch which backend handles requests):</p>
                                <code class="text-sm text-green-400 font-mono">watch -n 1 'tail -n 1 /var/log/nginx/backend-*-access.log'</code>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded mr-2">Test</span>
                            Backend Connectivity Testing
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🧪 Test all backend instances:</p>
                                <code class="text-sm text-green-400 font-mono">for i in 1 2 3 4; do curl -s -o /dev/null -w "Backend ${i}: %{http_code}\n" http://127.0.0.1:808${i}; done</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🧪 Test specific backend (example for backend 1):</p>
                                <code class="text-sm text-green-400 font-mono">curl -I http://127.0.0.1:8081</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🔍 Check which backend handled your request:</p>
                                <code class="text-sm text-green-400 font-mono">curl -I https://thetradevisor.com | grep X-Backend-Instance</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">📊 Check PHP-FPM pool status:</p>
                                <code class="text-sm text-green-400 font-mono">netstat -tlnp | grep "127.0.0.1:900[1-4]"</code>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💻 Useful Commands & One-Liners</h3>
                    
                    {{-- Custom Artisan Commands --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-1 rounded mr-2">Custom</span>
                            TheTradeVisor Commands
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🔄 Sync all symbols from database:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan symbols:sync</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🕐 Fix NULL timestamps in deals:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan deals:fix-times</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">🧹 Complete cache refresh (all caches + services):</p>
                                <code class="text-sm text-green-400 font-mono">./refurbish.sh</code>
                            </div>
                        </div>
                    </div>

                    {{-- Laravel Cache Commands --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded mr-2">Cache</span>
                            Cache Management
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Clear all Laravel caches at once:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan optimize:clear</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Clear application cache only:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan cache:clear</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Clear config cache:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan config:clear</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Clear route cache:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan route:clear</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Clear compiled views:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan view:clear</code>
                            </div>
                        </div>
                    </div>

                    {{-- Queue & Horizon Commands --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded mr-2">Queue</span>
                            Queue & Horizon
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Restart Horizon queue workers:</p>
                                <code class="text-sm text-green-400 font-mono">sudo supervisorctl restart horizon</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check Horizon status:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan horizon:status</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Gracefully terminate Horizon:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan horizon:terminate</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">List failed queue jobs:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan queue:failed</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Retry all failed jobs:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan queue:retry all</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Process one job manually:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan queue:work --once</code>
                            </div>
                        </div>
                    </div>

                    {{-- System & Debugging --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded mr-2">Debug</span>
                            System & Debugging
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">View real-time Laravel logs:</p>
                                <code class="text-sm text-green-400 font-mono">tail -f storage/logs/laravel.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">View last 50 log lines:</p>
                                <code class="text-sm text-green-400 font-mono">tail -n 50 storage/logs/laravel.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check disk space usage:</p>
                                <code class="text-sm text-green-400 font-mono">df -h</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check memory usage:</p>
                                <code class="text-sm text-green-400 font-mono">free -h</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check running processes:</p>
                                <code class="text-sm text-green-400 font-mono">ps aux | grep php</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check Nginx error logs:</p>
                                <code class="text-sm text-green-400 font-mono">sudo tail -f /var/log/nginx/error.log</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check PHP-FPM error logs:</p>
                                <code class="text-sm text-green-400 font-mono">sudo tail -f /var/log/php8.3-fpm.log</code>
                            </div>
                        </div>
                    </div>

                    {{-- Database Commands --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded mr-2">Database</span>
                            Database & Migrations
                        </h4>
                        <div class="bg-gray-900 rounded-md p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Run pending migrations:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan migrate --force</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Check migration status:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan migrate:status</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Access PostgreSQL console:</p>
                                <code class="text-sm text-green-400 font-mono">sudo -u postgres psql thetradevisor</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Laravel Tinker (REPL):</p>
                                <code class="text-sm text-green-400 font-mono">php artisan tinker</code>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Seed database:</p>
                                <code class="text-sm text-green-400 font-mono">php artisan db:seed</code>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
