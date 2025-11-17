<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-white font-semibold mb-4">Product</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/features" class="hover:text-white">Features</a></li>
                    <li><a href="/screenshots" class="hover:text-white">Screenshots</a></li>
                    <li><a href="/pricing" class="hover:text-white">Pricing</a></li>
                    <li><a href="{{ route('analytics') }}" class="hover:text-white">Analytics</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Resources</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/faq" class="hover:text-white">FAQ</a></li>
                    <li><a href="/docs" class="hover:text-white">Documentation</a></li>
                    <li><a href="/api-docs" class="hover:text-white">API</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/about" class="hover:text-white">About</a></li>
                    <li><a href="/contact" class="hover:text-white">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/privacy" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="/terms" class="hover:text-white">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>© {{ date('Y') }} TheTradeVisor. Professional Trading Analytics Platform.</p>
            <p><small>MetaTrader, MT4 and MT5 are registered Trademarks of MetaQuotes Ltd.</small</p>
        </div>
    </div>
</footer>
