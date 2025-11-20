<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'api_key',
        'subscription_tier',
        'max_accounts',
        'is_active',
    'is_admin',
    'last_login_at',
    'display_currency',
    'affiliate_id',
    'referred_by_affiliate_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    'is_admin' => 'boolean', 
        'max_accounts' => 'integer',
    ];

/**
 * Boot the model
 */
protected static function boot()
{
    parent::boot();
    
    // Auto-generate API key when creating a new user
    static::creating(function ($user) {
        if (empty($user->api_key)) {
            $user->api_key = self::generateApiKey();
        }
        
        // Auto-set max_accounts for enterprise users
        if ($user->subscription_tier === 'enterprise' && empty($user->max_accounts)) {
            $user->max_accounts = 999999;
        }
    });
    
    // Auto-create affiliate account for new users
    static::created(function ($user) {
        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'is_verified' => $user->email_verified_at ? true : false,
        ]);
        
        $user->affiliate_id = $affiliate->id;
        $user->saveQuietly();
    });
    
    // Auto-update max_accounts when subscription tier changes to enterprise
    static::updating(function ($user) {
        if ($user->isDirty('subscription_tier') && $user->subscription_tier === 'enterprise') {
            $user->max_accounts = 999999;
        }
    });
}

    // Relationships
    public function tradingAccounts()
    {
        return $this->hasMany(TradingAccount::class);
    }

    public function activeTradingAccounts()
    {
        return $this->hasMany(TradingAccount::class)->where('is_active', true);
    }

    public function digestSubscriptions()
    {
        return $this->hasMany(DigestSubscription::class);
    }

    public function enterpriseBroker()
    {
        return $this->hasOne(EnterpriseBroker::class);
    }

    public function whitelistedBrokerUsage()
    {
        return $this->hasMany(WhitelistedBrokerUsage::class);
    }
    
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
    
    public function referredByAffiliate()
    {
        return $this->belongsTo(Affiliate::class, 'referred_by_affiliate_id');
    }

    // Helper methods
    public static function generateApiKey()
    {
        return 'tvsr_' . Str::random(64);
    }

    public function canAddAccount()
    {
        return $this->tradingAccounts()->count() < $this->max_accounts;
    }

    public function isSubscribed()
    {
        return $this->subscription_tier !== 'free';
    }

/**
 * Regenerate API key
 */
public function regenerateApiKey()
{
    $this->api_key = self::generateApiKey();
    $this->save();
    return $this->api_key;
}

/**
 * Check if user can add more accounts
 */
public function canAddMoreAccounts()
{
    return $this->tradingAccounts()->count() < $this->max_accounts;
}

/**
 * Get account limit info
 */
public function getAccountLimitInfo()
{
    $current = $this->tradingAccounts()->count();
    $max = $this->max_accounts;
    
    return [
        'current' => $current,
        'max' => $max,
        'remaining' => max(0, $max - $current),
        'can_add' => $current < $max,
    ];
}

}
