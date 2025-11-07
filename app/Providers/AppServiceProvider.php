<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Deal;
use App\Observers\DealObserver;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Deal::observe(DealObserver::class);
    Paginator::useTailwind();
    }
}
