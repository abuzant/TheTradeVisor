<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryOptimizationMiddleware
{
    /**
     * Maximum allowed query execution time in milliseconds
     */
    private const MAX_QUERY_TIME = 5000; // 5 seconds
    
    /**
     * Maximum number of queries per request
     */
    private const MAX_QUERIES = 100;
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start query logging
        $queryCount = 0;
        $slowQueries = [];
        
        DB::listen(function ($query) use (&$queryCount, &$slowQueries) {
            $queryCount++;
            
            // Log slow queries
            if ($query->time > self::MAX_QUERY_TIME) {
                $slowQueries[] = [
                    'sql' => $query->sql,
                    'time' => $query->time,
                    'bindings' => $query->bindings,
                ];
                
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'time' => $query->time . 'ms',
                    'url' => request()->fullUrl(),
                    'user_id' => auth()->id(),
                ]);
            }
        });
        
        $response = $next($request);
        
        // Log if too many queries
        if ($queryCount > self::MAX_QUERIES) {
            Log::warning('Too many queries in single request', [
                'count' => $queryCount,
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);
        }
        
        // Log slow queries summary
        if (!empty($slowQueries)) {
            Log::error('Request with slow queries', [
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
                'slow_query_count' => count($slowQueries),
                'total_queries' => $queryCount,
                'queries' => $slowQueries,
            ]);
        }
        
        return $response;
    }
}
