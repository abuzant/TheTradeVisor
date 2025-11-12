<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    /**
     * Get all trading accounts for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->get('authenticated_user');
        
        $accounts = $user->tradingAccounts()
            ->select([
                'id',
                'account_number',
                'broker_name',
                'platform_type',
                'account_currency',
                'balance',
                'equity',
                'margin',
                'free_margin',
                'profit',
                'is_active',
                'created_at',
                'last_sync_at'
            ])
            ->get();
        
        return response()->json([
            'data' => $accounts,
            'meta' => [
                'total' => $accounts->count()
            ]
        ]);
    }
    
    /**
     * Get specific trading account by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->get('authenticated_user');
        
        $account = $user->tradingAccounts()
            ->where('id', $id)
            ->first();
        
        if (!$account) {
            return response()->json([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Account not found',
                    'details' => 'The requested account does not exist or does not belong to you'
                ]
            ], 404);
        }
        
        return response()->json([
            'data' => [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'broker_name' => $account->broker_name,
                'platform_type' => $account->platform_type,
                'account_currency' => $account->account_currency,
                'balance' => (float) $account->balance,
                'equity' => (float) $account->equity,
                'margin' => (float) $account->margin,
                'free_margin' => (float) $account->free_margin,
                'profit' => (float) $account->profit,
                'is_active' => (bool) $account->is_active,
                'last_sync_at' => $account->last_sync_at?->toIso8601String(),
                'created_at' => $account->created_at->toIso8601String()
            ]
        ]);
    }
}
