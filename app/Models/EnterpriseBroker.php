<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnterpriseBroker extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'official_broker_name',
        'is_active',
        'monthly_fee',
        'subscription_ends_at',
        'grace_period_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'monthly_fee' => 'decimal:2',
        'subscription_ends_at' => 'datetime',
        'grace_period_ends_at' => 'datetime',
    ];

    /**
     * Get the user (admin) who owns this enterprise broker
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all usage records for this broker
     */
    public function usageRecords(): HasMany
    {
        return $this->hasMany(WhitelistedBrokerUsage::class);
    }

    /**
     * Get all API keys for this broker
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(EnterpriseApiKey::class);
    }

    /**
     * Check if broker is currently active (including grace period)
     */
    public function isCurrentlyActive(): bool
    {
        if ($this->is_active) {
            return true;
        }

        // Check if in grace period
        if ($this->grace_period_ends_at && $this->grace_period_ends_at->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Check if broker is in grace period
     */
    public function isInGracePeriod(): bool
    {
        return !$this->is_active 
            && $this->grace_period_ends_at 
            && $this->grace_period_ends_at->isFuture();
    }

    /**
     * Get total unique users using this broker
     */
    public function getTotalUsersCount(): int
    {
        return $this->usageRecords()->distinct('user_id')->count('user_id');
    }

    /**
     * Get active users (last 7 days)
     */
    public function getActiveUsersCount(): int
    {
        return $this->usageRecords()
            ->where('last_seen_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');
    }
}
