<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'trading_account_id',
        'user_id',
        'ip_address',
        'country_code',
        'country_name',
        'endpoint',
        'method',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
