<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📚 {{ __('Admin Wiki') }}
            </h2>
            <span class="text-sm text-gray-600">System Administration & Maintenance Guide</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Quick Navigation --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <h3 class="text-2xl font-bold mb-4">🚀 Quick Navigation</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        <a href="#quick-actions" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">⚡</div>
                            <div class="font-semibold text-sm">Quick Actions</div>
                        </a>
                        <a href="#performance" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">📊</div>
                            <div class="font-semibold text-sm">Performance</div>
                        </a>
                        <a href="#recent-events" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">📋</div>
                            <div class="font-semibold text-sm">Recent Events</div>
                        </a>
                        <a href="#database" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">🗄️</div>
                            <div class="font-semibold text-sm">Database</div>
                        </a>
                        <a href="#api-endpoints" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">📡</div>
                            <div class="font-semibold text-sm">API Endpoints</div>
                        </a>
                        <a href="#troubleshooting" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">🔍</div>
                            <div class="font-semibold text-sm">Troubleshooting</div>
                        </a>
                        <a href="#artisan-commands" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">⚡</div>
                            <div class="font-semibold text-sm">Artisan</div>
                        </a>
                        <a href="#scripts" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">📜</div>
                            <div class="font-semibold text-sm">Scripts</div>
                        </a>
                        <a href="#scheduled-tasks" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">⏰</div>
                            <div class="font-semibold text-sm">Cron Jobs</div>
                        </a>
                        <a href="#services" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">🔧</div>
                            <div class="font-semibold text-sm">Services</div>
                        </a>
                        <a href="#security" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">🛡️</div>
                            <div class="font-semibold text-sm">Security</div>
                        </a>
                        <a href="#backups" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition">
                            <div class="text-2xl mb-1">💾</div>
                            <div class="font-semibold text-sm">Backups</div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- System Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="text-2xl mr-2">💻</span>
                        System Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($systemInfo as $category => $items)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3 capitalize">{{ str_replace('_', ' ', $category) }}</h4>
                            <dl class="space-y-2">
                                @foreach($items as $key => $value)
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">{{ $key }}:</dt>
                                    <dd class="text-gray-900 font-mono">{{ $value }}</dd>
                                </div>
                                @endforeach
                            </dl>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quick Actions Panel --}}
            <div id="quick-actions" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">⚡</span>
                        Quick Actions
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="showConfirmModal('Clear All Caches', 'This will clear application, config, route, and view caches.', 'cache-clear')" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-4 px-6 rounded-lg shadow-md transition flex flex-col items-center">
                            <span class="text-3xl mb-2">🗑️</span>
                            <span>Clear All Caches</span>
                        </button>

                        <button onclick="showConfirmModal('Restart Horizon', 'This will gracefully terminate Horizon. Supervisor will restart it automatically.', 'horizon-restart')" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-6 rounded-lg shadow-md transition flex flex-col items-center">
                            <span class="text-3xl mb-2">🔄</span>
                            <span>Restart Horizon</span>
                        </button>

                        <button onclick="showConfirmModal('View Recent Logs', 'This will display the last 100 lines from Laravel log.', 'view-logs')" 
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-6 rounded-lg shadow-md transition flex flex-col items-center">
                            <span class="text-3xl mb-2">📄</span>
                            <span>View Recent Logs</span>
                        </button>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> All actions require confirmation and will execute immediately upon approval.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Performance Metrics --}}
            <div id="performance" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📊</span>
                        Performance Metrics (Snapshot)
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $performanceMetrics['db_connections'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Active DB Connections</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $performanceMetrics['queue_pending'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Pending Queue Jobs</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $performanceMetrics['queue_failed'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Failed Queue Jobs</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $performanceMetrics['disk_used_percent'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Disk Usage</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-700">{{ $performanceMetrics['disk_free'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Disk Free</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-700">{{ $performanceMetrics['disk_total'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Disk Total</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-700">{{ $performanceMetrics['memory_usage'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Memory Usage</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-700">{{ $performanceMetrics['memory_peak'] }}</div>
                            <div class="text-sm text-gray-600 mt-1">Peak Memory</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent System Events --}}
            <div id="recent-events" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📋</span>
                        Recent System Events
                    </h3>

                    @if(count($recentEvents) > 0)
                        <div class="space-y-2">
                            @foreach($recentEvents as $event)
                                <div class="border-l-4 pl-4 py-2 {{ 
                                    $event['severity'] === 'error' ? 'border-red-500 bg-red-50' : 
                                    ($event['severity'] === 'warning' ? 'border-yellow-500 bg-yellow-50' : 'border-blue-500 bg-blue-50') 
                                }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <span class="text-xs font-mono text-gray-600">{{ $event['time'] }}</span>
                                            <p class="text-sm text-gray-800 mt-1">{{ $event['message'] }}</p>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded {{ 
                                            $event['severity'] === 'error' ? 'bg-red-200 text-red-800' : 
                                            ($event['severity'] === 'warning' ? 'bg-yellow-200 text-yellow-800' : 'bg-blue-200 text-blue-800') 
                                        }}">
                                            {{ strtoupper($event['severity']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No recent events found.</p>
                    @endif
                </div>
            </div>

            {{-- Database Schema --}}
            <div id="database" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">🗄️</span>
                        Database Schema Overview
                    </h3>

                    @if(count($databaseSchema) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Table Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Columns</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Row Count</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($databaseSchema as $table)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $table['name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $table['columns'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($table['rows']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Unable to load database schema.</p>
                    @endif
                </div>
            </div>

            {{-- API Endpoints --}}
            <div id="api-endpoints" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📡</span>
                        API Endpoints Reference
                    </h3>

                    <div class="space-y-4">
                        @foreach($apiEndpoints as $endpoint)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded font-mono text-sm font-bold {{ 
                                            $endpoint['method'] === 'GET' ? 'bg-blue-100 text-blue-800' : 
                                            ($endpoint['method'] === 'POST' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') 
                                        }}">
                                            {{ $endpoint['method'] }}
                                        </span>
                                        <code class="text-sm font-mono text-gray-900">{{ $endpoint['endpoint'] }}</code>
                                    </div>
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $endpoint['rate_limit'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 ml-16">{{ $endpoint['description'] }}</p>
                                <p class="text-xs text-gray-500 ml-16 mt-1">Auth: {{ $endpoint['auth'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Troubleshooting Guide --}}
            <div id="troubleshooting" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">🔍</span>
                        Troubleshooting Guide
                    </h3>

                    @foreach($troubleshooting as $guide)
                        <div class="mb-8 border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-bold text-red-600 mb-3">{{ $guide['issue'] }}</h4>
                            
                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Symptoms:</h5>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                                    @foreach($guide['symptoms'] as $symptom)
                                        <li>{{ $symptom }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Diagnostic Checks:</h5>
                                <div class="space-y-2">
                                    @foreach($guide['checks'] as $check)
                                        <div class="bg-gray-900 text-green-400 p-2 rounded font-mono text-xs">{{ $check }}</div>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2">Solutions:</h5>
                                <ul class="list-decimal list-inside space-y-1 text-sm text-gray-600">
                                    @foreach($guide['solutions'] as $solution)
                                        <li>{{ $solution }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Security Overview --}}
            <div id="security" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">🛡️</span>
                        Security Overview
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Security Metrics</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Failed Logins (24h):</dt>
                                    <dd class="text-gray-900 font-bold">{{ $securityInfo['failed_logins_24h'] }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Active API Keys:</dt>
                                    <dd class="text-gray-900 font-bold">{{ $securityInfo['active_api_keys'] }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Rate Limit Violations:</dt>
                                    <dd class="text-gray-900 font-mono text-xs">{{ $securityInfo['rate_limit_violations'] }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Environment Status</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Environment:</dt>
                                    <dd class="text-gray-900 font-bold">{{ strtoupper($securityInfo['environment']) }}</dd>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Debug Mode:</dt>
                                    <dd class="text-gray-900 font-bold">{{ $securityInfo['debug_mode'] }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Backup & Recovery --}}
            <div id="backups" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">💾</span>
                        Backup & Recovery Procedures
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-indigo-600 mb-3">Database Backups</h4>
                            <dl class="space-y-2 text-sm">
                                <div>
                                    <dt class="text-gray-600 font-semibold">Command:</dt>
                                    <dd class="bg-gray-900 text-green-400 p-2 rounded font-mono text-xs mt-1">{{ $backupInfo['database']['command'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Location:</dt>
                                    <dd class="text-gray-900 font-mono">{{ $backupInfo['database']['location'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Schedule:</dt>
                                    <dd class="text-gray-900">{{ $backupInfo['database']['schedule'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Retention:</dt>
                                    <dd class="text-gray-900">{{ $backupInfo['database']['retention'] }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-purple-600 mb-3">File Backups</h4>
                            <dl class="space-y-2 text-sm">
                                <div>
                                    <dt class="text-gray-600 font-semibold">Command:</dt>
                                    <dd class="bg-gray-900 text-green-400 p-2 rounded font-mono text-xs mt-1">{{ $backupInfo['files']['command'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Location:</dt>
                                    <dd class="text-gray-900 font-mono">{{ $backupInfo['files']['location'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Schedule:</dt>
                                    <dd class="text-gray-900">{{ $backupInfo['files']['schedule'] }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Retention:</dt>
                                    <dd class="text-gray-900">{{ $backupInfo['files']['retention'] }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                        <h4 class="font-semibold text-red-700 mb-3">⚠️ Database Restore Procedure</h4>
                        <ol class="space-y-2 text-sm text-red-800">
                            @foreach($backupInfo['restore_db']['steps'] as $step)
                                <li class="font-mono">{{ $step }}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Artisan Commands --}}
            <div id="artisan-commands" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">⚡</span>
                        Artisan Commands
                    </h3>

                    @foreach($artisanCommands as $category => $group)
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-indigo-600 mb-4 border-b-2 border-indigo-200 pb-2">
                            {{ $group['title'] }}
                        </h4>

                        @foreach($group['commands'] as $command)
                        <div class="mb-6 border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="text-lg font-mono font-semibold text-gray-900">{{ $command['name'] }}</h5>
                                @if(isset($command['schedule']))
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                    ⏰ {{ $command['schedule'] }}
                                </span>
                                @endif
                            </div>
                            
                            <p class="text-gray-700 mb-3">{{ $command['description'] }}</p>
                            
                            <div class="bg-gray-900 text-green-400 p-3 rounded font-mono text-sm mb-3">
                                {{ $command['usage'] }}
                            </div>

                            @if(isset($command['options']))
                            <div class="mb-3">
                                <h6 class="font-semibold text-gray-700 mb-2">Options:</h6>
                                <dl class="space-y-1 text-sm">
                                    @foreach($command['options'] as $option => $desc)
                                    <div class="flex">
                                        <dt class="font-mono text-indigo-600 mr-2">{{ $option }}</dt>
                                        <dd class="text-gray-600">- {{ $desc }}</dd>
                                    </div>
                                    @endforeach
                                </dl>
                            </div>
                            @endif

                            @if(isset($command['examples']))
                            <div class="mb-3">
                                <h6 class="font-semibold text-gray-700 mb-2">Examples:</h6>
                                @foreach($command['examples'] as $example => $explanation)
                                <div class="mb-2">
                                    <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mb-1">
                                        {{ $example }}
                                    </div>
                                    <p class="text-xs text-gray-600 ml-2">→ {{ $explanation }}</p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($command['docs']))
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center">
                                    📄 Full Documentation: <span class="font-mono ml-1">{{ $command['docs'] }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Shell Scripts --}}
            <div id="scripts" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📜</span>
                        Shell Scripts
                    </h3>

                    @foreach($scripts as $category => $group)
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-purple-600 mb-4 border-b-2 border-purple-200 pb-2">
                            {{ $group['title'] }}
                        </h4>

                        @foreach($group['scripts'] as $script)
                        <div class="mb-6 border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="text-lg font-mono font-semibold text-gray-900">{{ $script['name'] }}</h5>
                                @if(isset($script['schedule']))
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    ⏰ {{ $script['schedule'] }}
                                </span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-2 font-mono">{{ $script['path'] }}</p>
                            <p class="text-gray-700 mb-3">{{ $script['description'] }}</p>
                            
                            <div class="bg-gray-900 text-green-400 p-3 rounded font-mono text-sm mb-3">
                                {{ $script['usage'] }}
                            </div>

                            @if(isset($script['features']))
                            <div class="mb-3">
                                <h6 class="font-semibold text-gray-700 mb-2">Features:</h6>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                                    @foreach($script['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if(isset($script['examples']))
                            <div class="mb-3">
                                <h6 class="font-semibold text-gray-700 mb-2">Examples:</h6>
                                @foreach($script['examples'] as $example => $explanation)
                                <div class="mb-2">
                                    <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mb-1">
                                        {{ $example }}
                                    </div>
                                    <p class="text-xs text-gray-600 ml-2">→ {{ $explanation }}</p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($script['logs']) || isset($script['alerts']))
                            <div class="mt-3 pt-3 border-t border-gray-200 grid grid-cols-2 gap-2 text-sm">
                                @if(isset($script['logs']))
                                <div>
                                    <span class="font-semibold text-gray-700">Logs:</span>
                                    <span class="font-mono text-gray-600">{{ $script['logs'] }}</span>
                                </div>
                                @endif
                                @if(isset($script['alerts']))
                                <div>
                                    <span class="font-semibold text-gray-700">Alerts:</span>
                                    <span class="font-mono text-gray-600">{{ $script['alerts'] }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Scheduled Tasks --}}
            <div id="scheduled-tasks" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">⏰</span>
                        Scheduled Tasks (Cron)
                    </h3>

                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> All Laravel scheduled tasks run via: 
                            <code class="bg-blue-100 px-2 py-1 rounded">* * * * * cd /www && php artisan schedule:run</code>
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Command</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($scheduledTasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $task['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                            {{ $task['schedule'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                        {{ $task['command'] }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $task['description'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h6 class="font-semibold text-gray-700 mb-2">Useful Commands:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <code class="bg-gray-900 text-green-400 px-2 py-1 rounded mr-2">php artisan schedule:list</code>
                                <span class="text-gray-600">- View all scheduled tasks and next run time</span>
                            </div>
                            <div class="flex items-start">
                                <code class="bg-gray-900 text-green-400 px-2 py-1 rounded mr-2">php artisan schedule:test</code>
                                <span class="text-gray-600">- Test scheduled tasks</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Middleware --}}
            <div id="middleware" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">🛡️</span>
                        Middleware
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($middleware as $alias => $info)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <h5 class="font-mono font-semibold text-indigo-600 mb-2">{{ $alias }}</h5>
                            <p class="text-sm text-gray-700 mb-2">{{ $info['description'] }}</p>
                            <p class="text-xs text-gray-600">
                                <strong>Class:</strong> <code class="bg-gray-100 px-1 rounded">{{ $info['name'] }}</code>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                <strong>Usage:</strong> {{ $info['usage'] }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div id="services" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">🔧</span>
                        System Services
                    </h3>

                    @foreach($services as $key => $service)
                    <div class="mb-6 border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <h5 class="text-lg font-semibold text-gray-900 mb-2">{{ $service['name'] }}</h5>
                        <p class="text-gray-700 mb-3">{{ $service['description'] }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if(isset($service['port']))
                            <div>
                                <span class="font-semibold text-gray-700">Port:</span>
                                <span class="font-mono text-gray-600">{{ $service['port'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($service['url']))
                            <div>
                                <span class="font-semibold text-gray-700">URL:</span>
                                <a href="{{ $service['url'] }}" target="_blank" class="font-mono text-indigo-600 hover:text-indigo-800">
                                    {{ $service['url'] }}
                                </a>
                            </div>
                            @endif
                            
                            @if(isset($service['status_command']))
                            <div>
                                <span class="font-semibold text-gray-700">Status:</span>
                                <code class="bg-gray-900 text-green-400 px-2 py-1 rounded text-xs">{{ $service['status_command'] }}</code>
                            </div>
                            @endif
                            
                            @if(isset($service['restart_command']))
                            <div>
                                <span class="font-semibold text-gray-700">Restart:</span>
                                <code class="bg-gray-900 text-green-400 px-2 py-1 rounded text-xs">{{ $service['restart_command'] }}</code>
                            </div>
                            @endif
                            
                            @if(isset($service['terminate_command']))
                            <div>
                                <span class="font-semibold text-gray-700">Terminate:</span>
                                <code class="bg-gray-900 text-green-400 px-2 py-1 rounded text-xs">{{ $service['terminate_command'] }}</code>
                            </div>
                            @endif
                            
                            @if(isset($service['logs']))
                            <div class="col-span-2">
                                <span class="font-semibold text-gray-700">Logs:</span>
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $service['logs'] }}</code>
                            </div>
                            @endif
                            
                            @if(isset($service['slow_log']))
                            <div class="col-span-2">
                                <span class="font-semibold text-gray-700">Slow Log:</span>
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $service['slow_log'] }}</code>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Useful Oneliners --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">💡</span>
                        Useful One-Liners
                    </h3>

                    <div class="space-y-4">
                        <div class="border-l-4 border-indigo-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Check System Resources</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                top -bn1 | grep "Cpu(s)" && free -h && df -h
                            </code>
                        </div>

                        <div class="border-l-4 border-purple-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">View Recent Errors</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                tail -100 /www/storage/logs/laravel.log | grep ERROR
                            </code>
                        </div>

                        <div class="border-l-4 border-green-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Check Active Connections</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                sudo netstat -tuln | grep LISTEN
                            </code>
                        </div>

                        <div class="border-l-4 border-yellow-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">PostgreSQL Active Queries</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                sudo -u postgres psql -d thetradevisor -c "SELECT pid, now() - query_start as duration, query FROM pg_stat_activity WHERE state = 'active';"
                            </code>
                        </div>

                        <div class="border-l-4 border-red-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Clear All Caches</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
                            </code>
                        </div>

                        <div class="border-l-4 border-blue-500 pl-4">
                            <h6 class="font-semibold text-gray-700 mb-2">Monitor Real-Time Logs</h6>
                            <code class="bg-gray-900 text-green-400 px-3 py-2 rounded block text-sm">
                                tail -f /www/storage/logs/laravel.log
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documentation Links --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-2xl mr-2">📚</span>
                        Documentation Files
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="#" class="border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-indigo-300 transition">
                            <h5 class="font-semibold text-indigo-600 mb-2">API Error Codes</h5>
                            <p class="text-sm text-gray-600 mb-2">Complete API error code reference</p>
                            <code class="text-xs text-gray-500">/www/docs/API_ERROR_CODES.md</code>
                        </a>

                        <a href="#" class="border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-indigo-300 transition">
                            <h5 class="font-semibold text-indigo-600 mb-2">Inactive Accounts Cleanup</h5>
                            <p class="text-sm text-gray-600 mb-2">180-day data retention system</p>
                            <code class="text-xs text-gray-500">/www/docs/INACTIVE_ACCOUNTS_CLEANUP.md</code>
                        </a>

                        <a href="#" class="border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-indigo-300 transition">
                            <h5 class="font-semibold text-indigo-600 mb-2">404 Page Features</h5>
                            <p class="text-sm text-gray-600 mb-2">Interactive Space Invaders 404</p>
                            <code class="text-xs text-gray-500">/www/docs/404_PAGE_FEATURES.md</code>
                        </a>

                        <a href="#" class="border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-indigo-300 transition">
                            <h5 class="font-semibold text-indigo-600 mb-2">System Crash Postmortem</h5>
                            <p class="text-sm text-gray-600 mb-2">Nov 12, 2025 incident analysis</p>
                            <code class="text-xs text-gray-500">/www/docs/SYSTEM_CRASH_POSTMORTEM.md</code>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full mb-4">
                    <span class="text-2xl">⚠️</span>
                </div>
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 text-center mb-2"></h3>
                <p id="modalMessage" class="text-sm text-gray-600 text-center mb-6"></p>
                
                <div class="flex gap-3">
                    <button onclick="closeConfirmModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition">
                        Cancel
                    </button>
                    <button id="confirmButton" onclick="executeAction()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Result Modal --}}
    <div id="resultModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Action Result</h3>
                    <button onclick="closeResultModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div id="resultContent" class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm max-h-96 overflow-y-auto"></div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeResultModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Code block styling */
        code {
            font-family: 'Courier New', monospace;
        }
    </style>

    <script>
        let currentAction = null;

        function showConfirmModal(title, message, action) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            currentAction = action;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            currentAction = null;
        }

        function closeResultModal() {
            document.getElementById('resultModal').classList.add('hidden');
        }

        function executeAction() {
            if (!currentAction) return;

            closeConfirmModal();

            // Show loading state
            document.getElementById('resultContent').textContent = 'Executing action...';
            document.getElementById('resultModal').classList.remove('hidden');

            // Execute action based on type
            fetch('/admin/wiki/action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ action: currentAction })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('resultContent').textContent = data.output || 'Action completed successfully!';
                } else {
                    document.getElementById('resultContent').textContent = 'Error: ' + (data.error || 'Unknown error occurred');
                }
            })
            .catch(error => {
                document.getElementById('resultContent').textContent = 'Error: ' + error.message;
            });
        }
    </script>
</x-app-layout>
