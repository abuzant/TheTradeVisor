<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiKeyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SymbolMappingController;

// Health check endpoint for Cloudflare
Route::get('/healthcheck', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'service' => 'TheTradeVisor'
    ], 200);
})->middleware('throttle:60,1');

// Public pages
Route::get('/', [App\Http\Controllers\PublicController::class, 'landing'])->name('landing');
Route::get('/features', [App\Http\Controllers\PublicController::class, 'features'])->name('features');
Route::get('/screenshots', [App\Http\Controllers\PublicController::class, 'screenshots'])->name('screenshots');
Route::get('/pricing', [App\Http\Controllers\PublicController::class, 'pricing'])->name('pricing');
Route::get('/about', [App\Http\Controllers\PublicController::class, 'about'])->name('about');
Route::get('/faq', [App\Http\Controllers\PublicController::class, 'faq'])->name('faq');
Route::get('/contact', [App\Http\Controllers\PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\PublicController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/docs', [App\Http\Controllers\PublicController::class, 'docs'])->name('docs');
Route::get('/api-docs', [App\Http\Controllers\PublicController::class, 'apiDocs'])->name('api.docs');

// Logged-in Users
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Performance
    Route::get('/performance', [App\Http\Controllers\PerformanceController::class, 'index'])
        ->name('performance');

    // Broker Analytics (with rate limiting and circuit breaker)
    Route::middleware(['rate.limit.broker', 'circuit.breaker:analytics'])->group(function () {
        Route::get('/broker-analytics', [App\Http\Controllers\BrokerAnalyticsController::class, 'index'])
            ->name('broker.analytics');
        Route::get('/broker/{broker}', [App\Http\Controllers\BrokerDetailsController::class, 'show'])
            ->name('broker-details');
    });

    // Global Analytics (with rate limiting and circuit breaker)
    Route::middleware(['rate.limit.analytics', 'circuit.breaker:analytics'])->group(function () {
        Route::get('/analytics/{days?}', [App\Http\Controllers\AnalyticsController::class, 'index'])
            ->name('analytics');
        Route::get('/analytics/countries', [App\Http\Controllers\CountryAnalyticsController::class, 'topTradingCountries'])
            ->name('analytics.countries');
    });

    // User account management
    Route::get('/accounts', [App\Http\Controllers\AccountManagementController::class, 'index'])
        ->name('accounts.index');
    Route::post('/accounts/{account}/pause', [App\Http\Controllers\AccountManagementController::class, 'pause'])
        ->name('accounts.pause');
    Route::post('/accounts/{account}/unpause', [App\Http\Controllers\AccountManagementController::class, 'unpause'])
        ->name('accounts.unpause');
    Route::delete('/accounts/{account}', [App\Http\Controllers\AccountManagementController::class, 'destroy'])
        ->name('accounts.destroy');

    Route::get('/account/{accountId}', [DashboardController::class, 'account'])->name('account.show');

    // API Key management
    Route::get('/settings/api-key', [ApiKeyController::class, 'index'])->name('settings.api-key');
    Route::post('/settings/api-key/regenerate', [ApiKeyController::class, 'regenerate'])->name('settings.api-key.regenerate');

    // Trades
    Route::get('/trades', [App\Http\Controllers\TradesController::class, 'index'])
        ->name('trades.index');
    Route::get('/trades/symbol/{symbol}', [App\Http\Controllers\TradesController::class, 'symbol'])
        ->name('trades.symbol');

    // Export routes (with rate limiting and circuit breaker)
    Route::middleware(['rate.limit.exports', 'circuit.breaker:exports'])->group(function () {
        Route::get('/export/trades/csv', [App\Http\Controllers\ExportController::class, 'exportTradesCsv'])
            ->name('export.trades.csv');
        Route::get('/export/trades/pdf', [App\Http\Controllers\ExportController::class, 'exportTradesPdf'])
            ->name('export.trades.pdf');
        Route::get('/export/symbol/{symbol}/csv', [App\Http\Controllers\ExportController::class, 'exportSymbolCsv'])
            ->name('export.symbol.csv');
        Route::get('/export/dashboard/csv', [App\Http\Controllers\ExportController::class, 'exportDashboardCsv'])
            ->name('export.dashboard.csv');
        Route::get('/export/account-data', [App\Http\Controllers\ExportController::class, 'exportAccountData'])
            ->name('export.account.data');
    });

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Currency Settings
    Route::get('/settings/currency', [App\Http\Controllers\Settings\CurrencyController::class, 'edit'])
        ->name('settings.currency');
    Route::put('/settings/currency', [App\Http\Controllers\Settings\CurrencyController::class, 'update'])
        ->name('settings.currency.update');
    // Country Analytics (must come before parameterized route)
    Route::get('/analytics/countries', [App\Http\Controllers\CountryAnalyticsController::class, 'topTradingCountries'])->name('analytics.countries');
    
    // Global Analytics
    Route::get('/analytics/{days?}', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics')->where('days', '[0-9]+');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Log Viewer
    Route::get('/logs', [App\Http\Controllers\Admin\LogViewerController::class, 'index'])->name('logs');
    Route::get('/logs/download', [App\Http\Controllers\Admin\LogViewerController::class, 'download'])->name('logs.download');

    // Service Management
    Route::get('/services', [App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services');
    Route::post('/services/{service}/restart', [App\Http\Controllers\Admin\ServiceController::class, 'restart'])->name('services.restart');
    Route::post('/services/backend/{instance}/restart', [App\Http\Controllers\Admin\ServiceController::class, 'restartBackend'])->name('services.backend.restart');
    Route::post('/services/horizon/{action}', [App\Http\Controllers\Admin\ServiceController::class, 'horizonControl'])->name('services.horizon');
    Route::post('/services/clear-all-caches', [App\Http\Controllers\Admin\ServiceController::class, 'clearAllCaches'])->name('services.clear-caches');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/suspend', [App\Http\Controllers\Admin\UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [App\Http\Controllers\Admin\UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/regenerate-api-key', [App\Http\Controllers\Admin\UserManagementController::class, 'regenerateApiKey'])->name('users.regenerate-api-key');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');

    // Trade Management
    Route::get('/trades', [App\Http\Controllers\Admin\TradesController::class, 'index'])->name('trades.index');

    // Admin account management
    Route::get('/accounts', [App\Http\Controllers\Admin\AccountManagementController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/{account}/pause', [App\Http\Controllers\Admin\AccountManagementController::class, 'pause'])->name('accounts.pause');
    Route::post('/accounts/{account}/unpause', [App\Http\Controllers\Admin\AccountManagementController::class, 'unpause'])->name('accounts.unpause');
    Route::delete('/accounts/{account}/reset', [App\Http\Controllers\Admin\AccountManagementController::class, 'reset'])->name('accounts.reset');

    Route::get('/plans', function() {
        return view('plans');
    })->name('plans');

    // Symbol Management
    Route::get('/symbols', [SymbolMappingController::class, 'index'])->name('symbols.index');
    Route::post('/symbols/{id}', [SymbolMappingController::class, 'update'])->name('symbols.update');
    Route::post('/symbols/{id}/verify', [SymbolMappingController::class, 'verify'])->name('symbols.verify');
    Route::post('/symbols/bulk-verify', [SymbolMappingController::class, 'bulkVerify'])->name('symbols.bulk-verify');
    Route::put('/symbols/{symbol}', [App\Http\Controllers\Admin\SymbolManagementController::class, 'update'])->name('symbols.update.alt');
    Route::post('/symbols/bulk-normalize', [App\Http\Controllers\Admin\SymbolManagementController::class, 'bulkNormalize'])->name('symbols.bulk-normalize');
    Route::post('/symbols/auto-normalize', [App\Http\Controllers\Admin\SymbolManagementController::class, 'autoNormalize'])->name('symbols.auto-normalize');
    Route::post('/symbols/sync', [App\Http\Controllers\Admin\SymbolManagementController::class, 'syncSymbols'])->name('symbols.sync');
    
    // Rate Limit Management
    Route::get('/rate-limits', [App\Http\Controllers\Admin\RateLimitController::class, 'index'])->name('rate-limits.index');
    Route::post('/rate-limits', [App\Http\Controllers\Admin\RateLimitController::class, 'store'])->name('rate-limits.store');
    Route::put('/rate-limits/{setting}', [App\Http\Controllers\Admin\RateLimitController::class, 'update'])->name('rate-limits.update');
    Route::delete('/rate-limits/{setting}', [App\Http\Controllers\Admin\RateLimitController::class, 'destroy'])->name('rate-limits.destroy');
    Route::patch('/rate-limits/{setting}/toggle', [App\Http\Controllers\Admin\RateLimitController::class, 'toggle'])->name('rate-limits.toggle');
    Route::post('/rate-limits/clear-cache', [App\Http\Controllers\Admin\RateLimitController::class, 'clearCache'])->name('rate-limits.clear-cache');
    Route::get('/rate-limits/statistics', [App\Http\Controllers\Admin\RateLimitController::class, 'statistics'])->name('rate-limits.statistics');
    
    // Circuit Breaker Management
    Route::get('/circuit-breakers', [App\Http\Controllers\Admin\CircuitBreakerController::class, 'index'])->name('circuit-breakers.index');
    Route::post('/circuit-breakers/{service}/reset', [App\Http\Controllers\Admin\CircuitBreakerController::class, 'reset'])->name('circuit-breakers.reset');
    Route::post('/circuit-breakers/reset-all', [App\Http\Controllers\Admin\CircuitBreakerController::class, 'resetAll'])->name('circuit-breakers.reset-all');
});

// Legal pages (public)
Route::get('/terms', [App\Http\Controllers\LegalController::class, 'termsOfService'])->name('terms');
Route::get('/privacy', [App\Http\Controllers\LegalController::class, 'privacyPolicy'])->name('privacy');

require __DIR__.'/auth.php';

Route::get('/debug-session', function() {
    return [
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'session_driver' => config('session.driver'),
        'session_data' => session()->all(),
        'cookies' => request()->cookies->all(),
    ];
})->middleware('web');
