<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Traits\Sortable;

class AccountManagementController extends Controller
{
    use Sortable;
    
    public function index(Request $request)
    {
        $search = $request->get('search');
        $broker = $request->get('broker');
        $currency = $request->get('currency');
        $status = $request->get('status');

        $query = $request->user()->tradingAccounts()
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('broker_name', 'like', "%{$search}%")
                      ->orWhere('account_number', 'like', "%{$search}%");
                });
            })
            ->when($broker, function($query, $broker) {
                $query->where('broker_name', $broker);
            })
            ->when($currency, function($query, $currency) {
                $query->where('account_currency', $currency);
            })
            ->when($status !== null, function($query) use ($status) {
                if ($status === 'active') {
                    $query->where('is_active', true)->where('is_paused', false);
                } elseif ($status === 'paused') {
                    $query->where('is_paused', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            });
        
        // Define sortable columns
        $sortableColumns = ['broker_name', 'account_number', 'account_currency', 'balance', 'equity', 'profit', 'last_sync_at', 'created_at'];
        
        // Apply sorting
        if ($request->has('sort_by')) {
            $query = $this->applySorting($query, $request, $sortableColumns, 'last_sync_at', 'desc');
        } else {
            $query->orderBy('last_sync_at', 'desc');
        }
        
        $accounts = $query->paginate(20)->withQueryString();

        // Get unique brokers and currencies for filters
        $brokers = $request->user()->tradingAccounts()
            ->select('broker_name')
            ->distinct()
            ->pluck('broker_name');

        $currencies = $request->user()->tradingAccounts()
            ->select('account_currency')
            ->distinct()
            ->pluck('account_currency');

        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'last_sync_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        return view('accounts.index', compact('accounts', 'brokers', 'currencies', 'search', 'broker', 'currency', 'status', 'sortBy', 'sortDirection'));
    }

    public function pause(Request $request, TradingAccount $account)
    {
        // Ensure account belongs to user
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $account->pause($request->user(), $request->reason);

        return redirect()->back()->with('success', 'Account paused successfully.');
    }

    public function unpause(Request $request, TradingAccount $account)
    {
        // Ensure account belongs to user
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $account->unpause();

        return redirect()->back()->with('success', 'Account resumed successfully.');
    }
}
