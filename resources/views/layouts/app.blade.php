<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO Meta Tags --}}
        <title>@yield('title', 'Dashboard - TheTradeVisor | Professional MT5 Trading Analytics')</title>
        <meta name="description" content="@yield('description', 'Track and analyze your MT5 trading performance with TheTradeVisor. Real-time analytics, performance metrics, and comprehensive trading insights for forex traders.')">
        <meta name="keywords" content="MT5 analytics, forex trading, trading performance, MetaTrader 5, trading dashboard, forex analytics, trading metrics, position tracking">
        <meta name="author" content="TheTradeVisor">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">
        
        {{-- Open Graph Meta Tags --}}
        <meta property="og:title" content="@yield('og_title', 'TheTradeVisor - Professional MT5 Trading Analytics')">
        <meta property="og:description" content="@yield('og_description', 'Track and analyze your MT5 trading performance with real-time analytics and comprehensive insights.')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="TheTradeVisor">
        
        {{-- Twitter Card Meta Tags --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter_title', 'TheTradeVisor - MT5 Trading Analytics')">
        <meta name="twitter:description" content="@yield('twitter_description', 'Professional trading analytics for MetaTrader 5 traders.')">

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#4F46E5">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

	    <!-- Google Analytics -->
	    @if(config('services.google_analytics.enabled'))
	    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
	    <script>
	        window.dataLayer = window.dataLayer || [];
	        function gtag(){dataLayer.push(arguments);}
	        gtag('js', new Date());
	        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
	            'anonymize_ip': true,
	            'cookie_flags': 'SameSite=None;Secure'
	        });
	    </script>
	    @endif

	    @if(config('services.recaptcha.enabled'))
	    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	    @endif


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
            @include('layouts.navigation')

            <!-- Page Heading --}
            @isset($header)
                <header class="bg-white/80 backdrop-blur-sm shadow-soft border-b border-gray-200/50">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-footer />
        </div>
        @stack('scripts')
    </body>
</html>

