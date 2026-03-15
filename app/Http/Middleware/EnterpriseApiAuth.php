<?php

namespace App\Http\Middleware;

use App\Models\EnterpriseApiKey;
use App\Support\ApiKeyValidator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        $authorizationHeader = $request->header('Authorization');

        if (!ApiKeyValidator::hasAuthorizationHeader($authorizationHeader)) {
            Log::warning('Enterprise API key validation failed: Missing Authorization header', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'UNAUTHORIZED',
                'message' => 'Authorization header is required. Format: Authorization: Bearer ent_...'
            ], 401);
        }

        $trimmedAuthorization = trim($authorizationHeader);

        if (!str_starts_with(strtolower($trimmedAuthorization), 'bearer ')) {
            Log::warning('Enterprise API key validation failed: Invalid authorization header format', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'authorization_header' => $authorizationHeader,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'INVALID_TOKEN_FORMAT',
                'message' => 'Invalid authorization format. Expected: Bearer ent_...'
            ], 401);
        }

        $apiKey = ApiKeyValidator::extractKeyFromAuthorization($authorizationHeader);
        $keyType = ApiKeyValidator::detectKeyType($apiKey);

        if ($keyType !== 'enterprise') {
            Log::warning('Enterprise API key validation failed: Malformed API key format', [
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
                'api_key_length' => is_string($apiKey) ? strlen($apiKey) : null,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'detected_prefix' => is_string($apiKey) ? substr($apiKey, 0, 4) : null,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'INVALID_API_KEY_FORMAT',
                'message' => 'The provided enterprise API key format is invalid'
            ], 401);
        }

        // Validate API key exists and is valid
        $enterpriseApiKey = EnterpriseApiKey::where('key', $apiKey)
            ->with('enterpriseBroker')
            ->first();

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
        if (!$enterpriseApiKey->isValid() || !$enterpriseApiKey->enterpriseBroker || !$enterpriseApiKey->enterpriseBroker->is_active) {
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
