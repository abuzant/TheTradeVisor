<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $title ?? 'TheTradeVisor - Professional MT4/MT5 Trading Analytics Platform' }}</title>
    <meta name="description" content="{{ $description ?? 'Enterprise-grade trading analytics platform. Real-time data from MT4/MT5 terminals worldwide. Trusted by professional traders and institutions.' }}">
    <meta name="keywords" content="{{ $keywords ?? 'MT4, MT5, trading analytics, forex analytics, trading statistics, broker comparison, professional trading, trading platform' }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $ogTitle ?? $title ?? 'TheTradeVisor - Professional Trading Analytics' }}">
    <meta property="og:description" content="{{ $ogDescription ?? $description ?? 'Enterprise-grade trading analytics platform' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="TheTradeVisor">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle ?? $title ?? 'TheTradeVisor' }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? $description ?? 'Professional Trading Analytics' }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Google Analytics --}}
    @if(config('services.google_analytics.enabled'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            'anonymize_ip': true,
            'page_title': '{{ $title ?? "TheTradeVisor" }}',
            'page_path': '{{ request()->path() }}'
        });
    </script>
    @endif
    
    {{-- Styles --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
    
    @if(config('services.recaptcha.enabled'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    
    {{ $head ?? '' }}
</head>
<body class="bg-gray-50">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-gray-900">TheTradeVisor</a>
                    <span class="ml-3 px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">PROFESSIONAL</span>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/#analytics" class="text-gray-700 hover:text-gray-900 font-medium">Analytics</a>
                    <a href="/features" class="text-gray-700 hover:text-gray-900 font-medium">Features</a>
                    <a href="/pricing" class="text-gray-700 hover:text-gray-900 font-medium">Pricing</a>
                    <a href="/faq" class="text-gray-700 hover:text-gray-900 font-medium">FAQ</a>
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Get Started</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    {{ $slot }}

    <x-footer />

</body>
</html>
