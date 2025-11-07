<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataCollectionController;

// Public health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'TheTradeVisor API',
        'version' => '1.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Protected API routes
Route::middleware('api.key')->group(function () {
    
    // Data collection endpoint
    Route::post('/v1/data/collect', [DataCollectionController::class, 'collect']);
    
});
