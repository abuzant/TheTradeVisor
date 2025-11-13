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
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="#artisan-commands" class="bg-white/20 hover:bg-white/30 rounded-lg p-4 text-center transition">
                            <div class="text-3xl mb-2">⚡</div>
                            <div class="font-semibold">Artisan Commands</div>
                        </a>
                        <a href="#scripts" class="bg-white/20 hover:bg-white/30 rounded-lg p-4 text-center transition">
                            <div class="text-3xl mb-2">📜</div>
                            <div class="font-semibold">Shell Scripts</div>
                        </a>
                        <a href="#scheduled-tasks" class="bg-white/20 hover:bg-white/30 rounded-lg p-4 text-center transition">
                            <div class="text-3xl mb-2">⏰</div>
                            <div class="font-semibold">Scheduled Tasks</div>
                        </a>
                        <a href="#services" class="bg-white/20 hover:bg-white/30 rounded-lg p-4 text-center transition">
                            <div class="text-3xl mb-2">🔧</div>
                            <div class="font-semibold">Services</div>
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
</x-app-layout>
