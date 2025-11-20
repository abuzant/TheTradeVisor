<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataCollectionController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AccountSnapshotController;
use App\Http\Controllers\Api\AffiliateApiController;

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

// Affiliate API endpoints (requires affiliate authentication)
Route::prefix('v1/affiliate')->middleware('auth:affiliate')->group(function () {
    Route::get('/profile', [AffiliateApiController::class, 'profile']);
    Route::get('/stats', [AffiliateApiController::class, 'stats']);
    Route::get('/performance', [AffiliateApiController::class, 'performance']);
    Route::get('/campaigns', [AffiliateApiController::class, 'campaigns']);
    Route::get('/geo', [AffiliateApiController::class, 'geographic']);
    Route::get('/clicks', [AffiliateApiController::class, 'clicks']);
    Route::get('/conversions', [AffiliateApiController::class, 'conversions']);
    Route::get('/payouts', [AffiliateApiController::class, 'payouts']);
});
