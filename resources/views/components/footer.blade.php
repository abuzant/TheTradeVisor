<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <!-- Left side - Copyright -->
            <div class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

            <!-- Center - Links -->
            <div class="flex space-x-6 text-sm">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 transition">Dashboard</a>
                <a href="{{ route('analytics') }}" class="text-gray-500 hover:text-gray-700 transition">Analytics</a>
                <a href="{{ route('performance') }}" class="text-gray-500 hover:text-gray-700 transition">Performance</a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 transition font-medium">Admin</a>
                @endif
            </div>

            <!-- Right side - Version & Status -->
            <div class="text-sm text-gray-500">
                <span class="inline-flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    V2 Beta
                </span>
            </div>
        </div>
    </div>
</footer>
