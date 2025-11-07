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
