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
        'is_active',
        'is_admin',
        'last_login_at',
        'public_username',
        'public_username_set_at',
        'public_display_mode',
        'public_display_name',
        'show_on_leaderboard',
        'leaderboard_rank_by',
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
        'public_username_set_at' => 'datetime',
        'show_on_leaderboard' => 'boolean',
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

    public function whitelistedBrokerUsage()
    {
        return $this->hasMany(WhitelistedBrokerUsage::class);
    }

    public function publicProfileAccounts()
    {
        return $this->hasMany(PublicProfileAccount::class);
    }

    // Helper methods
    public static function generateApiKey()
    {
        return 'tvsr_' . Str::random(64);
    }

    // Removed: canAddAccount() - no more account limits
    // Removed: isSubscribed() - no more subscriptions

/**
 * Regenerate API key
 */
public function regenerateApiKey()
{
    $this->api_key = self::generateApiKey();
    $this->save();
    return $this->api_key;
}

}
