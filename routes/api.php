<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataCollectionController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\AnalyticsController;

// Public health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'TheTradeVisor API',
        'version' => '1.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Protected API routes with rate limiting
Route::middleware(['api.key', 'api.rate.limit'])->group(function () {
    
    // Data collection endpoint (for EA)
    Route::post('/v1/data/collect', [DataCollectionController::class, 'collect']);
    
    // User API endpoints
    Route::prefix('v1')->group(function () {
        
        // Accounts
        Route::get('/accounts', [AccountController::class, 'index']);
        Route::get('/accounts/{id}', [AccountController::class, 'show']);
        
        // Trades
        Route::get('/trades', [TradeController::class, 'index']);
        
        // Analytics
        Route::get('/analytics/performance', [AnalyticsController::class, 'performance']);
        
    });
    
});
