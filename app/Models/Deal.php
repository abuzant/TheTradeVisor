<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_account_id',
        'ticket',
        'order_id',
        'position_id',
        'symbol',
        'comment',
        'external_id',
        'type',
        'entry',
        'reason',
        'volume',
        'price',
        'profit',
        'swap',
        'commission',
        'fee',
        'time',
        'time_msc',
        'magic',
        'platform_type',
        'deal_category',
    ];

    protected $casts = [
        'ticket' => 'integer',
        'order_id' => 'integer',
        'position_id' => 'integer',
        'volume' => 'decimal:2',
        'price' => 'decimal:5',
        'profit' => 'decimal:2',
        'swap' => 'decimal:2',
        'commission' => 'decimal:2',
        'fee' => 'decimal:2',
        'time_msc' => 'integer',
        'magic' => 'integer',
        'time' => 'datetime',
    ];



    /**
     * Get the normalized symbol (accessor)
     */
    public function getNormalizedSymbolAttribute()
    {
        // Try to find the mapping
        $mapping = \App\Models\SymbolMapping::where('raw_symbol', $this->symbol)->first();
        
        if ($mapping) {
            return $mapping->normalized_symbol;
        }
        
        // If no mapping, return the raw symbol
        return $this->symbol;
    }

    //  Time Fomatting with Carbn
    public function getTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }
        
        if (empty($value)) {
            return null;
        }
        
        try {
            // Handle MT5 format (dots)
            if (is_string($value) && strpos($value, '.') !== false) {
                $value = str_replace('.', '-', $value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }


    public function tradingAccount()
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function getNormalizedSymbol()
    {
        return SymbolMapping::normalize($this->symbol);
    }

    /**
     * Scope a query to only include actual trades (exclude balance operations)
     */
    public function scopeTradesOnly($query)
    {
        return $query->where(function($q) {
                     $q->whereNull('deal_category')
                       ->orWhere('deal_category', 'trade');
                 })
                 ->whereNotNull('symbol')
                 ->where('symbol', '!=', '');
    }

    /**
     * Scope a query to only include balance operations
     */
    public function scopeBalanceOnly($query)
    {
        return $query->where('deal_category', 'balance');
    }

    /**
     * Get the appropriate number of decimal places for price display
     * based on the symbol type
     */
    public function getPriceDecimalsAttribute()
    {
        $symbol = strtoupper($this->normalized_symbol ?? $this->symbol ?? '');
        
        // Crypto currencies - 2 decimals (high value)
        if (str_contains($symbol, 'BTC') || str_contains($symbol, 'ETH')) {
            return 2;
        }
        
        // Gold/Silver/Metals - 2 decimals
        if (str_contains($symbol, 'XAU') || str_contains($symbol, 'XAG') || 
            str_contains($symbol, 'GOLD') || str_contains($symbol, 'SILVER')) {
            return 2;
        }
        
        // Indices - 2 decimals
        if (str_contains($symbol, 'US30') || str_contains($symbol, 'NAS100') || 
            str_contains($symbol, 'SPX') || str_contains($symbol, 'DAX') ||
            str_contains($symbol, 'FTSE')) {
            return 2;
        }
        
        // JPY pairs - 3 decimals (e.g., 110.123)
        if (str_contains($symbol, 'JPY')) {
            return 3;
        }
        
        // Exotic pairs and stocks - check if price is large
        if ($this->price > 100) {
            return 2; // Large numbers don't need 5 decimals
        }
        
        // Standard forex pairs - 5 decimals (e.g., 1.31683)
        return 5;
    }

    /**
     * Get formatted price with appropriate decimals
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, $this->price_decimals);
    }


}
