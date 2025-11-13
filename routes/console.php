<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


use Illuminate\Support\Facades\Schedule;
use App\Services\CurrencyService;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Update currency rates every hour
Schedule::call(function () {
    $currencyService = app(CurrencyService::class);
    $currencyService->updateAllRates();
})->hourly()->name('update-currency-rates')->withoutOverlapping();

// Update GeoIP database every 2 weeks (every 14 days)
Schedule::command('geoip:update')
    ->cron('0 2 */14 * *')  // Every 14 days at 2:00 AM
    ->name('update-geoip-database')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::error('GeoIP database update failed');
    })
    ->onSuccess(function () {
        \Log::info('GeoIP database updated successfully');
    });

// Cleanup inactive accounts daily at 3:00 AM
Schedule::command('accounts:cleanup-inactive')
    ->dailyAt('03:00')
    ->name('cleanup-inactive-accounts')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::error('Inactive accounts cleanup failed');
    })
    ->onSuccess(function () {
        \Log::info('Inactive accounts cleanup completed successfully');
    });
