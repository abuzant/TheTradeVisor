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
            \Log::warning('API key validation failed: No API key provided', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
            ]);
            
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
            // Log the failed attempt with details
            \Log::warning('API key validation failed: Invalid or inactive API key', [
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
                'api_key_length' => strlen($apiKey),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Check if key exists but user is inactive
            $inactiveUser = User::where('api_key', $apiKey)->first();
            if ($inactiveUser && !$inactiveUser->is_active) {
                \Log::warning('API key belongs to inactive user', [
                    'user_id' => $inactiveUser->id,
                    'user_email' => $inactiveUser->email,
                ]);
            }
            
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
