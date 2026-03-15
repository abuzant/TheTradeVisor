<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCspHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Set CSP header that includes Tailwind CSS CDN
        $csp = "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' www.googletagmanager.com www.google.com www.google-analytics.com fonts.bunny.net cdn.jsdelivr.net www.gstatic.com cdn.tailwindcss.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' fonts.googleapis.com fonts.bunny.net cdn.jsdelivr.net cdn.tailwindcss.com; ";
        $csp .= "font-src 'self' fonts.gstatic.com fonts.googleapis.com fonts.bunny.net; ";
        $csp .= "img-src 'self' data: https:; ";
        $csp .= "connect-src 'self' https:; ";
        $csp .= "frame-src 'self' www.googletagmanager.com;";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        return $response;
    }
}
