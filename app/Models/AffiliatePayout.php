<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    const CREATED_AT = 'requested_at';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'affiliate_id', 'amount', 'currency', 'usdt_amount',
        'wallet_address', 'wallet_type', 'transaction_hash', 'status',
        'conversion_ids', 'conversion_count',
        'processed_at', 'completed_at', 'failed_at', 'failure_reason',
        'admin_notes', 'processed_by'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'usdt_amount' => 'decimal:2',
        'conversion_ids' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
    
    // Relationships
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
    
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    public function conversions()
    {
        return AffiliateConversion::whereIn('id', $this->conversion_ids ?? [])->get();
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
