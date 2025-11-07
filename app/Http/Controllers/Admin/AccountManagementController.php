<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingAccount;
use App\Models\User;
use App\Traits\Sortable;

class AccountManagementController extends Controller
{

    use Sortable;

    /**
     * Display a listing of trading accounts
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $broker = $request->get('broker');
        $currency = $request->get('currency');
        $status = $request->get('status');
        $userId = $request->get('user_id');
    
        // Define sortable columns
        $sortableColumns = [
            'broker_name',
            'account_number',
            'account_currency',
            'balance',
            'equity',
            'last_sync_at',
            'is_active',
            'is_paused',
            'created_at',
        ];
    
        $query = TradingAccount::with('user')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('broker_name', 'like', "%{$search}%")
                      ->orWhere('account_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($broker, function($query, $broker) {
                $query->where('broker_name', $broker);
            })
            ->when($currency, function($query, $currency) {
                $query->where('account_currency', $currency);
            })
            ->when($userId, function($query, $userId) {
                $query->where('user_id', $userId);
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
    
        // Apply sorting using the trait
        $query = $this->applySorting($query, $request, $sortableColumns, 'last_sync_at', 'desc');
    
        // Paginate and append query parameters
        $accounts = $query->paginate(25)->appends($request->query());
    
        // Get filters
        $brokers = TradingAccount::select('broker_name')->distinct()->pluck('broker_name');
        $currencies = TradingAccount::select('account_currency')->distinct()->pluck('account_currency');
    
        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'last_sync_at');
        $sortDirection = $request->get('sort_direction', 'desc');
    
        return view('admin.accounts.index', compact(
            'accounts', 
            'brokers', 
            'currencies', 
            'search', 
            'broker', 
            'currency', 
            'status', 
            'userId',
            'sortBy',
            'sortDirection'
        ));
    }

    /**
     * Pause an account
     */
    public function pause(Request $request, TradingAccount $account)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Pause the account
        $account->is_paused = true;
        $account->paused_at = now();
        $account->paused_by = $request->user()->id;
        $account->pause_reason = $request->reason;
        $account->save();

        return redirect()->back()->with('success', 'Account paused successfully.');
    }

    /**
     * Unpause an account
     */
    public function unpause(Request $request, TradingAccount $account)
    {
        // Unpause the account
        $account->is_paused = false;
        $account->paused_at = null;
        $account->paused_by = null;
        $account->pause_reason = null;
        $account->save();

        return redirect()->back()->with('success', 'Account resumed successfully.');
    }
}
