<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhitelistedBrokerUsage extends Model
{
    protected $table = 'whitelisted_broker_usage';
    
    protected $fillable = [
        'user_id',
        'trading_account_id',
        'enterprise_broker_id',
        'account_number',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user who owns this usage record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trading account
     */
    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    /**
     * Get the enterprise broker
     */
    public function enterpriseBroker(): BelongsTo
    {
        return $this->belongsTo(EnterpriseBroker::class);
    }
}
