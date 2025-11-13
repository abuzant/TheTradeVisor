<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\QueryLoggingServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Use custom TrustProxies with Cloudflare IPs
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
        ]);
        
        // TEMPORARY: Exclude login from CSRF for debugging
        $middleware->validateCsrfTokens(except: [
            'login',
            'register',
        ]);
        
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ValidateApiKey::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
            'track.country' => \App\Http\Middleware\TrackCountryMiddleware::class,
            'track.web.country' => \App\Http\Middleware\TrackWebCountryMiddleware::class,
            'api.rate.limit' => \App\Http\Middleware\ApiRateLimiter::class,
            'rate.limit.analytics' => \App\Http\Middleware\RateLimitAnalytics::class,
            'rate.limit.exports' => \App\Http\Middleware\RateLimitExports::class,
            'rate.limit.broker' => \App\Http\Middleware\RateLimitBrokerAnalytics::class,
            'circuit.breaker' => \App\Http\Middleware\CircuitBreakerMiddleware::class,
        ]);

        // Add to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\ExtendedRememberMe::class,
            \App\Http\Middleware\TrackWebCountryMiddleware::class,
            \App\Http\Middleware\QueryOptimizationMiddleware::class,
        ]);

        // Add to api middleware group
        $middleware->api(append: [
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\TrackCountryMiddleware::class,
        ]);

    })
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // Force JSON responses for all API routes
        $exceptions->shouldRenderJsonWhen(function ($request, $exception) {
            return $request->is('api/*');
        });
        
        // Custom API error responses
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'NOT_FOUND',
                    'message' => 'The requested endpoint does not exist',
                    'path' => $request->path()
                ], 404);
            }
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'METHOD_NOT_ALLOWED',
                    'message' => 'The HTTP method is not allowed for this endpoint',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? 'Unknown'
                ], 405);
            }
        });
        
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'The provided data is invalid',
                    'errors' => $e->errors()
                ], 422);
            }
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'UNAUTHENTICATED',
                    'message' => 'Authentication is required to access this endpoint'
                ], 401);
            }
        });
        
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'FORBIDDEN',
                    'message' => 'You do not have permission to access this resource'
                ], 403);
            }
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null
                ], 429);
            }
        });
        
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'RESOURCE_NOT_FOUND',
                    'message' => 'The requested resource was not found'
                ], 404);
            }
        });
        
        // Catch-all for any other exceptions on API routes
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                // Log the error for debugging
                \Log::error('API Exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip()
                ]);
                
                // Don't expose internal errors in production
                $message = app()->environment('production') 
                    ? 'An internal server error occurred' 
                    : $e->getMessage();
                
                return response()->json([
                    'success' => false,
                    'error' => 'INTERNAL_SERVER_ERROR',
                    'message' => $message
                ], 500);
            }
        });
    })->create();
