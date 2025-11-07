<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('Authorization');
        
        // Remove 'Bearer ' prefix if present
        if (str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key is required',
                'message' => 'Please provide your API key in the Authorization header'
            ], 401);
        }
        
        // Validate API key
        $user = User::where('api_key', $apiKey)
                    ->where('is_active', true)
                    ->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or inactive'
            ], 401);
        }
        
        // Attach user to request
        $request->merge(['authenticated_user' => $user]);
        
        return $next($request);
    }
}
