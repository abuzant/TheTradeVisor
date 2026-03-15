<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get performance analytics for authenticated user
     */
    public function performance(Request $request): JsonResponse
    {
        $user = $request->get('authenticated_user');
        
        // Validate parameters
        $validated = $request->validate([
            'account_id' => 'nullable|integer|exists:trading_accounts,id',
            'days' => 'nullable|integer|in:1,7,30,90,365'
        ]);
        
        $days = $validated['days'] ?? 30;
        $accountIds = $user->tradingAccounts()->pluck('id');
        
        // If specific account requested, verify ownership
        if (!empty($validated['account_id'])) {
            if (!$accountIds->contains($validated['account_id'])) {
                return response()->json([
                    'error' => [
                        'code' => 'FORBIDDEN',
                        'message' => 'Access denied',
                        'details' => 'The specified account does not belong to you'
                    ]
                ], 403);
            }
            $accountIds = collect([$validated['account_id']]);
        }
        
        // Get deals for the period
        $deals = Deal::whereIn('trading_account_id', $accountIds)
            ->where('time', '>=', now()->subDays($days))
            ->where('entry', '!=', 1)
            ->get();
        
        if ($deals->isEmpty()) {
            return response()->json([
                'data' => [
                    'total_trades' => 0,
                    'winning_trades' => 0,
                    'losing_trades' => 0,
                    'win_rate' => 0,
                    'profit_factor' => 0,
                    'total_profit' => 0,
                    'total_loss' => 0,
                    'net_profit' => 0,
                    'average_win' => 0,
                    'average_loss' => 0,
                    'largest_win' => 0,
                    'largest_loss' => 0
                ]
            ]);
        }
        
        // Calculate metrics
        $totalTrades = $deals->count();
        $winningTrades = $deals->where('profit', '>', 0);
        $losingTrades = $deals->where('profit', '<', 0);
        
        $totalProfit = $winningTrades->sum('profit');
        $totalLoss = abs($losingTrades->sum('profit'));
        $netProfit = $deals->sum('profit');
        
        $winCount = $winningTrades->count();
        $lossCount = $losingTrades->count();
        
        $winRate = $totalTrades > 0 ? round(($winCount / $totalTrades) * 100, 2) : 0;
        $profitFactor = $totalLoss > 0 ? round($totalProfit / $totalLoss, 2) : 0;
        
        $averageWin = $winCount > 0 ? round($totalProfit / $winCount, 2) : 0;
        $averageLoss = $lossCount > 0 ? round($totalLoss / $lossCount, 2) : 0;
        
        $largestWin = $winningTrades->max('profit') ?? 0;
        $largestLoss = abs($losingTrades->min('profit') ?? 0);
        
        return response()->json([
            'data' => [
                'total_trades' => $totalTrades,
                'winning_trades' => $winCount,
                'losing_trades' => $lossCount,
                'win_rate' => $winRate,
                'profit_factor' => $profitFactor,
                'total_profit' => round($totalProfit, 2),
                'total_loss' => round($totalLoss, 2),
                'net_profit' => round($netProfit, 2),
                'average_win' => $averageWin,
                'average_loss' => $averageLoss,
                'largest_win' => round($largestWin, 2),
                'largest_loss' => round($largestLoss, 2)
            ]
        ]);
    }
}
