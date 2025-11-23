<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileVerificationBadge extends Model
{
    protected $fillable = [
        'trading_account_id',
        'badge_type',
        'badge_name',
        'badge_icon',
        'badge_color',
        'badge_tier',
        'is_favorite',
        'earned_at',
    ];

    protected $casts = [
        'badge_tier' => 'integer',
        'is_favorite' => 'boolean',
        'earned_at' => 'datetime',
    ];

    // Relationships
    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    // Scopes
    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeHighestTier($query, $limit = 3)
    {
        return $query->orderBy('badge_tier', 'desc')->limit($limit);
    }

    public function scopeMostRecent($query, $limit = 2)
    {
        return $query->orderBy('earned_at', 'desc')->limit($limit);
    }
}
