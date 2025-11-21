<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnterpriseAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow users who are enterprise admins
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access enterprise portal.');
        }
        
        // Check if user is an enterprise admin
        if (!$user->isEnterpriseAdmin()) {
            abort(403, 'Access denied. Enterprise admin privileges required.');
        }
        
        // Check if user has an associated enterprise broker
        $broker = $user->enterpriseBroker;
        if (!$broker) {
            abort(403, 'No enterprise broker associated with your account.');
        }
        
        // Check if broker is active
        if (!$broker->isCurrentlyActive()) {
            return redirect()->route('dashboard')->with('error', 'Your enterprise subscription is not active. Please contact support.');
        }
        
        return $next($request);
    }
}
