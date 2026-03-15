<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class DigestRenderService
{
    /**
     * Render the user’s performance page as a standalone HTML file.
     *
     * @param User $user
     * @param string $date Y-m-d
     * @return string Path like "digests/{user_id}/{date}.html"
     */
    public function renderPerformancePage(User $user, string $date): string
    {
        // Prepare data the same way the PerformanceController does
        $accounts = $user->tradingAccounts()->active()->get();
        $periods = [
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
        ];

        // Example metrics (reuse existing logic or extract from PerformanceController)
        $metrics = [];
        foreach ($periods as $days => $start) {
            $deals = \App\Models\Deal::whereIn('trading_account_id', $accounts->pluck('id'))
                ->whereBetween('time', [$start, now()])
                ->whereIn('entry', ['out', 'inout'])
                ->get();

            $totalTrades = $deals->count();
            $winningTrades = $deals->where('profit', '>', 0)->count();
            $totalProfit = $deals->sum('profit');
            $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

            $metrics[$days] = compact('totalTrades', 'winningTrades', 'totalProfit', 'winRate');
        }

        // Top winning and losing symbols (USD conversion placeholder)
        $allDeals = \App\Models\Deal::whereIn('trading_account_id', $accounts->pluck('id'))
            ->whereBetween('time', [$periods['7'], now()])
            ->get();

        $topDeal = $allDeals->where('profit', '>', 0)->sortByDesc('profit')->first();
        $worstDeal = $allDeals->where('profit', '<', 0)->sortBy('profit')->first();

        // Normalize and map symbols using the correct columns
        $topSymbol = null;
        $worstSymbol = null;
        if ($topDeal) {
            $mapped = \App\Models\SymbolMapping::where('raw_symbol', $topDeal->symbol)->first();
            $topSymbol = [
                'symbol' => $mapped->normalized_symbol ?? $topDeal->symbol,
                'original' => $topDeal->symbol,
                'profit' => $topDeal->profit,
            ];
        }
        if ($worstDeal) {
            $mapped = \App\Models\SymbolMapping::where('raw_symbol', $worstDeal->symbol)->first();
            $worstSymbol = [
                'symbol' => $mapped->normalized_symbol ?? $worstDeal->symbol,
                'original' => $worstDeal->symbol,
                'profit' => $worstDeal->profit,
            ];
        }

        // Open trades (positions without a closing deal)
        $openTrades = \App\Models\Position::whereIn('trading_account_id', $accounts->pluck('id'))
            ->where('is_open', true)
            ->with('tradingAccount')
            ->get()
            ->map(function ($pos) {
                $mapped = \App\Models\SymbolMapping::where('raw_symbol', $pos->symbol)->first();
                return [
                    'symbol' => $mapped->normalized_symbol ?? $pos->symbol,
                    'original' => $pos->symbol,
                    'type' => $pos->type,
                    'volume' => $pos->volume,
                    'open_price' => $pos->open_price,
                    'current_price' => $pos->current_price ?? $pos->open_price,
                    'profit' => $pos->profit ?? 0,
                ];
            });

        // Render a digest-specific Blade view that inlines CSS
        $html = View::make('digest.performance-html', [
            'user' => $user,
            'accounts' => $accounts,
            'metrics' => $metrics,
            'topSymbol' => $topSymbol,
            'worstSymbol' => $worstSymbol,
            'openTrades' => $openTrades,
            'generatedAt' => now()->toDateTimeString(),
        ])->render();

        // Ensure storage directory exists
        $directory = "digests/{$user->id}";
        Storage::disk('local')->makeDirectory($directory);

        // Save HTML file
        $filename = "{$date}.html";
        $path = "{$directory}/{$filename}";
        Storage::disk('local')->put($path, $html);

        return $path;
    }

    /**
     * Get the full local filesystem path for a stored digest.
     */
    public function localPath(string $relativePath): string
    {
        return storage_path('app/' . $relativePath);
    }
}
