<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpgradedSubscription
{
    use Dispatchable, SerializesModels;

    public User $user;
    public string $subscriptionTier;

    public function __construct(User $user, string $subscriptionTier)
    {
        $this->user = $user;
        $this->subscriptionTier = $subscriptionTier;
    }
}
