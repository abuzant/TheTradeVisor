<?php

namespace App\Services;

use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Deal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Position Aggregation Service
 * 
 * Handles aggregation of deals into positions for different platform types.
 * MT4: Simple ticket-based (one ticket = one position)
 * MT5 Netting: Position-based (multiple deals aggregate into one position)
 * MT5 Hedging: Similar to MT4 (multiple positions per symbol allowed)
 */
class PositionAggregationService
{
    protected PlatformDetectionService $platformDetection;
    
    public function __construct(PlatformDetectionService $platformDetection)
    {
        $this->platformDetection = $platformDetection;
    }
    
    /**
     * Aggregate deals into positions for a trading account
     * 
     * @param TradingAccount $account
     * @param array $deals Array of deal data from API
     * @return Collection
     */
    public function aggregateDeals(TradingAccount $account, array $deals): Collection
    {
        if ($this->platformDetection->isNettingMode($account)) {
            return $this->aggregateNettingDeals($account, $deals);
        }
        
        return $this->aggregateHedgingDeals($account, $deals);
    }
    
    /**
     * Aggregate deals for MT5 Netting mode
     * Multiple deals with same position_id aggregate into one position
     * 
     * @param TradingAccount $account
     * @param array $deals
     * @return Collection
     */
    protected function aggregateNettingDeals(TradingAccount $account, array $deals): Collection
    {
        $positions = collect();
        $groupedDeals = collect($deals)->groupBy('position_id');
        
        foreach ($groupedDeals as $positionId => $positionDeals) {
            // Skip if no position_id (balance operations, etc.)
            if (empty($positionId)) {
                continue;
            }
            
            $position = $this->createOrUpdateNettingPosition($account, $positionId, $positionDeals);
            if ($position) {
                $positions->push($position);
            }
        }
        
        return $positions;
    }
    
    /**
     * Create or update a netting position from deals
     * 
     * @param TradingAccount $account
     * @param string $positionId
     * @param Collection $deals
     * @return Position|null
     */
    protected function createOrUpdateNettingPosition(TradingAccount $account, string $positionId, Collection $deals): ?Position
    {
        // Sort deals by time
        $sortedDeals = $deals->sortBy('time');
        
        // Get first deal (entry)
        $firstDeal = $sortedDeals->first();
        if (!$firstDeal) {
            return null;
        }
        
        // Calculate aggregated values
        $totalVolumeIn = 0;
        $totalVolumeOut = 0;
        $weightedPriceSum = 0;
        $totalProfit = 0;
        $totalSwap = 0;
        $totalCommission = 0;
        $currentVolume = 0;
        $lastDeal = $firstDeal;
        
        foreach ($sortedDeals as $deal) {
            $volume = (float) $deal['volume'];
            $price = (float) $deal['price'];
            $entry = strtolower($deal['entry'] ?? 'unknown');
            
            if ($entry === 'in') {
                $totalVolumeIn += $volume;
                $weightedPriceSum += $volume * $price;
                $currentVolume += $volume;
            } elseif ($entry === 'out' || $entry === 'out_by') {
                $totalVolumeOut += $volume;
                $currentVolume -= $volume;
            }
            
            $totalProfit += (float) ($deal['profit'] ?? 0);
            $totalSwap += (float) ($deal['swap'] ?? 0);
            $totalCommission += (float) ($deal['commission'] ?? 0);
            $lastDeal = $deal;
        }
        
        // Calculate weighted average entry price
        $avgEntryPrice = $totalVolumeIn > 0 ? $weightedPriceSum / $totalVolumeIn : 0;
        
        // Determine if position is still open
        $isOpen = $currentVolume > 0.001; // Account for floating point precision
        
        // Find or create position
        $position = Position::updateOrCreate(
            [
                'trading_account_id' => $account->id,
                'position_identifier' => $positionId,
            ],
            [
                'ticket' => $firstDeal['ticket'] ?? 0,
                'symbol' => $firstDeal['symbol'] ?? '',
                'type' => $this->determinePositionType($sortedDeals),
                'volume' => abs($currentVolume),
                'open_price' => $avgEntryPrice,
                'current_price' => (float) ($lastDeal['price'] ?? 0),
                'profit' => $totalProfit,
                'swap' => $totalSwap,
                'commission' => $totalCommission,
                'open_time' => $firstDeal['time'] ?? now(),
                'update_time' => $lastDeal['time'] ?? now(),
                'close_time' => $isOpen ? null : ($lastDeal['time'] ?? null),
                'close_price' => $isOpen ? null : ((float) ($lastDeal['price'] ?? 0)),
                'total_volume_in' => $totalVolumeIn,
                'total_volume_out' => $totalVolumeOut,
                'deal_count' => $sortedDeals->count(),
                'is_open' => $isOpen,
                'platform_type' => 'MT5',
                'comment' => $firstDeal['comment'] ?? null,
                'magic' => $firstDeal['magic'] ?? 0,
            ]
        );
        
        Log::debug('Netting position aggregated', [
            'position_id' => $positionId,
            'deals_count' => $sortedDeals->count(),
            'volume_in' => $totalVolumeIn,
            'volume_out' => $totalVolumeOut,
            'current_volume' => $currentVolume,
            'is_open' => $isOpen
        ]);
        
        return $position;
    }
    
    /**
     * Aggregate deals for MT4 or MT5 Hedging mode
     * Each position is independent
     * 
     * @param TradingAccount $account
     * @param array $deals
     * @return Collection
     */
    protected function aggregateHedgingDeals(TradingAccount $account, array $deals): Collection
    {
        $positions = collect();
        
        foreach ($deals as $deal) {
            // Skip balance operations
            if (empty($deal['symbol'])) {
                continue;
            }
            
            $position = $this->createOrUpdateHedgingPosition($account, $deal);
            if ($position) {
                $positions->push($position);
            }
        }
        
        return $positions;
    }
    
    /**
     * Create or update a hedging position from a deal
     * 
     * @param TradingAccount $account
     * @param array $deal
     * @return Position|null
     */
    protected function createOrUpdateHedgingPosition(TradingAccount $account, array $deal): ?Position
    {
        $entry = strtolower($deal['entry'] ?? 'unknown');
        
        // For MT4/MT5 Hedging, each ticket is a separate position
        if ($entry === 'in') {
            // Opening a new position
            return Position::updateOrCreate(
                [
                    'trading_account_id' => $account->id,
                    'ticket' => $deal['ticket'],
                ],
                [
                    'symbol' => $deal['symbol'],
                    'type' => strtolower($deal['type']),
                    'volume' => (float) $deal['volume'],
                    'open_price' => (float) $deal['price'],
                    'current_price' => (float) $deal['price'],
                    'profit' => (float) ($deal['profit'] ?? 0),
                    'swap' => (float) ($deal['swap'] ?? 0),
                    'commission' => (float) ($deal['commission'] ?? 0),
                    'open_time' => $deal['time'] ?? now(),
                    'update_time' => $deal['time'] ?? now(),
                    'total_volume_in' => (float) $deal['volume'],
                    'total_volume_out' => 0,
                    'deal_count' => 1,
                    'is_open' => true,
                    'platform_type' => $account->platform_type ?? 'MT4',
                    'comment' => $deal['comment'] ?? null,
                    'magic' => $deal['magic'] ?? 0,
                    'sl' => $deal['sl'] ?? null,
                    'tp' => $deal['tp'] ?? null,
                ]
            );
        } elseif ($entry === 'out' || $entry === 'out_by') {
            // Closing an existing position
            $position = Position::where('trading_account_id', $account->id)
                ->where('ticket', $deal['ticket'])
                ->where('is_open', true)
                ->first();
            
            if ($position) {
                $position->update([
                    'current_price' => (float) $deal['price'],
                    'profit' => (float) ($deal['profit'] ?? 0),
                    'swap' => (float) ($deal['swap'] ?? 0),
                    'commission' => (float) ($deal['commission'] ?? 0),
                    'update_time' => $deal['time'] ?? now(),
                    'close_time' => $deal['time'] ?? now(),
                    'close_price' => (float) $deal['price'],
                    'total_volume_out' => (float) $deal['volume'],
                    'deal_count' => DB::raw('deal_count + 1'),
                    'is_open' => false,
                ]);
                
                return $position;
            }
        }
        
        return null;
    }
    
    /**
     * Determine position type from deals
     * 
     * @param Collection $deals
     * @return string 'buy' or 'sell'
     */
    protected function determinePositionType(Collection $deals): string
    {
        $firstDeal = $deals->first();
        $type = strtolower($firstDeal['type'] ?? 'buy');
        
        // Normalize type
        if (in_array($type, ['buy', 'buy_limit', 'buy_stop'])) {
            return 'buy';
        }
        
        return 'sell';
    }
    
    /**
     * Get positions with their associated deals
     * 
     * @param TradingAccount $account
     * @param bool $openOnly
     * @return Collection
     */
    public function getPositionsWithDeals(TradingAccount $account, bool $openOnly = false): Collection
    {
        $query = Position::where('trading_account_id', $account->id)
            ->with(['tradingAccount']);
        
        if ($openOnly) {
            $query->where('is_open', true);
        }
        
        $positions = $query->orderBy('open_time', 'desc')->get();
        
        // Attach deals to each position
        foreach ($positions as $position) {
            if ($this->platformDetection->isNettingMode($account) && $position->position_identifier) {
                // For netting, get all deals with same position_identifier
                $position->deals = Deal::where('trading_account_id', $account->id)
                    ->where('position_id', $position->position_identifier)
                    ->orderBy('time', 'asc')
                    ->get();
            } else {
                // For hedging, get deals with same ticket
                $position->deals = Deal::where('trading_account_id', $account->id)
                    ->where('ticket', $position->ticket)
                    ->orderBy('time', 'asc')
                    ->get();
            }
        }
        
        return $positions;
    }
}
