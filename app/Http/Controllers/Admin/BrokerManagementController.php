<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnterpriseBroker;
use App\Models\EnterpriseApiKey;
use App\Models\User;
use App\Models\WhitelistedBrokerUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BrokerManagementController extends Controller
{
    /**
     * Display a listing of enterprise brokers
     */
    public function index(Request $request)
    {
        $query = EnterpriseBroker::with('user');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'ILIKE', "%{$search}%")
                  ->orWhere('official_broker_name', 'ILIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false)
                      ->where(function($q) {
                          $q->whereNull('grace_period_ends_at')
                            ->orWhere('grace_period_ends_at', '<', now());
                      });
            } elseif ($request->status === 'grace') {
                $query->where('is_active', false)
                      ->where('grace_period_ends_at', '>=', now());
            }
        }

        $brokers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get stats for each broker
        foreach ($brokers as $broker) {
            $broker->total_accounts = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)->count();
            $broker->active_accounts = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
                ->where('last_seen_at', '>=', now()->subDays(30))
                ->count();
            $broker->total_users = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
                ->distinct('user_id')
                ->count('user_id');
            $broker->api_keys_count = $broker->apiKeys()->count();
        }

        return view('admin.brokers.index', compact('brokers'));
    }

    /**
     * Show the form for creating a new broker
     */
    public function create()
    {
        return view('admin.brokers.create');
    }

    /**
     * Store a newly created broker
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'official_broker_name' => 'required|string|max:255|unique:enterprise_brokers,official_broker_name',
            'monthly_fee' => 'required|numeric|min:0',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
            'subscription_months' => 'required|integer|min:1|max:24',
        ]);

        DB::beginTransaction();
        try {
            // Create admin user
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'is_enterprise_admin' => true,
                'is_active' => true,
            ]);

            // Create enterprise broker
            $broker = EnterpriseBroker::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'official_broker_name' => $request->official_broker_name,
                'is_active' => true,
                'monthly_fee' => $request->monthly_fee,
                'subscription_ends_at' => now()->addMonths($request->subscription_months),
                'grace_period_ends_at' => null,
            ]);

            // Create initial API key
            EnterpriseApiKey::create([
                'enterprise_broker_id' => $broker->id,
                'key' => EnterpriseApiKey::generateKey(),
                'name' => 'Primary API Key',
            ]);

            DB::commit();

            return redirect()->route('admin.brokers.show', $broker->id)
                ->with('success', 'Enterprise broker created successfully! Admin credentials have been set.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create broker: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified broker
     */
    public function show($id)
    {
        $broker = EnterpriseBroker::with('user', 'apiKeys')->findOrFail($id);

        // Get statistics
        $stats = [
            'total_accounts' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)->count(),
            'active_accounts' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
                ->where('last_seen_at', '>=', now()->subDays(30))
                ->count(),
            'dormant_accounts' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
                ->where(function($q) {
                    $q->where('last_seen_at', '<', now()->subDays(30))
                      ->orWhereNull('last_seen_at');
                })
                ->count(),
            'total_users' => WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // Get recent accounts
        $recentAccounts = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->with('tradingAccount', 'user')
            ->orderBy('last_seen_at', 'desc')
            ->limit(10)
            ->get();

        // Get API keys
        $apiKeys = $broker->apiKeys()->orderBy('created_at', 'desc')->get();

        return view('admin.brokers.show', compact('broker', 'stats', 'recentAccounts', 'apiKeys'));
    }

    /**
     * Show the form for editing the specified broker
     */
    public function edit($id)
    {
        $broker = EnterpriseBroker::with('user')->findOrFail($id);
        return view('admin.brokers.edit', compact('broker'));
    }

    /**
     * Update the specified broker
     */
    public function update(Request $request, $id)
    {
        $broker = EnterpriseBroker::findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'official_broker_name' => 'required|string|max:255|unique:enterprise_brokers,official_broker_name,' . $broker->id,
            'monthly_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'subscription_ends_at' => 'nullable|date',
            'grace_period_ends_at' => 'nullable|date',
        ]);

        $broker->update([
            'company_name' => $request->company_name,
            'official_broker_name' => $request->official_broker_name,
            'monthly_fee' => $request->monthly_fee,
            'is_active' => $request->is_active,
            'subscription_ends_at' => $request->subscription_ends_at,
            'grace_period_ends_at' => $request->grace_period_ends_at,
        ]);

        return redirect()->route('admin.brokers.show', $broker->id)
            ->with('success', 'Broker updated successfully');
    }

    /**
     * Remove the specified broker
     */
    public function destroy($id)
    {
        $broker = EnterpriseBroker::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete associated user
            $user = $broker->user;
            
            // Delete broker (cascade will handle API keys and usage records)
            $broker->delete();
            
            // Delete user
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.brokers.index')
                ->with('success', 'Enterprise broker deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete broker: ' . $e->getMessage());
        }
    }

    /**
     * Extend subscription
     */
    public function extendSubscription(Request $request, $id)
    {
        $broker = EnterpriseBroker::findOrFail($id);

        $request->validate([
            'months' => 'required|integer|min:1|max:24',
        ]);

        $months = (int) $request->months;
        $currentEnd = $broker->subscription_ends_at ?? now();
        $newEnd = $currentEnd->copy()->addMonths($months);

        $broker->update([
            'subscription_ends_at' => $newEnd,
            'is_active' => true,
            'grace_period_ends_at' => null,
        ]);

        return back()->with('success', "Subscription extended by {$months} month(s)");
    }

    /**
     * Create new API key for broker
     */
    public function createApiKey(Request $request, $id)
    {
        $broker = EnterpriseBroker::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $apiKey = EnterpriseApiKey::create([
            'enterprise_broker_id' => $broker->id,
            'key' => EnterpriseApiKey::generateKey(),
            'name' => $request->name,
        ]);

        return back()->with('success', 'API key created successfully')
            ->with('new_api_key', $apiKey->key);
    }

    /**
     * Revoke (delete) API key
     */
    public function revokeApiKey($brokerId, $keyId)
    {
        $broker = EnterpriseBroker::findOrFail($brokerId);
        $apiKey = EnterpriseApiKey::where('enterprise_broker_id', $broker->id)
            ->where('id', $keyId)
            ->firstOrFail();

        $apiKey->delete();

        return back()->with('success', 'API key revoked successfully');
    }

    /**
     * Toggle broker active status
     */
    public function toggleStatus($id)
    {
        $broker = EnterpriseBroker::findOrFail($id);
        
        $broker->update([
            'is_active' => !$broker->is_active,
        ]);

        $status = $broker->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Broker {$status} successfully");
    }

    /**
     * View all accounts for this broker
     */
    public function accounts($id, Request $request)
    {
        $broker = EnterpriseBroker::findOrFail($id);

        $query = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
            ->with('tradingAccount', 'user');

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('last_seen_at', '>=', now()->subDays(30));
            } elseif ($request->status === 'dormant') {
                $query->where(function($q) {
                    $q->where('last_seen_at', '<', now()->subDays(30))
                      ->orWhereNull('last_seen_at');
                });
            }
        }

        $accounts = $query->orderBy('last_seen_at', 'desc')->paginate(50);

        return view('admin.brokers.accounts', compact('broker', 'accounts'));
    }
}
