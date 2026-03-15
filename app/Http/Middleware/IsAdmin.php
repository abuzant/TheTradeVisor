<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user is admin (we'll add is_admin column or check email)
        if (!$user || !$user->is_admin) {
            abort(403, 'Unauthorized access');
        }
        
        return $next($request);
    }
}
