<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_account_id',
        'ticket',
        'symbol',
        'comment',
        'external_id',
        'type',
        'reason',
        'volume',
        'open_price',
        'current_price',
        'sl',
        'tp',
        'profit',
        'swap',
        'commission',
        'open_time',
        'update_time',
        'magic',
        'identifier',
        'is_open',
    ];

    protected $casts = [
        'ticket' => 'integer',
        'volume' => 'decimal:2',
        'open_price' => 'decimal:5',
        'current_price' => 'decimal:5',
        'sl' => 'decimal:5',
        'tp' => 'decimal:5',
        'profit' => 'decimal:2',
        'swap' => 'decimal:2',
        'commission' => 'decimal:2',
        'magic' => 'integer',
        'identifier' => 'integer',
        'is_open' => 'boolean',
        'open_time' => 'datetime',
        'update_time' => 'datetime',
    ];

    protected $appends = ['normalized_symbol', 'open_time_human'];


    /**
     * Get normalized symbol
     */
    public function getNormalizedSymbolAttribute()
    {
        return SymbolMapping::normalize($this->symbol);
    }

    /**
     * Get human-readable open time
     */
    public function getOpenTimeHumanAttribute()
    {
        $openTime = $this->getOpenTimeAttribute($this->attributes['open_time'] ?? null);
        return $openTime ? $openTime->diffForHumans() : 'N/A';
    }

    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function getNormalizedSymbol()
    {
        return SymbolMapping::normalize($this->symbol);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($position) {
            if (empty($position->open_time)) {
                $position->open_time = now();
           }
        });
    }

/**
 * Get the open_time attribute as Carbon instance
 */
public function getOpenTimeAttribute($value)
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
 * Get the update_time attribute as Carbon instance
 */
public function getUpdateTimeAttribute($value)
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
