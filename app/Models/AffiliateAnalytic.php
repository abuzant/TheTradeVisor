<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateAnalytic extends Model
{
    const UPDATED_AT = null;
    
    protected $fillable = [
        'affiliate_id', 'date',
        'clicks', 'unique_clicks', 'signups', 'paid_signups', 'earnings',
        'click_to_signup_rate', 'signup_to_paid_rate',
        'top_country_code', 'top_utm_source'
    ];
    
    protected $casts = [
        'date' => 'date',
        'earnings' => 'decimal:2',
        'click_to_signup_rate' => 'decimal:2',
        'signup_to_paid_rate' => 'decimal:2',
        'created_at' => 'datetime',
    ];
    
    // Relationships
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
}
