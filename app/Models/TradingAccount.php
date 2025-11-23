<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TradingAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_uuid',
        'account_number',
        'account_hash',
        'broker_name',
        'broker_server',
        'account_type',
        'account_name',
        'account_currency',
        'leverage',
        'balance',
        'equity',
        'margin',
        'free_margin',
        'margin_level',
        'profit',
        'credit',
        'trade_allowed',
        'trade_expert',
        'last_seen_ip',
        'detected_country',
        'detected_city',
        'detected_timezone',
        'last_sync_at',
        'is_active',
        'is_paused',        
        'paused_at',        
        'paused_by',        
        'pause_reason',
        'country_code',
        'country_name',
        'last_ip',
        'last_seen_at',
        'platform_type',
        'account_mode',
        'platform_build',
        'platform_detected_at',
    ];

    protected $casts = [
        'leverage' => 'integer',
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'margin' => 'decimal:2',
        'free_margin' => 'decimal:2',
        'margin_level' => 'decimal:2',
        'profit' => 'decimal:2',
        'credit' => 'decimal:2',
        'trade_allowed' => 'boolean',
        'trade_expert' => 'boolean',
        'is_active' => 'boolean',
        'is_paused' => 'boolean',
        'platform_build' => 'integer',
        'last_sync_at' => 'datetime',
        'paused_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'platform_detected_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function openPositions()
    {
        return $this->hasMany(Position::class)->where('is_open', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrders()
    {
        return $this->hasMany(Order::class)->where('is_active', true);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function whitelistedBrokerUsage()
    {
        return $this->hasOne(WhitelistedBrokerUsage::class);
    }

    public function publicProfileAccount()
    {
        return $this->hasOne(PublicProfileAccount::class);
    }

    public function verificationBadges()
    {
        return $this->hasMany(ProfileVerificationBadge::class);
    }

    // Helper methods
    public static function generateUuid()
    {
        return Str::uuid()->toString();
    }

    public function getUniqueIdentifier()
    {
        return $this->account_number 
            ? hash('sha256', $this->account_number . '::' . $this->broker_server)
            : $this->account_hash;
    }



/**
 * Get balance converted to target currency
 */
public function getBalanceInCurrency(string $targetCurrency): float
{
    if ($this->account_currency === $targetCurrency) {
        return (float) $this->balance;
    }

    $currencyService = app(\App\Services\CurrencyService::class);
    return $currencyService->convert(
        (float) $this->balance,
        $this->account_currency,
        $targetCurrency
    );
}

/**
 * Get equity converted to target currency
 */
public function getEquityInCurrency(string $targetCurrency): float
{
    if ($this->account_currency === $targetCurrency) {
        return (float) $this->equity;
    }

    $currencyService = app(\App\Services\CurrencyService::class);
    return $currencyService->convert(
        (float) $this->equity,
        $this->account_currency,
        $targetCurrency
    );
}

/**
 * Get profit converted to target currency
 */
public function getProfitInCurrency(string $targetCurrency): float
{
    if ($this->account_currency === $targetCurrency) {
        return (float) $this->profit;
    }

    $currencyService = app(\App\Services\CurrencyService::class);
    return $currencyService->convert(
        (float) $this->profit,
        $this->account_currency,
        $targetCurrency
    );
}


public function pause(User $user, string $reason = null): void
{
    $this->update([
        'is_paused' => true,
        'paused_at' => now(),
        'paused_by' => $user->id,
        'pause_reason' => $reason,
    ]);
}

public function unpause(): void
{
    $this->update([
        'is_paused' => false,
        'paused_at' => null,
        'paused_by' => null,
        'pause_reason' => null,
    ]);
}

public function pausedBy()
{
    return $this->belongsTo(User::class, 'paused_by');
}

// Add scope
public function scopeActive($query)
{
    return $query->where('is_active', true)->where('is_paused', false);
}



/**
 * Get the history upload progress for this account
 */
public function historyUploadProgress(): HasOne
{
    return $this->hasOne(HistoryUploadProgress::class);
}

/**
 * Get maximum days of data this account can view
 * Enterprise whitelisted accounts: 180 days
 * Standard accounts: 7 days
 */
public function getMaxDaysView(): int
{
    $usage = $this->whitelistedBrokerUsage;
    
    if ($usage && $usage->enterpriseBroker && $usage->enterpriseBroker->isCurrentlyActive()) {
        return 180; // Enterprise: Full access
    }
    
    return 7; // Standard: Limited to 7 days
}

/**
 * Check if this account is whitelisted by an enterprise broker
 */
public function isEnterpriseWhitelisted(): bool
{
    return $this->getMaxDaysView() === 180;
}

/**
 * Get the enterprise broker for this account (if whitelisted)
 */
public function getEnterpriseBroker()
{
    $usage = $this->whitelistedBrokerUsage;
    return $usage ? $usage->enterpriseBroker : null;
}

/**
 * Check if account is active (last seen within days)
 */
public function isActive(int $days = 30): bool
{
    $usage = $this->whitelistedBrokerUsage;
    
    if (!$usage || !$usage->last_seen_at) {
        // Fallback to updated_at
        return $this->updated_at && $this->updated_at->isAfter(now()->subDays($days));
    }
    
    return $usage->last_seen_at->isAfter(now()->subDays($days));
}

/**
 * Check if account is dormant (no activity in X days)
 */
public function isDormant(int $days = 30): bool
{
    return !$this->isActive($days);
}




}
