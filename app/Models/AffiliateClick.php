<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    const CREATED_AT = 'clicked_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'affiliate_id', 'ip_address', 'user_agent', 'referrer', 'landing_page',
        'country_code', 'city',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'session_id', 'fingerprint',
        'converted', 'converted_at', 'conversion_user_id'
    ];
    
    protected $casts = [
        'converted' => 'boolean',
        'converted_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];
    
    // Relationships
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
    
    public function conversionUser()
    {
        return $this->belongsTo(User::class, 'conversion_user_id');
    }
    
    public function conversion()
    {
        return $this->hasOne(AffiliateConversion::class, 'click_id');
    }
}
