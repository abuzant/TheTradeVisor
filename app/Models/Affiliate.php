<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class Affiliate extends Authenticatable
{
    protected $fillable = [
        'user_id', 'username', 'email', 'password', 'slug', 'referral_url',
        'is_active', 'is_verified', 'email_verified_at',
        'usdt_wallet_address', 'wallet_type', 'payment_threshold',
        'total_clicks', 'total_signups', 'total_paid_signups',
        'total_earnings', 'pending_earnings', 'paid_earnings',
        'last_login_at'
    ];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'payment_threshold' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'paid_earnings' => 'decimal:2',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($affiliate) {
            if (empty($affiliate->slug)) {
                $affiliate->slug = self::generateUniqueSlug();
            }
            if (empty($affiliate->referral_url)) {
                $affiliate->referral_url = "https://join.thetradevisor.com/offers/{$affiliate->slug}";
            }
        });
    }
    
    public static function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(12);
        } while (self::where('slug', $slug)->exists());
        
        return $slug;
    }
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }
    
    public function conversions()
    {
        return $this->hasMany(AffiliateConversion::class);
    }
    
    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }
    
    public function analytics()
    {
        return $this->hasMany(AffiliateAnalytic::class);
    }
    
    // Helper Methods
    public function updateStatistics(): void
    {
        $this->total_clicks = $this->clicks()->count();
        $this->total_signups = $this->clicks()->where('converted', true)->count();
        $this->total_paid_signups = $this->conversions()->whereIn('status', ['approved', 'paid'])->count();
        
        $earnings = $this->conversions();
        $this->total_earnings = $earnings->whereIn('status', ['approved', 'paid'])->sum('commission_amount');
        $this->pending_earnings = $earnings->where('status', 'pending')->sum('commission_amount');
        $this->paid_earnings = $earnings->where('status', 'paid')->sum('commission_amount');
        
        $this->save();
    }
    
    public function canRequestPayout(): bool
    {
        return $this->pending_earnings >= $this->payment_threshold 
            && !empty($this->usdt_wallet_address);
    }
    
    public function getConversionRate(): float
    {
        if ($this->total_clicks == 0) {
            return 0;
        }
        return round(($this->total_paid_signups / $this->total_clicks) * 100, 2);
    }
}
