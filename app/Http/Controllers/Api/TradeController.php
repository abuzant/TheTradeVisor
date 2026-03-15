<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TradeController extends Controller
{
    /**
     * Get trades for authenticated user with filters
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->get('authenticated_user');
        
        // Validate query parameters
        $validated = $request->validate([
            'account_id' => 'nullable|integer|exists:trading_accounts,id',
            'symbol' => 'nullable|string|max:20',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1'
        ]);
        
        $limit = $validated['limit'] ?? 50;
        $page = $validated['page'] ?? 1;
        
        // Get user's account IDs
        $accountIds = $user->tradingAccounts()->pluck('id');
        
        // Build query
        $query = Deal::whereIn('trading_account_id', $accountIds)
            ->where('entry', '!=', 1) // Exclude entry deals
            ->orderBy('time', 'desc');
        
        // Apply filters
        if (!empty($validated['account_id'])) {
            // Verify account belongs to user
            if (!$accountIds->contains($validated['account_id'])) {
                return response()->json([
                    'error' => [
                        'code' => 'FORBIDDEN',
                        'message' => 'Access denied',
                        'details' => 'The specified account does not belong to you'
                    ]
                ], 403);
            }
            $query->where('trading_account_id', $validated['account_id']);
        }
        
        if (!empty($validated['symbol'])) {
            $query->where('symbol', 'LIKE', '%' . $validated['symbol'] . '%');
        }
        
        if (!empty($validated['from_date'])) {
            $query->where('time', '>=', $validated['from_date']);
        }
        
        if (!empty($validated['to_date'])) {
            $query->where('time', '<=', $validated['to_date'] . ' 23:59:59');
        }
        
        // Get total count before pagination
        $total = $query->count();
        
        // Apply pagination
        $offset = ($page - 1) * $limit;
        $trades = $query->skip($offset)->take($limit)->get();
        
        // Format response
        $data = $trades->map(function ($trade) {
            return [
                'id' => $trade->id,
                'account_id' => $trade->trading_account_id,
                'symbol' => $trade->symbol,
                'type' => $trade->action === 0 ? 'buy' : 'sell',
                'volume' => (float) $trade->volume,
                'open_price' => (float) $trade->price,
                'profit' => (float) $trade->profit,
                'commission' => (float) $trade->commission,
                'swap' => (float) $trade->swap,
                'time' => $trade->time->toIso8601String(),
            ];
        });
        
        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total' => $total,
                'per_page' => $limit
            ]
        ]);
    }
}
