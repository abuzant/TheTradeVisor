<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MainDomainOnly
{
    /**
     * Handle an incoming request - block enterprise subdomain from accessing main routes
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Block enterprise subdomain
        if ($host === 'enterprise.thetradevisor.com') {
            return redirect()->route('enterprise.login');
        }
        
        // Allow all other domains (thetradevisor.com, www.thetradevisor.com, localhost)
        return $next($request);
    }
}
