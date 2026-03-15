<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Log Selection and Controls --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.logs') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            {{-- Log Type Selection --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Log File</label>
                                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="this.form.submit()">
                                    @foreach($availableLogs as $key => $log)
                                        <option value="{{ $key }}" {{ $logType === $key ? 'selected' : '' }}>
                                            {{ $log['name'] }} ({{ $log['size'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lines Selection --}}
                            <div>
                                <label for="lines" class="block text-sm font-medium text-gray-700 mb-2">Lines to Show</label>
                                <select name="lines" id="lines" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="this.form.submit()">
                                    <option value="50" {{ $lines == 50 ? 'selected' : '' }}>Last 50 lines</option>
                                    <option value="100" {{ $lines == 100 ? 'selected' : '' }}>Last 100 lines</option>
                                    <option value="200" {{ $lines == 200 ? 'selected' : '' }}>Last 200 lines</option>
                                    <option value="500" {{ $lines == 500 ? 'selected' : '' }}>Last 500 lines</option>
                                    <option value="1000" {{ $lines == 1000 ? 'selected' : '' }}>Last 1000 lines</option>
                                </select>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                    Refresh
                                </button>
                                <a href="{{ route('admin.logs.download', ['type' => $logType]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Log Display --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $availableLogs[$logType]['name'] ?? 'Log Content' }}
                        </h3>
                        @if(isset($availableLogs[$logType]))
                            <span class="text-sm text-gray-500">
                                Last modified: {{ date('Y-m-d H:i:s', $availableLogs[$logType]['modified']) }}
                            </span>
                        @endif
                    </div>

                    @if($error)
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <p class="text-sm text-red-800">{{ $error }}</p>
                        </div>
                    @elseif(empty($logContent))
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <p class="text-sm text-gray-600">Log file is empty</p>
                        </div>
                    @else
                        <div class="bg-gray-900 rounded-md p-4 overflow-x-auto">
                            <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap">{{ $logContent }}</pre>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Available Logs Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Log Files</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($availableLogs as $key => $log)
                            <div class="border border-gray-200 rounded-md p-4 {{ $logType === $key ? 'bg-indigo-50 border-indigo-300' : '' }}">
                                <h4 class="font-semibold text-sm text-gray-900">{{ $log['name'] }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Size: {{ $log['size'] }}</p>
                                <p class="text-xs text-gray-500">Modified: {{ date('H:i:s', $log['modified']) }}</p>
                                <a href="{{ route('admin.logs', ['type' => $key]) }}" class="text-xs text-indigo-600 hover:text-indigo-900 mt-2 inline-block">
                                    View →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
