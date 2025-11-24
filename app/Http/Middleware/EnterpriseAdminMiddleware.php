<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnterpriseAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow authenticated enterprise admins
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if authenticated with enterprise guard
        if (!Auth::guard('enterprise')->check()) {
            return redirect()->route('enterprise.login')->with('error', 'Please login to access enterprise portal.');
        }
        
        $admin = Auth::guard('enterprise')->user();
        
        // Check if admin is active
        if (!$admin->is_active) {
            Auth::guard('enterprise')->logout();
            return redirect()->route('enterprise.login')->with('error', 'Your account has been deactivated.');
        }
        
        // Check if admin has an associated enterprise broker
        $broker = $admin->enterpriseBroker;
        if (!$broker) {
            abort(403, 'No enterprise broker associated with your account.');
        }
        
        // Check if broker is active
        if (!$broker->isCurrentlyActive()) {
            return redirect()->route('enterprise.login')->with('error', 'Your enterprise subscription is not active. Please contact support.');
        }
        
        return $next($request);
    }
}
