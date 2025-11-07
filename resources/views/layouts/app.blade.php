<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>

