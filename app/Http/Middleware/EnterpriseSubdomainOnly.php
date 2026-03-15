<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnterpriseSubdomainOnly
{
    /**
     * Handle an incoming request - ensure it's from enterprise subdomain
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Allow enterprise.thetradevisor.com
        if ($host === 'enterprise.thetradevisor.com') {
            return $next($request);
        }
        
        // Redirect to main site if accessed from wrong domain
        return redirect('https://thetradevisor.com' . $request->getRequestUri());
    }
}
