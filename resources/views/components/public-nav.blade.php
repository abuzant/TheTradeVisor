<nav class="bg-white border-b border-gray-200 {{ $fixed ?? false ? 'fixed w-full z-50' : '' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">TheTradeVisor</span>
                </a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                        Get Started Free
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
