<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Temporarily Unavailable - TheTradeVisor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <!-- Icon -->
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-center text-gray-900 mb-4">
                    Service Temporarily Unavailable
                </h1>

                <!-- Message -->
                <p class="text-center text-gray-600 mb-6">
                    Our system is currently experiencing high load. To maintain performance for all users, 
                    we've temporarily disabled some features.
                </p>

                <!-- Feature Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">
                                Feature: <span class="font-bold">{{ ucfirst($feature) }}</span>
                            </p>
                            <p class="text-sm text-blue-700 mt-1">
                                This feature will be available again in approximately {{ floor($retry_after / 60) }} minutes.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                @if(isset($metrics['cpu_usage']) || isset($metrics['memory_usage']))
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Current System Status</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @if(isset($metrics['cpu_usage']))
                        <div>
                            <p class="text-xs text-gray-500">CPU Usage</p>
                            <p class="text-lg font-bold text-gray-900">{{ $metrics['cpu_usage'] }}%</p>
                        </div>
                        @endif
                        @if(isset($metrics['memory_usage']))
                        <div>
                            <p class="text-xs text-gray-500">Memory Usage</p>
                            <p class="text-lg font-bold text-gray-900">{{ $metrics['memory_usage'] }}%</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- What You Can Do -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">What you can do:</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Wait a few minutes and try again</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Access other features that are still available</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Check your dashboard for cached data</span>
                        </li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Go to Dashboard
                    </a>
                    <button onclick="location.reload()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Try Again
                    </button>
                </div>

                <!-- Auto-refresh notice -->
                <p class="text-center text-sm text-gray-500 mt-6">
                    This page will automatically refresh in <span id="countdown">{{ floor($retry_after / 60) }}</span> minutes.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh after retry_after seconds
        setTimeout(function() {
            location.reload();
        }, {{ $retry_after * 1000 }});

        // Countdown timer
        let seconds = {{ $retry_after }};
        setInterval(function() {
            seconds--;
            if (seconds > 0) {
                document.getElementById('countdown').textContent = Math.floor(seconds / 60);
            }
        }, 1000);
    </script>
</body>
</html>
