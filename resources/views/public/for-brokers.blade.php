<x-public-layout>
    <x-slot name="title">For Brokers - TheTradeVisor Enterprise Solution</x-slot>
    <x-slot name="description">Empower your traders with professional analytics. TheTradeVisor Enterprise provides unlimited account tracking for your clients - completely free.</x-slot>
    <x-slot name="keywords">broker analytics, MT4 MT5 broker, trading analytics for brokers, enterprise trading platform</x-slot>

    <x-slot name="head">
        <style>
            .hero-gradient { background: linear-gradient(135deg, #312e81 0%, #581c87 50%, #3730a3 100%); }
            .text-gradient { background: linear-gradient(90deg, #60a5fa 0%, #c084fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
            .feature-card { transition: all 0.3s ease; }
            .feature-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
            .pricing-glow { box-shadow: 0 25px 60px rgba(79, 70, 229, 0.3); }
        </style>
    </x-slot>

    {{-- Hero Section --}}
    <section class="hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    Empower Your Traders.<br/>
                    <span class="text-gradient">Grow Your Business.</span>
                </h1>
                <p class="text-xl md:text-2xl text-indigo-200 mb-10 max-w-3xl mx-auto">
                    Give your clients unlimited access to professional trading analytics — completely free.
                    Stand out from competitors and attract more traders to your brokerage.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="inline-block px-10 py-4 bg-white text-indigo-900 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-xl">
                        Get Started Today
                    </a>
                    <a href="#features" class="inline-block px-10 py-4 bg-white/10 text-white rounded-lg font-bold text-lg hover:bg-white/20 transition border-2 border-white/30">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Value Proposition --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Brokers Choose TheTradeVisor</h2>
                <p class="text-xl text-gray-600">A win-win solution for you and your traders</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-2xl shadow-lg border border-green-100">
                    <div class="text-5xl mb-4">🎁</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Free for Your Traders</h3>
                    <p class="text-gray-700">
                        Your clients get unlimited account tracking at no cost. No subscription fees, no hidden charges.
                        They'll love trading with you even more.
                    </p>
                </div>

                <div class="feature-card bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl shadow-lg border border-blue-100">
                    <div class="text-5xl mb-4">🚀</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Attract More Clients</h3>
                    <p class="text-gray-700">
                        Stand out from competitors. Traders actively seek brokers offering premium analytics tools.
                        This is your competitive advantage.
                    </p>
                </div>

                <div class="feature-card bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl shadow-lg border border-purple-100">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Enterprise Dashboard</h3>
                    <p class="text-gray-700">
                        Monitor all your traders' performance in one place. Aggregate analytics, insights, and trends
                        across your entire client base.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">What Your Traders Get</h2>
                <p class="text-xl text-gray-600">Professional-grade analytics that keep them engaged</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">📈</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Real-Time Performance Tracking</h4>
                    <p class="text-gray-600">Live balance, equity, profit/loss, and position monitoring across all accounts</p>
                </div>

                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">🎯</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Advanced Analytics</h4>
                    <p class="text-gray-600">Win rate, profit factor, drawdown analysis, and performance metrics</p>
                </div>

                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">📊</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Detailed Reports</h4>
                    <p class="text-gray-600">Daily, weekly, and monthly performance reports with actionable insights</p>
                </div>

                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">🔍</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Trade Analysis</h4>
                    <p class="text-gray-600">Symbol-wise performance, trade duration analysis, and entry/exit patterns</p>
                </div>

                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">📱</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Mobile Ready</h4>
                    <p class="text-gray-600">Access analytics anywhere with our responsive web interface</p>
                </div>

                <div class="feature-card bg-white p-6 rounded-xl shadow-md">
                    <div class="text-3xl mb-3">🔔</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Daily Digest Emails</h4>
                    <p class="text-gray-600">Automated performance summaries delivered to their inbox</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Screenshot Gallery --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">See It In Action</h2>
                <p class="text-xl text-gray-600">Professional analytics your traders will love</p>
            </div>

            <div class="space-y-12">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">📈 Account Performance Tracking</h3>
                    <p class="text-gray-600 text-lg mb-6">Your traders get beautiful, real-time performance dashboards with balance/equity trends and key metrics.</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <img src="{{ asset('platform-screenshots/Account Performance.png') }}" alt="Account Performance" class="w-full h-auto rounded-lg">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-3">📊 Account Overview</h4>
                        <p class="text-gray-600 mb-4">Centralized dashboard for all trading accounts in one view.</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <img src="{{ asset('platform-screenshots/Account Overview.png') }}" alt="Account Overview" class="w-full h-auto rounded-lg">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-3">📉 Risk Analytics</h4>
                        <p class="text-gray-600 mb-4">Advanced risk metrics and drawdown analysis for smarter trading.</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <img src="{{ asset('platform-screenshots/Risk Analytics Dashboard.png') }}" alt="Risk Analytics" class="w-full h-auto rounded-lg">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-3">🔍 Symbol Performance</h4>
                        <p class="text-gray-600 mb-4">Detailed breakdown of trading performance by instrument.</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <img src="{{ asset('platform-screenshots/Symbol Performance.png') }}" alt="Symbol Performance" class="w-full h-auto rounded-lg">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-3">⏰ Trading Sessions</h4>
                        <p class="text-gray-600 mb-4">Analyze performance across different trading sessions and hours.</p>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <img src="{{ asset('platform-screenshots/Trading Session Analysis.png') }}" alt="Trading Sessions" class="w-full h-auto rounded-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-20 bg-gradient-to-br from-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get started in 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Contact Us</h3>
                    <p class="text-gray-700">Reach out to our enterprise team to discuss your needs and get a personalized demo.</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Integration Setup</h3>
                    <p class="text-gray-700">We set up your enterprise dashboard and provide everything your traders need.</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Share with Traders</h3>
                    <p class="text-gray-700">Your clients connect their accounts and start enjoying premium analytics immediately.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Simple, Transparent Pricing</h2>

            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-3xl p-12 pricing-glow">
                <div class="text-6xl font-bold mb-2">$999<span class="text-2xl font-normal">/month</span></div>
                <p class="text-xl text-indigo-200 mb-8">Unlimited traders. Unlimited accounts. Unlimited value.</p>

                <ul class="text-left max-w-md mx-auto space-y-3 mb-10">
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> Unlimited trader accounts</li>
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> Unlimited trading accounts per trader</li>
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> Enterprise dashboard</li>
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> Priority support</li>
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> Custom branding options</li>
                    <li class="flex items-center"><span class="text-green-300 mr-3 text-xl">✓</span> API access</li>
                </ul>

                <a href="#contact" class="inline-block px-10 py-4 bg-white text-indigo-600 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-xl">
                    Get Started Now →
                </a>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section id="contact" class="py-20 bg-gray-900 text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">Ready to Empower Your Traders?</h2>
            <p class="text-xl text-gray-300 mb-10">
                Join leading brokerages worldwide who trust TheTradeVisor to provide their traders with professional analytics tools.
            </p>

            <div class="bg-gray-800 rounded-2xl p-8">
                <p class="text-lg text-gray-300 mb-6">Contact our enterprise team to get started:</p>
                <a href="mailto:enterprise@thetradevisor.com" class="inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    enterprise@thetradevisor.com
                </a>

                <div class="mt-8 pt-8 border-t border-gray-700">
                    <p class="text-gray-400">Or call us at: <span class="text-white font-semibold">+1 (555) 123-4567</span></p>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
