<?php

namespace App\Services;

use App\Models\TradingAccount;
use Illuminate\Support\Facades\Log;

/**
 * Platform Detection Service
 * 
 * Detects whether a trading account is MT4 or MT5,
 * and determines the account mode (netting vs hedging).
 */
class PlatformDetectionService
{
    /**
     * Detect platform type and mode from account data
     * 
     * @param array $accountData Raw account data from API
     * @return array ['platform' => 'MT4'|'MT5', 'mode' => 'netting'|'hedging', 'build' => int]
     */
    public function detectPlatform(array $accountData): array
    {
        // MT5 specific fields that MT4 doesn't have
        $mt5Indicators = [
            'margin_mode',      // MT5 has different margin calculation modes
            'trade_mode',       // MT5 has netting (0) vs hedging (1)
            'fifo_close',       // MT5 FIFO closing rule
            'account_stopout_mode', // MT5 stopout mode
        ];
        
        // Check for MT5 indicators
        $isMT5 = false;
        foreach ($mt5Indicators as $indicator) {
            if (isset($accountData[$indicator])) {
                $isMT5 = true;
                break;
            }
        }
        
        // Determine platform
        $platform = $isMT5 ? 'MT5' : 'MT4';
        
        // Determine mode
        $mode = 'hedging'; // Default for MT4
        
        if ($isMT5 && isset($accountData['trade_mode'])) {
            // MT5 trade_mode: 0 = netting, 1 = hedging
            $mode = $accountData['trade_mode'] == 0 ? 'netting' : 'hedging';
        }
        
        // Get build number if available
        $build = $accountData['build'] ?? $accountData['platform_build'] ?? null;
        
        Log::info('Platform detected', [
            'platform' => $platform,
            'mode' => $mode,
            'build' => $build,
            'account_number' => $accountData['account_number'] ?? 'unknown'
        ]);
        
        return [
            'platform' => $platform,
            'mode' => $mode,
            'build' => $build
        ];
    }
    
    /**
     * Detect platform from position/deal data structure
     * 
     * @param array $positionData Position or deal data
     * @return string 'MT4'|'MT5'
     */
    public function detectFromPositionData(array $positionData): string
    {
        // MT5 positions have position_id field
        if (isset($positionData['position_id']) || isset($positionData['position'])) {
            return 'MT5';
        }
        
        // MT5 deals have specific entry types
        if (isset($positionData['entry'])) {
            $entry = strtolower($positionData['entry']);
            if (in_array($entry, ['in', 'out', 'inout', 'out_by'])) {
                return 'MT5';
            }
        }
        
        // Default to MT4
        return 'MT4';
    }
    
    /**
     * Update trading account with detected platform info
     * 
     * @param TradingAccount $account
     * @param array $accountData
     * @return void
     */
    public function updateAccountPlatform(TradingAccount $account, array $accountData): void
    {
        $detection = $this->detectPlatform($accountData);
        
        $account->update([
            'platform_type' => $detection['platform'],
            'account_mode' => $detection['mode'],
            'platform_build' => $detection['build'],
            'platform_detected_at' => now()
        ]);
        
        Log::info('Account platform updated', [
            'account_id' => $account->id,
            'platform' => $detection['platform'],
            'mode' => $detection['mode']
        ]);
    }
    
    /**
     * Check if account is MT5 Netting mode
     * 
     * @param TradingAccount $account
     * @return bool
     */
    public function isNettingMode(TradingAccount $account): bool
    {
        return $account->platform_type === 'MT5' && $account->account_mode === 'netting';
    }
    
    /**
     * Check if account is MT5 Hedging mode
     * 
     * @param TradingAccount $account
     * @return bool
     */
    public function isHedgingMode(TradingAccount $account): bool
    {
        return $account->platform_type === 'MT4' || 
               ($account->platform_type === 'MT5' && $account->account_mode === 'hedging');
    }
    
    /**
     * Get platform display name
     * 
     * @param TradingAccount $account
     * @return string
     */
    public function getPlatformDisplayName(TradingAccount $account): string
    {
        if (!$account->platform_type) {
            return 'Unknown';
        }
        
        $name = $account->platform_type;
        
        if ($account->platform_type === 'MT5') {
            $name .= ' (' . ucfirst($account->account_mode ?? 'unknown') . ')';
        }
        
        return $name;
    }
}
