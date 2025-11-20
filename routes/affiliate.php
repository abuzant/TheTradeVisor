<?php

use App\Http\Controllers\Affiliate\AffiliateAuthController;
use App\Http\Controllers\Affiliate\AffiliateDashboardController;
use App\Http\Controllers\Affiliate\AffiliateTrackingController;
use Illuminate\Support\Facades\Route;

// Public affiliate tracking (join.thetradevisor.com/offers/{slug})
Route::get('/offers/{slug}', [AffiliateTrackingController::class, 'track'])
    ->name('affiliate.track')
    ->middleware('throttle:affiliate_clicks');

// Affiliate Authentication Routes
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    
    // Guest routes
    Route::middleware('guest:affiliate')->group(function () {
        Route::get('/login', [AffiliateAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AffiliateAuthController::class, 'login']);
        Route::get('/register', [AffiliateAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AffiliateAuthController::class, 'register']);
    });
    
    // Authenticated routes
    Route::middleware('auth:affiliate')->group(function () {
        Route::post('/logout', [AffiliateAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [AffiliateDashboardController::class, 'index'])->name('dashboard');
        
        // Analytics
        Route::get('/analytics', [AffiliateDashboardController::class, 'analytics'])->name('analytics');
        
        // Links & Tools
        Route::get('/links', [AffiliateDashboardController::class, 'links'])->name('links');
        
        // Payouts
        Route::get('/payouts', [AffiliateDashboardController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/request', [AffiliateDashboardController::class, 'requestPayout'])->name('payouts.request');
        
        // Settings
        Route::get('/settings', [AffiliateDashboardController::class, 'settings'])->name('settings');
        Route::put('/settings', [AffiliateDashboardController::class, 'updateSettings'])->name('settings.update');
    });
});
