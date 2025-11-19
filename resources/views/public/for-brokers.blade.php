<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For Brokers - TheTradeVisor Enterprise Solution</title>
    <meta name="description" content="Empower your traders with professional analytics. TheTradeVisor Enterprise provides unlimited account tracking for your clients - completely free.">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="For Brokers - TheTradeVisor Enterprise Solution">
    <meta property="og:description" content="Empower your traders with professional analytics. Unlimited accounts for your clients - completely free.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    
    {{-- Cloudflare Email Protection --}}
    <script data-cfasync="false">
        // Email obfuscation to prevent scraping
        document.addEventListener('DOMContentLoaded', function() {
            const emailElements = document.querySelectorAll('[data-cfemail]');
            emailElements.forEach(function(element) {
                const encoded = element.getAttribute('data-cfemail');
                if (encoded && encoded !== element.textContent) {
                    // Email is already visible, just protect the attribute
                    element.removeAttribute('data-cfemail');
                }
            });
        });
    </script>
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    Empower Your Traders.<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                        Grow Your Business.
                    </span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Give your clients unlimited access to professional trading analytics - completely free.
                    Stand out from competitors and attract more traders to your brokerage.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="group px-10 py-5 bg-gradient-to-r from-white to-gray-50 text-indigo-900 rounded-xl font-bold text-lg hover:from-gray-50 hover:to-white transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 border-2 border-white/20">
                        <span class="flex items-center justify-center gap-2">
                            Get Started Today
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </a>
                    <a href="#features" class="px-10 py-5 bg-white/10 text-white rounded-xl font-bold text-lg hover:bg-white/20 transition-all duration-300 border-2 border-white/30 backdrop-blur-sm hover:border-white/50">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Value Proposition --}}
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Brokers Choose TheTradeVisor</h2>
                <p class="text-xl text-gray-600">A win-win solution for you and your traders</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-2xl shadow-lg border border-green-100">
                    <div class="text-5xl mb-4">🎁</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Free for Your Traders</h3>
                    <p class="text-gray-700">
                        Your clients get unlimited account tracking at no cost. No subscription fees, no hidden charges.
                        They'll love trading with you even more.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl shadow-lg border border-blue-100">
                    <div class="text-5xl mb-4">🚀</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Attract More Clients</h3>
                    <p class="text-gray-700">
                        Stand out from competitors. Traders actively seek brokers offering premium analytics tools.
                        This is your competitive advantage.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl shadow-lg border border-purple-100">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Enterprise Dashboard</h3>
                    <p class="text-gray-700">
                        Monitor all your traders' performance in one place. Aggregate analytics, insights, and trends
                        across your entire client base.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">What Your Traders Get</h2>
                <p class="text-xl text-gray-600">Professional-grade analytics that keep them engaged</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">📈</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Real-Time Performance Tracking</h4>
                    <p class="text-gray-600">Live balance, equity, profit/loss, and position monitoring across all accounts</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">🎯</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Advanced Analytics</h4>
                    <p class="text-gray-600">Win rate, profit factor, drawdown analysis, and performance metrics</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">📊</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Interactive Charts</h4>
                    <p class="text-gray-600">Beautiful visualizations of trading history and account snapshots</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">🌍</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Global Insights</h4>
                    <p class="text-gray-600">Compare performance with traders worldwide, symbol analytics, and trends</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">📱</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Multi-Account Management</h4>
                    <p class="text-gray-600">Track unlimited MT4 and MT5 accounts in one unified dashboard</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow">
                    <div class="text-3xl mb-3">🔔</div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Daily Digest Emails</h4>
                    <p class="text-gray-600">Automated performance summaries delivered to their inbox</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Screenshot Gallery --}}
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">See It In Action</h2>
                <p class="text-xl text-gray-600">Professional analytics your traders will love</p>
            </div>
            
            {{-- Interactive Screenshot Carousel --}}
            <div class="relative" id="screenshot-gallery">
                {{-- Main Display --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-3xl shadow-2xl overflow-hidden mb-8">
                    <div class="p-8">
                        {{-- Screenshot Info --}}
                        <div class="flex items-center gap-4 mb-6">
                            <span class="text-5xl screenshot-emoji">📈</span>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 screenshot-title">Account Performance</h3>
                                <p class="text-gray-600 screenshot-description">Real-time profit/loss tracking and performance metrics</p>
                            </div>
                        </div>
                        
                        {{-- Main Image --}}
                        <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden">
                            <img id="main-screenshot" src="{{ asset('platform-screenshots/Account Performance.png') }}" alt="Screenshot" class="w-full h-auto transition-opacity duration-300">
                            
                            {{-- Navigation Arrows --}}
                            <button onclick="previousScreenshot()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-900 rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button onclick="nextScreenshot()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-900 rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Counter --}}
                        <div class="text-center mt-6">
                            <span class="text-sm font-semibold text-gray-600">
                                <span id="current-slide">1</span> / <span id="total-slides">6</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Thumbnail Navigation --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <button onclick="showScreenshot(0)" class="screenshot-thumb active group">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border-2 border-transparent group-hover:border-blue-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">📈</div>
                            <p class="text-sm font-semibold text-gray-900">Performance</p>
                        </div>
                    </button>
                    
                    <button onclick="showScreenshot(1)" class="screenshot-thumb group">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border-2 border-transparent group-hover:border-purple-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">📊</div>
                            <p class="text-sm font-semibold text-gray-900">Overview</p>
                        </div>
                    </button>
                    
                    <button onclick="showScreenshot(2)" class="screenshot-thumb group">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border-2 border-transparent group-hover:border-green-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">💼</div>
                            <p class="text-sm font-semibold text-gray-900">Open Trades</p>
                        </div>
                    </button>
                    
                    <button onclick="showScreenshot(3)" class="screenshot-thumb group">
                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-4 border-2 border-transparent group-hover:border-orange-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">📋</div>
                            <p class="text-sm font-semibold text-gray-900">History</p>
                        </div>
                    </button>
                    
                    <button onclick="showScreenshot(4)" class="screenshot-thumb group">
                        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-4 border-2 border-transparent group-hover:border-cyan-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">🌐</div>
                            <p class="text-sm font-semibold text-gray-900">Sessions</p>
                        </div>
                    </button>
                    
                    <button onclick="showScreenshot(5)" class="screenshot-thumb group">
                        <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl p-4 border-2 border-transparent group-hover:border-violet-500 transition-all duration-300 group-hover:shadow-lg">
                            <div class="text-3xl mb-2">⚡</div>
                            <p class="text-sm font-semibold text-gray-900">Platforms</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- View All Link --}}
            <div class="text-center mt-12">
                <a href="{{ route('screenshots') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                    View All Screenshots
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        const screenshots = [
            {
                emoji: '📈',
                title: 'Account Performance',
                description: 'Real-time profit/loss tracking and performance metrics',
                image: '{{ asset("platform-screenshots/Account Performance.png") }}'
            },
            {
                emoji: '📊',
                title: 'Account Overview',
                description: 'Centralized dashboard for all trading accounts',
                image: '{{ asset("platform-screenshots/Account Overview.png") }}'
            },
            {
                emoji: '💼',
                title: 'Live Open Positions',
                description: 'Monitor active trades in real-time',
                image: '{{ asset("platform-screenshots/Open Positions.png") }}'
            },
            {
                emoji: '📋',
                title: 'Closed Positions',
                description: 'Analyze completed trades and historical data',
                image: '{{ asset("platform-screenshots/Closed Positions Explorer.png") }}'
            },
            {
                emoji: '🌐',
                title: 'Trading Sessions',
                description: 'Performance across different trading sessions',
                image: '{{ asset("platform-screenshots/Trading Session Analysis.png") }}'
            },
            {
                emoji: '⚡',
                title: 'Platform Matrix',
                description: 'Compare performance across brokers',
                image: '{{ asset("platform-screenshots/Platform Performance Matrix.png") }}'
            }
        ];

        let currentIndex = 0;

        function showScreenshot(index) {
            currentIndex = index;
            const screenshot = screenshots[index];
            
            // Fade out
            const mainImg = document.getElementById('main-screenshot');
            mainImg.style.opacity = '0';
            
            setTimeout(() => {
                // Update content
                document.querySelector('.screenshot-emoji').textContent = screenshot.emoji;
                document.querySelector('.screenshot-title').textContent = screenshot.title;
                document.querySelector('.screenshot-description').textContent = screenshot.description;
                mainImg.src = screenshot.image;
                mainImg.alt = screenshot.title;
                
                // Update counter
                document.getElementById('current-slide').textContent = index + 1;
                
                // Update active thumbnail
                document.querySelectorAll('.screenshot-thumb').forEach((thumb, i) => {
                    if (i === index) {
                        thumb.classList.add('active');
                        thumb.querySelector('div').classList.add('ring-4', 'ring-indigo-500', 'scale-105');
                    } else {
                        thumb.classList.remove('active');
                        thumb.querySelector('div').classList.remove('ring-4', 'ring-indigo-500', 'scale-105');
                    }
                });
                
                // Fade in
                mainImg.style.opacity = '1';
            }, 150);
        }

        function nextScreenshot() {
            const nextIndex = (currentIndex + 1) % screenshots.length;
            showScreenshot(nextIndex);
        }

        function previousScreenshot() {
            const prevIndex = (currentIndex - 1 + screenshots.length) % screenshots.length;
            showScreenshot(prevIndex);
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') previousScreenshot();
            if (e.key === 'ArrowRight') nextScreenshot();
        });

        // Set total slides on load
        document.getElementById('total-slides').textContent = screenshots.length;
    </script>

    {{-- How It Works --}}
    <div class="py-20 bg-gradient-to-br from-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get started in 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Contact Us</h3>
                    <p class="text-gray-700">
                        Reach out to our team. We'll verify your brokerage and set up your enterprise account.
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Get Whitelisted</h3>
                    <p class="text-gray-700">
                        Your broker name is added to our whitelist. All your traders get unlimited free access.
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Share with Traders</h3>
                    <p class="text-gray-700">
                        Your clients connect their accounts and start enjoying premium analytics immediately.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Simple, Transparent Pricing</h2>
            
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-3xl p-12 shadow-2xl">
                <div class="text-6xl font-bold mb-4">$999<span class="text-2xl font-normal">/month</span></div>
                <p class="text-2xl mb-8">Unlimited traders. Unlimited accounts. Unlimited value.</p>
                
                <ul class="text-left max-w-md mx-auto space-y-3 mb-8">
                    <li class="flex items-start">
                        <span class="text-green-300 mr-3 text-xl">✓</span>
                        <span>Unlimited traders from your brokerage</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-300 mr-3 text-xl">✓</span>
                        <span>Unlimited trading accounts per trader</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-300 mr-3 text-xl">✓</span>
                        <span>Enterprise dashboard with aggregated analytics</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-300 mr-3 text-xl">✓</span>
                        <span>Priority support and dedicated account manager</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-300 mr-3 text-xl">✓</span>
                        <span>Custom branding options available</span>
                    </li>
                </ul>

                <a href="#contact" class="group inline-flex items-center gap-3 px-12 py-5 bg-white text-indigo-900 rounded-xl font-bold text-xl hover:bg-gray-50 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1">
                    Get Started Now
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Contact Section --}}
    <div id="contact" class="py-20 bg-gray-900 text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">Ready to Empower Your Traders?</h2>
            <p class="text-xl text-gray-300 mb-8">
                Join leading brokerages worldwide who trust TheTradeVisor to provide their traders
                with professional analytics tools.
            </p>
            
            <div class="bg-gray-800 rounded-2xl p-8">
                <p class="text-lg mb-6">Contact our enterprise team to get started:</p>
                <a href="mailto:enterprise@thetradevisor.com" class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span data-cfemail="[email protected]">enterprise@thetradevisor.com</span>
                </a>
                
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <p class="text-gray-400">Or call us at: <span class="text-white font-semibold">+1 (555) 123-4567</span></p>
                </div>
            </div>
        </div>
    </div>

    <x-unified-footer />
</body>
</html>
