<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_account_id',
        'ticket',
        'symbol',
        'comment',
        'external_id',
        'type',
        'state',
        'reason',
        'volume_initial',
        'volume_current',
        'price_open',
        'price_current',
        'price_stoplimit',
        'sl',
        'tp',
        'time_setup',
        'time_setup_msc',
        'time_done',
        'time_done_msc',
        'expiration',
        'position_id',
        'position_by_id',
        'magic',
        'is_active',
    ];

    protected $casts = [
        'ticket' => 'integer',
        'volume_initial' => 'decimal:2',
        'volume_current' => 'decimal:2',
        'price_open' => 'decimal:5',
        'price_current' => 'decimal:5',
        'price_stoplimit' => 'decimal:5',
        'sl' => 'decimal:5',
        'tp' => 'decimal:5',
        'time_setup_msc' => 'integer',
        'time_done_msc' => 'integer',
        'position_id' => 'integer',
        'position_by_id' => 'integer',
        'magic' => 'integer',
        'is_active' => 'boolean',
        // 'time_setup' => 'datetime',
        // 'time_done' => 'datetime',
        // 'expiration' => 'datetime',
    ];

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function getNormalizedSymbol()
    {
        return SymbolMapping::normalize($this->symbol);
    }


/**
 * Get normalized symbol
 */
public function getNormalizedSymbolAttribute()
{
    return SymbolMapping::normalize($this->symbol);
}




public function getTimeSetupAttribute($value)
{
    if ($value instanceof Carbon) {
        return $value;
    }
    
    if (empty($value)) {
        return null;
    }
    
    try {
        if (is_string($value) && strpos($value, '.') !== false) {
            $value = str_replace('.', '-', $value);
        }
        return Carbon::parse($value);
    } catch (\Exception $e) {
        return Carbon::now();
    }
}

/**
 * Get the time_done attribute as Carbon instance
 */
public function getTimeDoneAttribute($value)
{
    if ($value instanceof Carbon) {
        return $value;
    }
    
    if (empty($value)) {
        return null;
    }
    
    try {
        if (is_string($value) && strpos($value, '.') !== false) {
            $value = str_replace('.', '-', $value);
        }
        return Carbon::parse($value);
    } catch (\Exception $e) {
        return Carbon::now();
    }
}

/**
 * Get the expiration attribute as Carbon instance
 */
public function getExpirationAttribute($value)
{
    if ($value instanceof Carbon) {
        return $value;
    }
    
    if (empty($value)) {
        return null;
    }
    
    try {
        if (is_string($value) && strpos($value, '.') !== false) {
            $value = str_replace('.', '-', $value);
        }
        return Carbon::parse($value);
    } catch (\Exception $e) {
        return Carbon::now();
    }
}


}
