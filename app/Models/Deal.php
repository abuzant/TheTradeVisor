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
        'activity_type',
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
     * Scope: Get only closed trades (OUT deals)
     * This is the CORRECT way to get closed trades for both MT4 and MT5
     * 
     * MT5: OUT deal = position closed with final profit
     * MT4: OUT deal = order closed with final profit
     * Works for both Netting and Hedging modes
     */
    public function scopeClosedTrades($query)
    {
        return $query->where('entry', 'out')
            ->whereIn('type', ['0', '1', 'buy', 'sell']);
    }

    /**
     * Scope: Filter by account ID
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('trading_account_id', $accountId);
    }

    /**
     * Scope: Filter by multiple account IDs
     */
    public function scopeForAccounts($query, array $accountIds)
    {
        return $query->whereIn('trading_account_id', $accountIds);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $start, $end = null)
    {
        $query->where('time', '>=', $start);
        
        if ($end) {
            $query->where('time', '<=', $end);
        }
        
        return $query;
    }

    /**
     * Scope: Filter by position ID
     */
    public function scopeForPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /**
     * Scope: Get only IN deals (position opens/additions)
     */
    public function scopeOpenDeals($query)
    {
        return $query->where('entry', 'in');
    }

    /**
     * Scope: Get winning trades
     */
    public function scopeWinning($query)
    {
        return $query->where('profit', '>', 0);
    }

    /**
     * Scope: Get losing trades
     */
    public function scopeLosing($query)
    {
        return $query->where('profit', '<', 0);
    }

    /**
     * Scope: Order by most recent
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('time', 'desc');
    }

    /**
     * Get the POSITION type (not the deal action type)
     * 
     * CRITICAL: For OUT deals (closed positions), the 'type' field shows the CLOSING action,
     * not the position type. Example:
     * - BUY position: IN deal type='buy', OUT deal type='sell' (selling to close)
     * - SELL position: IN deal type='sell', OUT deal type='buy' (buying to close)
     * 
     * This accessor finds the IN deal to get the TRUE position type.
     */
    public function getPositionTypeAttribute()
    {
        // If this is an IN deal, the type IS the position type
        if ($this->entry === 'in') {
            return $this->display_type;
        }
        
        // If this is an OUT deal, we need to find the IN deal for this position
        if ($this->entry === 'out' && $this->position_id) {
            $inDeal = static::where('position_id', $this->position_id)
                ->where('entry', 'in')
                ->first();
            
            if ($inDeal) {
                return $inDeal->display_type;
            }
        }
        
        // Fallback: return the deal type (may be incorrect for OUT deals)
        return $this->display_type;
    }

    /**
     * Check if this is a BUY position (not buy action)
     */
    public function getIsPositionBuyAttribute()
    {
        return stripos($this->position_type, 'buy') !== false;
    }

    /**
     * Check if this is a SELL position (not sell action)
     */
    public function getIsPositionSellAttribute()
    {
        return stripos($this->position_type, 'sell') !== false;
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

    /**
     * Get the correct deal type (MT5 sends numeric codes that need mapping)
     * MT5 Deal Type Enum:
     * 0 = DEAL_TYPE_BUY
     * 1 = DEAL_TYPE_SELL  
     * 2 = DEAL_TYPE_BALANCE
     * 3 = DEAL_TYPE_CREDIT
     * 4 = DEAL_TYPE_CHARGE
     * 5 = DEAL_TYPE_CORRECTION
     * 6 = DEAL_TYPE_BONUS
     * 7 = DEAL_TYPE_COMMISSION
     * 8 = DEAL_TYPE_COMMISSION_DAILY
     * 9 = DEAL_TYPE_COMMISSION_MONTHLY
     * 10 = DEAL_TYPE_COMMISSION_AGENT_DAILY
     * 11 = DEAL_TYPE_COMMISSION_AGENT_MONTHLY
     * 12 = DEAL_TYPE_INTEREST
     * 13 = DEAL_TYPE_BUY_CANCELED
     * 14 = DEAL_TYPE_SELL_CANCELED
     * 15 = DEAL_TYPE_DIVIDEND
     * 16 = DEAL_TYPE_DIVIDEND_FRANKED
     * 17 = DEAL_TYPE_TAX
     */
    public function getDisplayTypeAttribute()
    {
        $type = $this->attributes['type'] ?? '';
        
        // If already a string (buy/sell), return as-is
        if (is_string($type) && (stripos($type, 'buy') !== false || stripos($type, 'sell') !== false)) {
            return strtoupper($type);
        }
        
        // Map numeric MT5 codes to readable strings
        $typeMap = [
            '0' => 'BUY',
            '1' => 'SELL',
            '2' => 'BALANCE',
            '3' => 'CREDIT',
            '4' => 'CHARGE',
            '5' => 'CORRECTION',
            '6' => 'BONUS',
            '7' => 'COMMISSION',
            '8' => 'COMMISSION_DAILY',
            '9' => 'COMMISSION_MONTHLY',
            '10' => 'COMMISSION_AGENT_DAILY',
            '11' => 'COMMISSION_AGENT_MONTHLY',
            '12' => 'INTEREST',
            '13' => 'BUY_CANCELED',
            '14' => 'SELL_CANCELED',
            '15' => 'DIVIDEND',
            '16' => 'DIVIDEND_FRANKED',
            '17' => 'TAX',
        ];
        
        return $typeMap[$type] ?? strtoupper($type);
    }

    /**
     * Check if this is a buy deal
     */
    public function getIsBuyAttribute()
    {
        $type = $this->attributes['type'] ?? '';
        
        // Check numeric code
        if ($type === '0' || $type === 0) {
            return true;
        }
        
        // Check string
        if (is_string($type) && stripos($type, 'buy') !== false && stripos($type, 'sell') === false) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if this is a sell deal
     */
    public function getIsSellAttribute()
    {
        $type = $this->attributes['type'] ?? '';
        
        // Check numeric code
        if ($type === '1' || $type === 1) {
            return true;
        }
        
        // Check string
        if (is_string($type) && stripos($type, 'sell') !== false) {
            return true;
        }
        
        return false;
    }


}
