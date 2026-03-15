<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\Sortable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TradingAccount;
use App\Models\EnterpriseBroker;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    use Sortable;

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $hideZeroAccounts = $request->get('hide_zero_accounts', false);

        // Define sortable columns (removed subscription_tier and max_accounts)
        $sortableColumns = [
            'name',
            'email',
            'is_active',
            'is_admin',
            'is_enterprise_admin',
            'created_at',
            'last_login_at',
        ];

        $query = User::query()
            ->withCount('tradingAccounts')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($hideZeroAccounts, function($query) {
                $query->whereHas('tradingAccounts');
            });

        // Apply sorting using the trait
        $query = $this->applySorting($query, $request, $sortableColumns, 'created_at', 'desc');

        // Paginate and append query parameters
        $users = $query->paginate(25)->appends($request->query());

        // Get sort parameters for view
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        return view('admin.users.index', compact(
            'users', 
            'search', 
            'status',
            'hideZeroAccounts',
            'sortBy',
            'sortDirection'
        ));
    }

    public function show(User $user)
    {
        $user->load(['tradingAccounts' => function($query) {
            $query->orderBy('last_sync_at', 'desc');
        }]);

        // Get list of enterprise broker names for star indicator
        $enterpriseBrokerNames = EnterpriseBroker::where('is_active', true)
            ->pluck('official_broker_name')
            ->toArray();

        // Get user statistics (multi-account context: Always use USD)
        $displayCurrency = 'USD';

        $stats = [
            'total_accounts' => $user->tradingAccounts()->count(),
            'active_accounts' => $user->tradingAccounts()->where('is_active', true)->count(),
            'total_balance' => $user->tradingAccounts->sum(function($account) use ($displayCurrency) {
                return $account->getBalanceInCurrency($displayCurrency);
            }),
            'total_equity' => $user->tradingAccounts->sum(function($account) use ($displayCurrency) {
                return $account->getEquityInCurrency($displayCurrency);
            }),
            'member_since' => $user->created_at->diffForHumans(),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
        ];

        return view('admin.users.show', compact('user', 'stats', 'enterpriseBrokerNames'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_active' => 'required|boolean',
            'is_admin' => 'required|boolean',
        ]);

        // All users have unlimited accounts now - no subscription tiers
        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    public function suspend(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User suspended successfully');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User activated successfully');
    }

    public function regenerateApiKey(User $user)
    {
        $newKey = $user->regenerateApiKey();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'API key regenerated successfully')
            ->with('new_api_key', $newKey);
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
