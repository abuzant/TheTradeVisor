<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SymbolMapping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'raw_symbol',
        'normalized_symbol',
        'broker_suffix',
        'is_verified',
    ];
    
    protected $casts = [
        'is_verified' => 'boolean',
    ];
    

    /**
     * Auto-normalize symbol by removing common broker suffixes
     */
    protected static function autoNormalize($symbol)
    {
        // Remove common suffixes: .a, .lv, .sd, .pro, .raw, -micro, etc.
        $normalized = preg_replace('/\.(a|lv|sd|pro|raw|ecn|m|i|c|f)$/i', '', $symbol);
        $normalized = preg_replace('/(-micro|-mini|-standard)$/i', '', $normalized);
        
        // Convert to uppercase
        return strtoupper($normalized);
    }
    
    /**
     * Extract broker suffix from symbol
     */
    protected static function extractSuffix($symbol)
    {
        // Match common suffixes
        if (preg_match('/(\.[a-z]+|-[a-z]+)$/i', $symbol, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    

/**
 * Automatically normalize and store a symbol
 */
public static function normalizeAndStore($rawSymbol)
{
    // Validate symbol
    if (empty($rawSymbol) || !is_string($rawSymbol)) {
        Log::warning('Invalid symbol provided to normalizeAndStore', [
            'raw_symbol' => $rawSymbol,
            'type' => gettype($rawSymbol)
        ]);
        return 'UNKNOWN';
    }
    
    // Trim whitespace
    $rawSymbol = trim($rawSymbol);
    
    if (strlen($rawSymbol) === 0) {
        return 'UNKNOWN';
    }
    
    // Check if already exists
    $existing = self::where('raw_symbol', $rawSymbol)->first();
    
    if ($existing) {
        return $existing->normalized_symbol;
    }
    
    // Auto-normalize the symbol
    $normalized = self::autoNormalize($rawSymbol);
    $suffix = self::extractSuffix($rawSymbol);
    
    // Create new mapping (unverified)
    self::create([
        'raw_symbol' => $rawSymbol,
        'normalized_symbol' => $normalized,
        'broker_suffix' => $suffix,
        'is_verified' => false,
    ]);
    
    return $normalized;
}

/**
 * Get normalized symbol (lookup or create)
 */
public static function normalize($rawSymbol)
{
    // Validate symbol
    if (empty($rawSymbol) || !is_string($rawSymbol)) {
        return 'UNKNOWN';
    }
    
    $rawSymbol = trim($rawSymbol);
    
    if (strlen($rawSymbol) === 0) {
        return 'UNKNOWN';
    }
    
    $mapping = self::where('raw_symbol', $rawSymbol)->first();
    
    if ($mapping) {
        return $mapping->normalized_symbol;
    }
    
    // Auto-create if doesn't exist
    return self::normalizeAndStore($rawSymbol);
}

}
