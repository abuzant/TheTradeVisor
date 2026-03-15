<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\ApiKeyValidator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');
        $apiKey = ApiKeyValidator::extractKeyFromAuthorization($authorizationHeader);

        if (!$apiKey) {
            \Log::warning('API key validation failed: No API key provided', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'authorization_header_present' => $authorizationHeader !== null,
                'headers' => $request->headers->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'API key is required',
                'message' => 'Please provide your API key in the Authorization header'
            ], 401);
        }

        $keyType = ApiKeyValidator::detectKeyType($apiKey);

        if (!$keyType) {
            \Log::warning('API key validation failed: Malformed API key format', [
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
                'api_key_length' => strlen($apiKey),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'detected_prefix' => substr($apiKey, 0, 4),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
                'message' => 'The provided API key format is invalid'
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
                'key_type' => $keyType,
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
