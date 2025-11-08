<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Circuit Breakers') }}
            </h2>
            <form method="POST" action="{{ route('admin.circuit-breakers.reset-all') }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Reset all circuit breakers?')"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Reset All
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">What are Circuit Breakers?</h3>
                    <p class="text-gray-600 mb-2">
                        Circuit breakers protect your application from cascading failures. When a service fails repeatedly, 
                        the circuit "opens" and stops trying to call it, preventing your app from hanging or crashing.
                    </p>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="bg-green-50 p-4 rounded">
                            <div class="font-bold text-green-800">🟢 CLOSED</div>
                            <div class="text-sm text-gray-600">Normal operation, all requests pass through</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded">
                            <div class="font-bold text-red-800">🔴 OPEN</div>
                            <div class="text-sm text-gray-600">Service failing, requests blocked, using fallback</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded">
                            <div class="font-bold text-yellow-800">🟡 HALF-OPEN</div>
                            <div class="text-sm text-gray-600">Testing if service recovered, limited requests</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($statuses as $status)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $status['friendly_name'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ $status['service'] }}</p>
                                </div>
                                <div class="text-2xl">
                                    @if($status['state'] === 'closed')
                                        🟢
                                    @elseif($status['state'] === 'open')
                                        🔴
                                    @else
                                        🟡
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">State:</span>
                                    <span class="font-semibold
                                        @if($status['state'] === 'closed') text-green-600
                                        @elseif($status['state'] === 'open') text-red-600
                                        @else text-yellow-600
                                        @endif">
                                        {{ strtoupper($status['state']) }}
                                    </span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Failures:</span>
                                    <span class="font-semibold">{{ $status['failures'] }} / {{ $status['threshold'] }}</span>
                                </div>

                                @if($status['open_until'])
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Retry at:</span>
                                        <span class="font-semibold">{{ $status['open_until_human'] }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Retry Timeout:</span>
                                    <span class="font-semibold">{{ $status['retry_timeout'] }}s</span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Health:</span>
                                    <span class="font-semibold {{ $status['healthy'] ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $status['healthy'] ? 'Healthy' : 'Unhealthy' }}
                                    </span>
                                </div>
                            </div>

                            @if($status['state'] !== 'closed')
                                <form method="POST" action="{{ route('admin.circuit-breakers.reset', $status['service']) }}" class="mt-4">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Reset this circuit breaker?')"
                                            class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        Reset Circuit
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">💡 Tips:</h4>
                <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                    <li>Circuit breakers automatically reset after the retry timeout expires</li>
                    <li>If a service keeps failing, check the System Logs for error details</li>
                    <li>Manual reset is useful for testing or after fixing the underlying issue</li>
                    <li>Healthy services show as CLOSED with 0 failures</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
