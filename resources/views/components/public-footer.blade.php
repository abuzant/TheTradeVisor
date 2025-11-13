<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-4">TheTradeVisor</h3>
                <p class="text-sm">Global trading analytics platform for MT5 traders.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Product</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/#features" class="hover:text-white">Features</a></li>
                    @auth
                        <li><a href="{{ route('analytics') }}" class="hover:text-white">Analytics</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('register') }}" class="hover:text-white">Get Started</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="mailto:hello@thetradevisor.com" class="hover:text-white">Contact</a></li>
                    @auth
                        <li><a href="{{ route('settings.api-key') }}" class="hover:text-white">API Key</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} TheTradeVisor. All rights reserved.</p>
            <p class="mt-2 text-xs text-gray-500">
                MetaTrader 4 and MetaTrader 5 are registered trademarks of MetaQuotes Ltd.
            </p>
        </div>
    </div>
</footer>
