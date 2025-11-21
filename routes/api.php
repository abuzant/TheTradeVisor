<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataCollectionController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AccountSnapshotController;
use App\Http\Controllers\Api\Enterprise\EnterpriseApiController;

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
        
        // Account Snapshots
        Route::get('/accounts/{account}/snapshots', [AccountSnapshotController::class, 'accountSnapshots']);
        Route::get('/accounts/{account}/snapshots/export', [AccountSnapshotController::class, 'export']);
        Route::get('/accounts/{account}/snapshots/stats', [AccountSnapshotController::class, 'stats']);
        Route::get('/users/me/snapshots', [AccountSnapshotController::class, 'userSnapshots']);
        
    });
    
});

// Enterprise API routes (separate authentication)
Route::prefix('enterprise/v1')->middleware(['enterprise.api'])->group(function () {
    
    // Endpoint 1: Get all accounts
    Route::get('/accounts', [EnterpriseApiController::class, 'accounts']);
    
    // Endpoint 2: Get aggregated metrics
    Route::get('/metrics', [EnterpriseApiController::class, 'metrics']);
    
    // Endpoint 3: Get performance data
    Route::get('/performance', [EnterpriseApiController::class, 'performance']);
    
    // Endpoint 4: Get top performers
    Route::get('/top-performers', [EnterpriseApiController::class, 'topPerformers']);
    
    // Endpoint 5: Get trading hours analysis
    Route::get('/trading-hours', [EnterpriseApiController::class, 'tradingHours']);
    
    // Endpoint 6: Export data
    Route::get('/export', [EnterpriseApiController::class, 'export']);
    
});
