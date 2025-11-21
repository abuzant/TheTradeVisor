<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\EnterpriseApiKey;
use Illuminate\Support\Facades\Log;

class EnterpriseApiAuth
{
    /**
     * Handle an incoming request.
     * Validates enterprise API key from Authorization header
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from Authorization header
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader) {
            return response()->json([
                'success' => false,
                'error' => 'UNAUTHORIZED',
                'message' => 'Authorization header is required. Format: Authorization: Bearer ent_...'
            ], 401);
        }

        // Extract token from "Bearer ent_..."
        if (!preg_match('/^Bearer\s+(ent_\S+)$/i', $authHeader, $matches)) {
            return response()->json([
                'success' => false,
                'error' => 'INVALID_TOKEN_FORMAT',
                'message' => 'Invalid authorization format. Expected: Bearer ent_...'
            ], 401);
        }

        $apiKey = $matches[1];

        // Validate API key exists and is valid
        $enterpriseApiKey = EnterpriseApiKey::where('key', $apiKey)->first();

        if (!$enterpriseApiKey) {
            Log::warning('Invalid enterprise API key attempt', [
                'key_prefix' => substr($apiKey, 0, 10) . '...',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'INVALID_API_KEY',
                'message' => 'Invalid or revoked API key'
            ], 401);
        }

        // Check if the broker is still active
        if (!$enterpriseApiKey->isValid()) {
            Log::warning('Inactive enterprise broker API key used', [
                'broker_id' => $enterpriseApiKey->enterprise_broker_id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'BROKER_INACTIVE',
                'message' => 'Enterprise broker subscription is not active'
            ], 403);
        }

        // Update last used timestamp
        $enterpriseApiKey->markAsUsed();

        // Attach enterprise broker to request for use in controllers
        $request->attributes->set('enterprise_broker', $enterpriseApiKey->enterpriseBroker);
        $request->attributes->set('enterprise_api_key', $enterpriseApiKey);

        Log::info('Enterprise API request authenticated', [
            'broker_id' => $enterpriseApiKey->enterprise_broker_id,
            'broker_name' => $enterpriseApiKey->enterpriseBroker->official_broker_name,
            'endpoint' => $request->path(),
            'method' => $request->method(),
        ]);

        return $next($request);
    }
}
