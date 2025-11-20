<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateConversion extends Model
{
    const CREATED_AT = 'converted_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'affiliate_id', 'click_id', 'user_id',
        'subscription_tier', 'commission_amount', 'commission_currency',
        'status', 'approved_at', 'paid_at', 'rejected_at', 'rejection_reason',
        'is_suspicious', 'fraud_score', 'fraud_notes'
    ];
    
    protected $casts = [
        'commission_amount' => 'decimal:2',
        'is_suspicious' => 'boolean',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
        'converted_at' => 'datetime',
    ];
    
    // Relationships
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
    
    public function click()
    {
        return $this->belongsTo(AffiliateClick::class, 'click_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function payout()
    {
        return $this->belongsToMany(AffiliatePayout::class, 'conversion_ids');
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }
}
