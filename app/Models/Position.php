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
        'position_identifier',
        'entry_type',
        'close_time',
        'close_price',
        'total_volume_in',
        'total_volume_out',
        'deal_count',
        'platform_type',
    ];

    protected $casts = [
        'ticket' => 'integer',
        'volume' => 'decimal:2',
        'open_price' => 'decimal:5',
        'current_price' => 'decimal:5',
        'close_price' => 'decimal:5',
        'sl' => 'decimal:5',
        'tp' => 'decimal:5',
        'profit' => 'decimal:2',
        'swap' => 'decimal:2',
        'commission' => 'decimal:2',
        'total_volume_in' => 'decimal:2',
        'total_volume_out' => 'decimal:2',
        'magic' => 'integer',
        'identifier' => 'integer',
        'deal_count' => 'integer',
        'is_open' => 'boolean',
        'open_time' => 'datetime',
        'update_time' => 'datetime',
        'close_time' => 'datetime',
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

    /**
     * Get deals associated with this position
     */
    public function deals()
    {
        // For MT5 netting, match by position_identifier
        if ($this->platform_type === 'MT5' && $this->position_identifier) {
            return $this->hasMany(Deal::class, 'position_id', 'position_identifier')
                ->where('trading_account_id', $this->trading_account_id);
        }
        
        // For MT4/MT5 hedging, match by ticket
        return $this->hasMany(Deal::class, 'ticket', 'ticket')
            ->where('trading_account_id', $this->trading_account_id);
    }

    /**
     * Check if position is MT5 Netting
     */
    public function isNettingPosition(): bool
    {
        return $this->platform_type === 'MT5' && !empty($this->position_identifier);
    }

    /**
     * Get platform display badge
     */
    public function getPlatformBadgeAttribute(): string
    {
        if (!$this->platform_type) {
            return '';
        }
        
        $badge = $this->platform_type;
        
        if ($this->platform_type === 'MT5' && $this->isNettingPosition()) {
            $badge .= ' Netting';
        }
        
        return $badge;
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
