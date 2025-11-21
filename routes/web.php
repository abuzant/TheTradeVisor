<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\MyDigestController;
use App\Http\Controllers\Admin\DigestControlController;
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

/*
|--------------------------------------------------------------------------
| ENTERPRISE SUBDOMAIN ROUTES (enterprise.thetradevisor.com)
|--------------------------------------------------------------------------
| Enterprise broker portal - Login only, no registration, no public pages
| These routes are available on ALL domains but middleware restricts access
*/

// Enterprise login (accessible from any domain, but middleware restricts)
Route::get('/enterprise-login', [App\Http\Controllers\Auth\EnterpriseLoginController::class, 'showLoginForm'])
    ->middleware('enterprise.subdomain')
    ->name('enterprise.login');
Route::post('/enterprise-login', [App\Http\Controllers\Auth\EnterpriseLoginController::class, 'login'])
    ->middleware(['enterprise.subdomain', 'recaptcha'])
    ->name('enterprise.login.submit');
Route::post('/enterprise-logout', [App\Http\Controllers\Auth\EnterpriseLoginController::class, 'logout'])
    ->middleware('enterprise.subdomain')
    ->name('enterprise.logout');

// Enterprise authenticated routes
Route::middleware(['enterprise.subdomain', 'auth', 'enterprise.admin'])->prefix('enterprise')->name('enterprise.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\EnterpriseController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [App\Http\Controllers\EnterpriseController::class, 'analytics'])->name('analytics');
    Route::get('/accounts', [App\Http\Controllers\EnterpriseController::class, 'accounts'])->name('accounts');
    Route::get('/settings', [App\Http\Controllers\EnterpriseController::class, 'settings'])->name('settings');
    Route::post('/settings', [App\Http\Controllers\EnterpriseController::class, 'updateSettings'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| MAIN DOMAIN ROUTES (thetradevisor.com)
|--------------------------------------------------------------------------
| Public pages, user registration, trader portal
*/

// Public pages (with main domain middleware to block enterprise subdomain)
Route::middleware('main.domain')->group(function () {
Route::get('/', [App\Http\Controllers\PublicController::class, 'landing'])->name('landing');
Route::get('/features', [App\Http\Controllers\PublicController::class, 'features'])->name('features');
Route::get('/screenshots', [App\Http\Controllers\PublicController::class, 'screenshots'])->name('screenshots');
Route::get('/pricing', [App\Http\Controllers\PublicController::class, 'pricing'])->name('pricing');
Route::get('/about', [App\Http\Controllers\PublicController::class, 'about'])->name('about');
Route::get('/faq', [App\Http\Controllers\PublicController::class, 'faq'])->name('faq');
Route::get('/contact', [App\Http\Controllers\PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\PublicController::class, 'contactSubmit'])
    ->middleware('recaptcha')
    ->name('contact.submit');
Route::get('/docs', [App\Http\Controllers\PublicController::class, 'docs'])->name('docs');
Route::get('/api-docs', [App\Http\Controllers\PublicController::class, 'apiDocs'])->name('api.docs');
Route::get('/download', [App\Http\Controllers\PublicController::class, 'download'])->name('download');
Route::get('/download/setup', [App\Http\Controllers\PublicController::class, 'downloadSetup'])->name('download.setup');
Route::get('/for-brokers', [App\Http\Controllers\PublicController::class, 'forBrokers'])->name('for-brokers');

// Legal Pages
Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');
Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

// Public Broker Analytics (SEO-friendly, aggregated data from all users)
Route::get('/broker/{broker}', [App\Http\Controllers\BrokerDetailsController::class, 'show'])
    ->middleware('web')
    ->name('broker-details');

// Logged-in Users
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // My Digest (preview page for trading digest)
    Route::get('/my-digest', [MyDigestController::class, 'show'])
        ->name('digest.show');

    // Performance
    Route::get('/performance', [App\Http\Controllers\PerformanceController::class, 'index'])
        ->name('performance');

    // Broker Analytics (with rate limiting and circuit breaker)
    Route::middleware(['rate.limit.broker', 'circuit.breaker:analytics'])->group(function () {
        Route::get('/broker-analytics', [App\Http\Controllers\BrokerAnalyticsController::class, 'index'])
            ->name('broker.analytics');
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
    Route::get('/accounts/{account}/snapshots', [App\Http\Controllers\AccountSnapshotViewController::class, 'index'])
        ->name('account.snapshots');
    Route::get('/account-health', [App\Http\Controllers\AccountSnapshotViewController::class, 'accountHealth'])
        ->name('account.health');

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

    // Profile digest preferences
    Route::post('/profile/digests', [\App\Http\Controllers\ProfileDigestController::class, 'update'])
        ->name('profile.digests.update');

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

    // Enterprise Broker Management
    Route::prefix('brokers')->name('brokers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BrokerManagementController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\BrokerManagementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\BrokerManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\BrokerManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\BrokerManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\BrokerManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\BrokerManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Admin\BrokerManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/extend-subscription', [App\Http\Controllers\Admin\BrokerManagementController::class, 'extendSubscription'])->name('extend-subscription');
        Route::post('/{id}/api-keys', [App\Http\Controllers\Admin\BrokerManagementController::class, 'createApiKey'])->name('api-keys.create');
        Route::delete('/{brokerId}/api-keys/{keyId}', [App\Http\Controllers\Admin\BrokerManagementController::class, 'revokeApiKey'])->name('api-keys.revoke');
        Route::get('/{id}/accounts', [App\Http\Controllers\Admin\BrokerManagementController::class, 'accounts'])->name('accounts');
    });
    Route::post('/symbols/sync', [App\Http\Controllers\Admin\SymbolManagementController::class, 'syncSymbols'])->name('symbols.sync');

    // Digest Control
    Route::get('/digest-control', [DigestControlController::class, 'index'])->name('digest-control.index');
    Route::post('/digest-control/toggle', [DigestControlController::class, 'toggle'])->name('digest-control.toggle');
    Route::post('/digest-control/toggle-llm', [DigestControlController::class, 'toggleLlm'])->name('digest-control.toggle-llm');
    Route::post('/digest-control/test', [DigestControlController::class, 'testGenerate'])->name('digest-control.test');
    
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
    
    // Admin Wiki
    Route::get('/wiki', [App\Http\Controllers\Admin\AdminWikiController::class, 'index'])->name('wiki');
    Route::post('/wiki/action', [App\Http\Controllers\Admin\AdminWikiController::class, 'executeAction'])->name('wiki.action');
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

}); // End of main domain middleware group

// Fallback for enterprise subdomain (must be AFTER main domain group)
Route::fallback(function () {
    $host = request()->getHost();
    if ($host === 'enterprise.thetradevisor.com') {
        return redirect()->route('enterprise.login');
    }
    abort(404);
})->middleware('web');
