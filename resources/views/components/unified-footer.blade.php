<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
            {{-- Product Column --}}
            <div>
                <h4 class="text-white font-semibold mb-4">Product</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('features') }}" class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="{{ route('screenshots') }}" class="hover:text-white transition-colors">Screenshots</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="{{ route('analytics') }}" class="hover:text-white transition-colors">Analytics</a></li>
                </ul>
            </div>

            {{-- Resources Column --}}
            <div>
                <h4 class="text-white font-semibold mb-4">Resources</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/download" class="hover:text-white transition-colors">Download EA</a></li>
                    <li><a href="/faq" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="/documentation" class="hover:text-white transition-colors">Documentation</a></li>
                    <li><a href="/api-docs" class="hover:text-white transition-colors">API</a></li>
                </ul>
            </div>

            {{-- Company Column --}}
            <div>
                <h4 class="text-white font-semibold mb-4">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/about" class="hover:text-white transition-colors">About</a></li>
                    <li><a href="/contact" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="{{ route('for-brokers') }}" class="hover:text-white transition-colors font-medium text-indigo-400">For Brokers</a></li>
                </ul>
            </div>

            {{-- Legal Column --}}
            <div>
                <h4 class="text-white font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} TheTradeVisor. The Professional Trading Analytics Platform for MetaTrader 4/5.</p>
            <p class="mt-2 text-xs text-gray-500">
                MetaTrader, MT4 and MT5 are registered Trademarks of MetaQuotes Ltd.
            </p>
        </div>
    </div>
</footer>
