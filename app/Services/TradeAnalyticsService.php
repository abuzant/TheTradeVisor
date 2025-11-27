<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\TradingAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * TradeAnalyticsService
 * 
 * Centralized service for querying trading data correctly across MT4 and MT5 platforms.
 * 
 * CRITICAL UNDERSTANDING:
 * - MT4 (Order-based): Each order is independent
 * - MT5 (Position-based): Orders create deals that modify positions
 * - For BOTH platforms, use Deal model with entry='out' for closed trades
 * 
 * WHY DEALS NOT POSITIONS:
 * - Positions table = Current/recent state only (~43 records)
 * - Deals table = Complete transaction history (313+ records)
 * - Each closed position/order creates an OUT deal with final P/L
 * 
 * POSITION_IDENTIFIER is the Master Key:
 * - Unique permanent ID assigned to each position
 * - NEVER changes during position lifetime
 * - All related deals have DEAL_POSITION_ID = this identifier
 * - Use this to reconstruct complete position history
 */
class TradeAnalyticsService
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get all closed trades for account(s)
     * 
     * @param int|array $accountIds Account ID or array of IDs
     * @param Carbon|null $startDate Start date filter
     * @param Carbon|null $endDate End date filter
     * @param int|null $limit Maximum number of records
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getClosedTradesQuery($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, ?int $limit = null)
    {
        $accountIds = is_array($accountIds) ? $accountIds : [$accountIds];

        $query = Deal::whereIn('trading_account_id', $accountIds)
            ->where('entry', 'out')
            ->whereIn('type', ['0', '1', 'buy', 'sell']) // Only actual trades, not balance operations
            ->with('tradingAccount');

        if ($startDate) {
            $query->where('time', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('time', '<=', $endDate);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->orderBy('time', 'desc');
    }

    /**
     * Get closed trades collection
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param int|null $limit
     * @return Collection
     */
    public function getClosedTrades($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, ?int $limit = 1000): Collection
    {
        return $this->getClosedTradesQuery($accountIds, $startDate, $endDate, $limit)->get();
    }

    /**
     * Get total number of closed trades
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return int
     */
    public function getTotalTrades($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        return $this->getClosedTradesQuery($accountIds, $startDate, $endDate)->count();
    }

    /**
     * Calculate win rate
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return float Win rate percentage (0-100)
     */
    public function calculateWinRate($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $deals = $this->getClosedTrades($accountIds, $startDate, $endDate);
        
        if ($deals->isEmpty()) {
            return 0.0;
        }

        $total = $deals->count();
        $wins = $deals->where('profit', '>', 0)->count();
        
        return round(($wins / $total) * 100, 2);
    }

    /**
     * Calculate total profit
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param string $targetCurrency Currency to convert to (default: USD)
     * @return float Total profit in target currency
     */
    public function calculateTotalProfit($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, string $targetCurrency = 'USD'): float
    {
        $deals = $this->getClosedTrades($accountIds, $startDate, $endDate);
        
        if ($deals->isEmpty()) {
            return 0.0;
        }

        $totalProfit = 0;

        foreach ($deals as $deal) {
            $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
            $convertedProfit = $this->currencyService->safeConvert(
                $deal->profit,
                $accountCurrency,
                $targetCurrency
            );

            if ($convertedProfit === null) {
                continue;
            }

            $totalProfit += $convertedProfit;
        }

        return round($totalProfit, 2);
    }

    /**
     * Get complete position history (all deals for a position)
     * 
     * @param string|int $positionId Position identifier
     * @param int|null $accountId Optional account ID for additional filtering
     * @return Collection
     */
    public function getPositionHistory($positionId, ?int $accountId = null): Collection
    {
        $query = Deal::where('position_id', $positionId)
            ->orderBy('time', 'asc');

        if ($accountId) {
            $query->where('trading_account_id', $accountId);
        }

        return $query->get();
    }

    /**
     * Calculate hold time for a position
     * 
     * @param string|int $positionId Position identifier
     * @param int|null $accountId Optional account ID
     * @return int Hold time in seconds (0 if cannot calculate)
     */
    public function calculateHoldTime($positionId, ?int $accountId = null): int
    {
        $deals = $this->getPositionHistory($positionId, $accountId);

        if ($deals->isEmpty()) {
            return 0;
        }

        $inDeal = $deals->where('entry', 'in')->first();
        $outDeal = $deals->where('entry', 'out')->sortByDesc('time')->first();

        if (!$inDeal || !$outDeal) {
            return 0;
        }

        return $outDeal->time->diffInSeconds($inDeal->time);
    }

    /**
     * Calculate average hold time for multiple positions
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return int Average hold time in seconds
     */
    public function calculateAverageHoldTime($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        $accountIds = is_array($accountIds) ? $accountIds : [$accountIds];

        // Get all deals with IN and OUT entries
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->whereIn('entry', ['in', 'out'])
            ->when($startDate, fn($q) => $q->where('time', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('time', '<=', $endDate))
            ->orderBy('time', 'asc')
            ->get(['position_id', 'entry', 'time']);

        if ($deals->isEmpty()) {
            return 0;
        }

        // Group by position_id to find matching IN/OUT pairs
        $grouped = $deals->groupBy('position_id');
        $holdTimes = [];

        foreach ($grouped as $positionDeals) {
            $inDeal = $positionDeals->where('entry', 'in')->first();
            $outDeal = $positionDeals->where('entry', 'out')->sortByDesc('time')->first();
            
            // Only calculate if we have both IN and OUT (closed position)
            if ($inDeal && $outDeal) {
                $holdTimes[] = $outDeal->time->diffInSeconds($inDeal->time);
            }
        }

        if (empty($holdTimes)) {
            return 0;
        }

        return (int) round(array_sum($holdTimes) / count($holdTimes));
    }

    /**
     * Get symbol performance statistics
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param string $targetCurrency
     * @param int $limit
     * @return Collection
     */
    public function getSymbolPerformance($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, string $targetCurrency = 'USD', int $limit = 10): Collection
    {
        $deals = $this->getClosedTrades($accountIds, $startDate, $endDate);

        if ($deals->isEmpty()) {
            return collect([]);
        }

        // Group by normalized symbol
        $symbolGroups = $deals->groupBy(function($deal) {
            return $deal->normalized_symbol ?? $deal->symbol;
        });

        $symbols = $symbolGroups->map(function($symbolDeals, $symbol) use ($targetCurrency) {
            // Convert all profits to target currency
            $totalProfit = 0;
            $winningTrades = 0;

            foreach ($symbolDeals as $deal) {
                $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
                $convertedProfit = $this->currencyService->safeConvert(
                    $deal->profit,
                    $accountCurrency,
                    $targetCurrency
                );

                if ($convertedProfit === null) {
                    continue;
                }

                $totalProfit += $convertedProfit;

                if ($convertedProfit > 0) {
                    $winningTrades++;
                }
            }

            $totalTrades = $symbolDeals->count();
            $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

            return [
                'symbol' => $symbol,
                'total_trades' => $totalTrades,
                'total_profit' => round($totalProfit, 2),
                'avg_profit' => round($totalProfit / $totalTrades, 2),
                'win_rate' => $winRate,
                'total_volume' => round($symbolDeals->sum('volume'), 2),
            ];
        })
        ->sortByDesc('total_profit')
        ->take($limit)
        ->values();

        return $symbols;
    }

    /**
     * Get trading statistics summary
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param string $targetCurrency
     * @return array
     */
    public function getTradingStats($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, string $targetCurrency = 'USD'): array
    {
        $deals = $this->getClosedTrades($accountIds, $startDate, $endDate);

        if ($deals->isEmpty()) {
            return [
                'total_trades' => 0,
                'winning_trades' => 0,
                'losing_trades' => 0,
                'win_rate' => 0,
                'total_profit' => 0,
                'total_loss' => 0,
                'net_profit' => 0,
                'avg_profit' => 0,
                'avg_win' => 0,
                'avg_loss' => 0,
                'profit_factor' => 0,
                'total_volume' => 0,
            ];
        }

        // Convert all profits to target currency
        $convertedProfits = [];
        $totalVolume = 0;

        foreach ($deals as $deal) {
            $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
            $convertedProfit = $this->currencyService->safeConvert(
                $deal->profit,
                $accountCurrency,
                $targetCurrency
            );

            if ($convertedProfit === null) {
                continue;
            }

            $convertedProfits[] = $convertedProfit;
            $totalVolume += $deal->volume;
        }

        $totalTrades = count($convertedProfits);
        $winningProfits = array_filter($convertedProfits, fn($p) => $p > 0);
        $losingProfits = array_filter($convertedProfits, fn($p) => $p < 0);

        $winningTrades = count($winningProfits);
        $losingTrades = count($losingProfits);
        
        $totalProfit = array_sum($winningProfits);
        $totalLoss = abs(array_sum($losingProfits));
        $netProfit = array_sum($convertedProfits);

        $avgWin = $winningTrades > 0 ? $totalProfit / $winningTrades : 0;
        $avgLoss = $losingTrades > 0 ? $totalLoss / $losingTrades : 0;
        $profitFactor = $totalLoss > 0 ? $totalProfit / $totalLoss : ($totalProfit > 0 ? 999 : 0);

        return [
            'total_trades' => $totalTrades,
            'winning_trades' => $winningTrades,
            'losing_trades' => $losingTrades,
            'win_rate' => round(($winningTrades / $totalTrades) * 100, 2),
            'total_profit' => round($totalProfit, 2),
            'total_loss' => round($totalLoss, 2),
            'net_profit' => round($netProfit, 2),
            'avg_profit' => round($netProfit / $totalTrades, 2),
            'avg_win' => round($avgWin, 2),
            'avg_loss' => round($avgLoss, 2),
            'profit_factor' => round($profitFactor, 2),
            'total_volume' => round($totalVolume, 2),
        ];
    }

    /**
     * Get recent closed trades
     * 
     * @param int|array $accountIds
     * @param int $limit
     * @return Collection
     */
    public function getRecentClosedTrades($accountIds, int $limit = 20): Collection
    {
        return $this->getClosedTradesQuery($accountIds, null, null, $limit)
            ->with('tradingAccount')
            ->get();
    }

    /**
     * Get best and worst trades
     * 
     * @param int|array $accountIds
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param string $targetCurrency
     * @return array
     */
    public function getBestAndWorstTrades($accountIds, ?Carbon $startDate = null, ?Carbon $endDate = null, string $targetCurrency = 'USD'): array
    {
        $deals = $this->getClosedTrades($accountIds, $startDate, $endDate);

        if ($deals->isEmpty()) {
            return [
                'best_trade' => null,
                'worst_trade' => null,
            ];
        }

        // Convert profits and find extremes
        $bestTrade = null;
        $worstTrade = null;
        $maxProfit = PHP_FLOAT_MIN;
        $minProfit = PHP_FLOAT_MAX;

        foreach ($deals as $deal) {
            $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
            $convertedProfit = $this->currencyService->safeConvert(
                $deal->profit,
                $accountCurrency,
                $targetCurrency
            );

            if ($convertedProfit === null) {
                continue;
            }

            if ($convertedProfit > $maxProfit) {
                $maxProfit = $convertedProfit;
                $bestTrade = $deal;
                $bestTrade->converted_profit = $convertedProfit;
            }

            if ($convertedProfit < $minProfit) {
                $minProfit = $convertedProfit;
                $worstTrade = $deal;
                $worstTrade->converted_profit = $convertedProfit;
            }
        }

        if ($bestTrade === null && $worstTrade === null) {
            return [
                'best_trade' => null,
                'worst_trade' => null,
            ];
        }

        return [
            'best_trade' => $bestTrade,
            'worst_trade' => $worstTrade,
        ];
    }
}
