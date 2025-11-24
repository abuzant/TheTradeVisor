<x-public-layout>
    <x-slot name="title">Pricing - TheTradeVisor | Free Trading Analytics</x-slot>
    <x-slot name="description">100% FREE for traders with unlimited accounts. Enterprise solutions for brokers at $999/month. No credit card required.</x-slot>
    <x-slot name="keywords">free trading analytics, MT4 MT5 free, trading platform free, broker analytics</x-slot>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "PriceList",
        "name": "TheTradeVisor Pricing",
        "description": "Free for traders, Enterprise solutions for brokers",
        "offers": [
            {
                "@type": "Offer",
                "name": "Trader Plan",
                "price": "0",
                "priceCurrency": "USD",
                "priceValidUntil": "{{ now()->addDays(30)->format('Y-m-d') }}",
                "description": "Unlimited trading accounts, real-time analytics, and global market insights. 100% Free.",
                "category": "Free Tier"
            },
            {
                "@type": "Offer",
                "name": "Enterprise Broker Plan",
                "price": "999",
                "priceCurrency": "USD",
                "priceValidUntil": "{{ now()->addDays(30)->format('Y-m-d') }}",
                "description": "For brokers. Includes 180-day data access for all clients, dedicated portal, and REST API.",
                "category": "Enterprise Tier"
            }
        ]
    }
    </script>

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                🎉 Now 100% FREE for All Traders!
            </div>
            <h1 class="text-5xl font-bold text-gray-900 mb-6">Free Trading Analytics.<br/>Unlimited Accounts.</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Professional trading analytics for everyone. No subscriptions, no limits, no credit card required.
            </p>
        </div>
    </section>

    {{-- Pricing Cards --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Path</h2>
                <p class="text-lg text-gray-600">Free for traders, enterprise solutions for brokers</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                
                {{-- For Traders - FREE --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border-2 border-green-300 p-8 relative shadow-xl">
                    <div class="absolute top-4 right-4 bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        100% FREE
                    </div>
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">For Traders</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-6xl font-bold text-gray-900">$0</span>
                            <span class="text-gray-600 ml-2 text-lg">/forever</span>
                        </div>
                        <p class="text-gray-700 font-medium">Everything you need, completely free</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong class="text-lg">Unlimited Trading Accounts</strong><br/><span class="text-sm text-gray-600">Connect as many accounts as you want</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Real-time Analytics</strong><br/><span class="text-sm text-gray-600">Live performance tracking</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>7-180 Days Historical Data</strong><br/><span class="text-sm text-gray-600">Depends on your broker</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Global Analytics</strong><br/><span class="text-sm text-gray-600">Compare with worldwide traders</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Data Export</strong><br/><span class="text-sm text-gray-600">CSV/PDF reports</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>No Credit Card Required</strong><br/><span class="text-sm text-gray-600">Start immediately</span></span>
                        </li>
                    </ul>
                    
                    @guest
                    <a href="{{ route('register') }}" class="block w-full py-4 px-6 text-center bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-bold text-lg hover:from-green-700 hover:to-emerald-700 shadow-lg transform hover:scale-105 transition">
                        Start Free Now →
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="block w-full py-4 px-6 text-center bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-bold text-lg hover:from-green-700 hover:to-emerald-700 shadow-lg">
                        Go to Dashboard →
                    </a>
                    @endguest
                </div>

                {{-- For Brokers - Enterprise --}}
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border-2 border-indigo-300 p-8 relative shadow-xl">
                    <div class="absolute top-4 right-4 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        ENTERPRISE
                    </div>
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">For Brokers</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-6xl font-bold text-gray-900">$999</span>
                            <span class="text-gray-600 ml-2 text-lg">/month</span>
                        </div>
                        <p class="text-gray-700 font-medium">Enterprise analytics for your traders</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong class="text-lg">180-Day Data Access</strong><br/><span class="text-sm text-gray-600">For all your traders automatically</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Dedicated Enterprise Portal</strong><br/><span class="text-sm text-gray-600">Aggregated analytics dashboard</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>REST API Access</strong><br/><span class="text-sm text-gray-600">6 endpoints with full filtering</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Advanced Filtering</strong><br/><span class="text-sm text-gray-600">By country, platform, symbol</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Multiple API Keys</strong><br/><span class="text-sm text-gray-600">Create and manage multiple keys</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800"><strong>Priority Support</strong><br/><span class="text-sm text-gray-600">Dedicated account manager</span></span>
                        </li>
                    </ul>
                    
                    <a href="/contact" class="block w-full py-4 px-6 text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-bold text-lg hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:scale-105 transition">
                        Contact Sales →
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Is TheTradeVisor really 100% free?</h3>
                    <p class="text-gray-600">Yes! Completely free for all traders with unlimited accounts. No credit card required, no hidden fees, no trials that expire.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">How many accounts can I connect?</h3>
                    <p class="text-gray-600">Unlimited! Connect as many MT4/MT5 accounts as you want, completely free.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">What's the difference between standard and enterprise brokers?</h3>
                    <p class="text-gray-600">Standard brokers: 7 days of historical data. Enterprise brokers: 180 days of historical data. If your broker is an enterprise partner, you automatically get extended data access.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">How do I get 180-day data access?</h3>
                    <p class="text-gray-600">If you trade with an enterprise broker partner, you automatically get 180-day historical data access at no cost. Check your analytics page to see your current data access period.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">I'm a broker. How do I become an enterprise partner?</h3>
                    <p class="text-gray-600">Enterprise plans for brokers start at $999/month and include 180-day data access for all your traders, dedicated portal, REST API, and priority support. <a href="/contact" class="text-indigo-600 hover:text-indigo-800 font-semibold">Contact our sales team</a> to learn more.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">What happens to inactive accounts?</h3>
                    <p class="text-gray-600">Account data is automatically deleted after 180 days of inactivity. This helps us maintain optimal performance and security for active users.</p>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Do you accept donations or tips?</h3>
                    <p class="text-gray-600">We appreciate the thought! Our platform is funded by enterprise broker partnerships, which allows us to keep it 100% free for traders. The best way to support us is to tell other traders about TheTradeVisor.</p>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
