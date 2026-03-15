<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicProfileAccount extends Model
{
    protected $fillable = [
        'user_id',
        'trading_account_id',
        'account_slug',
        'is_public',
        'custom_title',
        'widget_preset',
        'visible_widgets',
        'show_recent_trades',
        'show_symbols',
        'view_count',
        'last_viewed_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'visible_widgets' => 'array',
        'show_recent_trades' => 'boolean',
        'show_symbols' => 'boolean',
        'view_count' => 'integer',
        'last_viewed_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProfileView::class);
    }

    // Helper methods
    public function getPublicUrl(): string
    {
        $username = $this->user->public_display_mode === 'anonymous' 
            ? 'anonymous' 
            : $this->user->public_username;
            
        return url("/@{$username}/{$this->account_slug}/{$this->tradingAccount->account_number}");
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }

    public function getViewCountLast180Days(): int
    {
        return $this->views()
            ->where('viewed_at', '>=', now()->subDays(180))
            ->count();
    }
}
