<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\SymbolMapping;
use Illuminate\Http\Request;
use App\Traits\Sortable;

class TradesController extends Controller
{
    use Sortable;
    
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Deal::whereHas('tradingAccount', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->whereNotNull('symbol')
        ->where('symbol', '!=', '')
        ->where('symbol', '!=', 'UNKNOWN')
        ->with('tradingAccount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('symbol', 'like', "%{$search}%");
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', 'like', $request->type . '%');
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('time', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->where('time', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }

        // Define sortable columns
        $sortableColumns = ['time', 'symbol', 'type', 'volume', 'price', 'profit', 'commission', 'swap'];
        
        // Apply sorting
        if ($request->has('sort_by')) {
            $query = $this->applySorting($query, $request, $sortableColumns, 'time', 'desc');
        } else {
            $query->orderBy('time', 'desc');
        }

        $deals = $query->paginate(50)->withQueryString();

        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'time');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Multi-account context: Always use USD, convert all deals
        $currencyService = app(\App\Services\CurrencyService::class);
        foreach ($deals as $deal) {
            if ($deal->tradingAccount) {
                $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
                $deal->profit_usd = $currencyService->convert($deal->profit, $accountCurrency, 'USD');
                $deal->commission_usd = $currencyService->convert($deal->commission, $accountCurrency, 'USD');
                $deal->swap_usd = $currencyService->convert($deal->swap, $accountCurrency, 'USD');
            } else {
                $deal->profit_usd = $deal->profit;
                $deal->commission_usd = $deal->commission;
                $deal->swap_usd = $deal->swap;
            }
        }

        return view('trades.index', compact('deals', 'sortBy', 'sortDirection'));
    }


    public function symbol(Request $request, $symbol)
    {
        $user = $request->user();
    
    // Normalize the symbol
    $normalizedSymbol = strtoupper($symbol);
    
    // Find all raw symbols that map to this normalized symbol
    $symbolMappings = SymbolMapping::where('normalized_symbol', $normalizedSymbol)
        ->pluck('raw_symbol')
        ->toArray();
    
    // If no mappings, search for the symbol directly
    if (empty($symbolMappings)) {
        $symbolMappings = [$symbol];
    }
    
    // Get statistics using database aggregation (no memory loading)
    $baseQuery = Deal::whereHas('tradingAccount', function($q) use ($user) {
        $q->where('user_id', $user->id);
    })
    ->whereNotNull('symbol')
    ->where('symbol', '!=', '')
    ->whereIn('symbol', $symbolMappings)
    ->whereIn('entry', ['out', 'inout']);  // Only count closed trades
    
    // Get aggregated stats from database (clone query to avoid conflicts)
    $aggregateStats = clone $baseQuery;
    $aggregateStats = $aggregateStats->selectRaw('
        COUNT(*) as total_trades,
        SUM(CASE WHEN profit > 0 THEN 1 ELSE 0 END) as winning_trades,
        SUM(CASE WHEN profit < 0 THEN 1 ELSE 0 END) as losing_trades,
        SUM(profit) as total_profit,
        SUM(volume) as total_volume,
        MAX(profit) as best_trade,
        MIN(profit) as worst_trade,
        SUM(commission) as total_commission,
        SUM(swap) as total_swap,
        SUM(fee) as total_fee
    ')->first();
    
    // Get limited sample for detailed analysis (max 5000 recent trades) - use fresh query
    $allDeals = (clone $baseQuery)->with('tradingAccount')->orderBy('time', 'desc')->limit(5000)->get();
    
    // If no deals found, return empty stats
    if ($allDeals->isEmpty()) {
        $stats = [
            'total_trades' => 0,
            'winning_trades' => 0,
            'losing_trades' => 0,
            'win_rate' => 0,
            'total_profit' => 0,
            'total_volume' => 0,
            'avg_volume' => 0,
            'avg_profit' => 0,
            'best_trade' => 0,
            'worst_trade' => 0,
            'profit_factor' => 0,
            'risk_reward' => 0,
            'avg_win' => 0,
            'avg_loss' => 0,
            'buy_trades' => 0,
            'sell_trades' => 0,
            'buy_percentage' => 0,
            'sell_percentage' => 0,
            'best_direction' => 'N/A',
            'first_trade' => 'N/A',
            'last_trade' => 'N/A',
            'trading_days' => 0,
            'trades_per_day' => 0,
            'most_active_hour' => 'N/A',
            'day_distribution' => collect([]),
            'max_win_streak' => 0,
            'max_loss_streak' => 0,
            'current_streak' => 0,
            'total_commission' => 0,
            'total_swap' => 0,
            'total_fees' => 0,
            'avg_cost_per_trade' => 0,
        ];
        
        $deals = Deal::whereHas('tradingAccount', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with('tradingAccount')  // Load account for currency conversion
        ->whereIn('symbol', $symbolMappings)
        ->whereIn('entry', ['out', 'inout'])  // Only show closed trades with profit
        ->orderBy('time', 'desc')
        ->paginate(50);
        
        // Convert all profits to USD (multi-account context)
        $currencyService = app(\App\Services\CurrencyService::class);
        foreach ($deals as $deal) {
            $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
            $deal->profit_usd = $currencyService->convert($deal->profit, $accountCurrency, 'USD');
            $deal->commission_usd = $currencyService->convert($deal->commission, $accountCurrency, 'USD');
            $deal->swap_usd = $currencyService->convert($deal->swap, $accountCurrency, 'USD');
        }
        
        return view('trades.symbol', compact('deals', 'symbol', 'stats'));
    }
    
    // Use aggregated stats from database
    $totalTrades = $aggregateStats->total_trades ?? 0;
    $winningTrades = $aggregateStats->winning_trades ?? 0;
    $losingTrades = $aggregateStats->losing_trades ?? 0;
    $totalProfit = $aggregateStats->total_profit ?? 0;
    $totalVolume = $aggregateStats->total_volume ?? 0;
    
    // Convert all profits to USD for multi-account symbol view
    $currencyService = app(\App\Services\CurrencyService::class);
    $allProfitsUSD = [];
    $buyProfitUSD = 0;
    $sellProfitUSD = 0;
    $buyCount = 0;
    $sellCount = 0;
    
    foreach ($allDeals as $deal) {
        $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
        $profitUSD = $currencyService->convert($deal->profit, $accountCurrency, 'USD');
        $allProfitsUSD[] = $profitUSD;
        
        if ($deal->is_buy) {
            $buyProfitUSD += $profitUSD;
            $buyCount++;
        } elseif ($deal->is_sell) {
            $sellProfitUSD += $profitUSD;
            $sellCount++;
        }
    }
    
    $grossProfit = collect($allProfitsUSD)->filter(fn($p) => $p > 0)->sum();
    $grossLoss = abs(collect($allProfitsUSD)->filter(fn($p) => $p < 0)->sum());
    
    $buyTrades = $buyCount;
    $sellTrades = $sellCount;
    $buyProfit = $buyProfitUSD;
    $sellProfit = $sellProfitUSD;
    
    // Streaks
    $maxWinStreak = 0;
    $maxLossStreak = 0;
    $currentStreak = 0;
    $tempWin = 0;
    $tempLoss = 0;
    
    foreach($allDeals as $deal) {
        if ($deal->profit > 0) {
            $tempWin++;
            $tempLoss = 0;
            $maxWinStreak = max($maxWinStreak, $tempWin);
            $currentStreak = $tempWin;
        } elseif ($deal->profit < 0) {
            $tempLoss++;
            $tempWin = 0;
            $maxLossStreak = max($maxLossStreak, $tempLoss);
            $currentStreak = -$tempLoss;
        }
    }
    
    // Time analysis
    $firstTrade = $allDeals->last();
    $lastTrade = $allDeals->first();
    
    $tradingDays = $firstTrade && $lastTrade && $firstTrade->time && $lastTrade->time ? 
        \Carbon\Carbon::parse($firstTrade->time)->diffInDays(\Carbon\Carbon::parse($lastTrade->time)) + 1 : 1;
    
    // Day distribution
    $dayDist = [];
    foreach($allDeals as $deal) {
        if ($deal->time) {
            $day = \Carbon\Carbon::parse($deal->time)->format('l');
            if (!isset($dayDist[$day])) {
                $dayDist[$day] = 0;
            }
            $dayDist[$day]++;
        }
    }
    arsort($dayDist);
    
    // Hour analysis
    $hours = [];
    foreach($allDeals as $deal) {
        if ($deal->time) {
            $hour = \Carbon\Carbon::parse($deal->time)->format('H');
            if (!isset($hours[$hour])) {
                $hours[$hour] = 0;
            }
            $hours[$hour]++;
        }
    }
    arsort($hours);
    $mostActiveHour = !empty($hours) ? sprintf('%02d:00', array_key_first($hours)) : 'N/A';
    
    // Costs - need to convert from native currency to USD (multi-account context)
    $currencyService = app(\App\Services\CurrencyService::class);
    $totalCommissionUSD = 0;
    $totalSwapUSD = 0;
    $totalFeesUSD = 0;
    
    foreach ($allDeals as $deal) {
        if ($deal->tradingAccount) {
            $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
            $totalCommissionUSD += $currencyService->convert($deal->commission, $accountCurrency, 'USD');
            $totalSwapUSD += $currencyService->convert($deal->swap, $accountCurrency, 'USD');
            $totalFeesUSD += $currencyService->convert($deal->fee, $accountCurrency, 'USD');
        } else {
            $totalCommissionUSD += $deal->commission;
            $totalSwapUSD += $deal->swap;
            $totalFeesUSD += $deal->fee;
        }
    }
    
    $totalCommission = $totalCommissionUSD;
    $totalSwap = $totalSwapUSD;
    $totalFees = abs($totalCommission) + abs($totalFeesUSD);
    
    $stats = [
        'total_trades' => $totalTrades,
        'winning_trades' => $winningTrades,
        'losing_trades' => $losingTrades,
        'win_rate' => $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0,
        'total_profit' => $totalProfit,
        'total_volume' => $totalVolume,
        'avg_volume' => $totalTrades > 0 ? $totalVolume / $totalTrades : 0,
        'avg_profit' => $totalTrades > 0 ? $totalProfit / $totalTrades : 0,
        'best_trade' => !empty($allProfitsUSD) ? max($allProfitsUSD) : 0,
        'worst_trade' => !empty($allProfitsUSD) ? min($allProfitsUSD) : 0,
        'profit_factor' => $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : ($grossProfit > 0 ? 999 : 0),
        'risk_reward' => $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : 0,
        'avg_win' => $winningTrades > 0 ? $grossProfit / $winningTrades : 0,
        'avg_loss' => $losingTrades > 0 ? $grossLoss / $losingTrades : 0,
        'buy_trades' => $buyTrades,
        'sell_trades' => $sellTrades,
        'buy_percentage' => $totalTrades > 0 ? round(($buyTrades / $totalTrades) * 100, 1) : 0,
        'sell_percentage' => $totalTrades > 0 ? round(($sellTrades / $totalTrades) * 100, 1) : 0,
        'best_direction' => $buyProfit > $sellProfit ? 'Buy' : ($sellProfit > $buyProfit ? 'Sell' : 'Balanced'),
        'first_trade' => $firstTrade && $firstTrade->time ? \Carbon\Carbon::parse($firstTrade->time)->format('M d, Y') : 'N/A',
        'last_trade' => $lastTrade && $lastTrade->time ? \Carbon\Carbon::parse($lastTrade->time)->format('M d, Y') : 'N/A',
        'trading_days' => $tradingDays,
        'trades_per_day' => $tradingDays > 0 ? round($totalTrades / $tradingDays, 1) : 0,
        'most_active_hour' => $mostActiveHour,
        'day_distribution' => collect($dayDist)->take(7),
        'max_win_streak' => $maxWinStreak,
        'max_loss_streak' => $maxLossStreak,
        'current_streak' => $currentStreak,
        'total_commission' => $totalCommission,
        'total_swap' => $totalSwap,
        'total_fees' => $totalFees,
        'avg_cost_per_trade' => $totalTrades > 0 ? $totalFees / $totalTrades : 0,
    ];
    
    // Paginate - only show closed trades with profit
    $deals = Deal::whereHas('tradingAccount', function($q) use ($user) {
        $q->where('user_id', $user->id);
    })
    ->with('tradingAccount')  // Load account for currency conversion
    ->whereIn('symbol', $symbolMappings)
    ->whereIn('entry', ['out', 'inout'])  // Only show closed trades with profit
    ->orderBy('time', 'desc')
    ->paginate(50);
    
    // Convert all profits to USD (multi-account context)
    $currencyService = app(\App\Services\CurrencyService::class);
    foreach ($deals as $deal) {
        $accountCurrency = $deal->tradingAccount->account_currency ?? 'USD';
        $deal->profit_usd = $currencyService->convert($deal->profit, $accountCurrency, 'USD');
        $deal->commission_usd = $currencyService->convert($deal->commission, $accountCurrency, 'USD');
        $deal->swap_usd = $currencyService->convert($deal->swap, $accountCurrency, 'USD');
    }
    
    return view('trades.symbol', compact('deals', 'symbol', 'stats'));
}


}
