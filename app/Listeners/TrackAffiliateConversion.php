<?php

namespace App\Listeners;

use App\Events\UserUpgradedSubscription;
use App\Services\AffiliateTrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackAffiliateConversion
{
    protected $trackingService;
    
    /**
     * Create the event listener.
     */
    public function __construct(AffiliateTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserUpgradedSubscription $event): void
    {
        // Track the conversion if user was referred by an affiliate
        if ($event->user->referred_by_affiliate_id) {
            $this->trackingService->trackConversion($event->user, $event->subscriptionTier);
        }
    }
}
