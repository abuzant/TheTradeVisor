<?php

namespace App\Providers;

use App\Events\UserUpgradedSubscription;
use App\Listeners\TrackAffiliateConversion;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserUpgradedSubscription::class => [
            TrackAffiliateConversion::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
