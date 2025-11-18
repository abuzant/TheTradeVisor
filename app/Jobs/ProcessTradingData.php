<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\TradingAccount;
use App\Models\Position;
use App\Models\Order;
use App\Models\Deal;
use App\Services\TradingDataValidationService;
use Carbon\Carbon;

class ProcessTradingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $data;
    protected $filename;

    public function __construct($data, $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $userId = $this->data['user_id'];
            $clientIp = $this->data['client_ip'];
            $isFirstRun = $this->data['meta']['is_first_run'] ?? false;

            Log::info('Processing current trading data', [
                'user_id' => $userId,
                'is_first_run' => $isFirstRun,
                'file' => $this->filename
            ]);

            // 1. Process Account Information
            $tradingAccount = $this->processAccount($userId, $clientIp);

            // 2. Process Open Positions
            if (isset($this->data['positions']) && is_array($this->data['positions'])) {
                $this->processPositions($tradingAccount, $this->data['positions']);
            }

            // 3. Process Pending Orders
            if (isset($this->data['orders']) && is_array($this->data['orders'])) {
                $this->processOrders($tradingAccount, $this->data['orders']);
            }

            // 4. Process Recent History/Deals
            if (isset($this->data['history']) && is_array($this->data['history'])) {
                $this->processDeals($tradingAccount, $this->data['history']);
            }

            DB::commit();

            // Clear user-specific caches after successful processing
            $this->clearUserCache($userId, $tradingAccount->id);

            Log::info('Successfully processed current trading data', [
                'trading_account_id' => $tradingAccount->id,
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process trading data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $this->filename
            ]);

            throw $e;
        }
    }

    /**
     * Process account information
     */
    protected function processAccount($userId, $clientIp)
    {
        $accountData = $this->data['account'];

        // Detect geolocation from IP if available
        $country = null;
        $countryName = null;
        $city = null;
        $timezone = null;

        if ($clientIp) {
            try {
                $geoService = app(\App\Services\GeoIPService::class);
                $geoData = $geoService->getCountryFromIP($clientIp);
                
                if ($geoData) {
                    $country = $geoData['country_code'] ?? null;
                    $countryName = $geoData['country_name'] ?? null;
                }
            } catch (\Exception $e) {
                // Silently fail, geolocation is optional
                \Log::debug('GeoIP lookup failed in ProcessTradingData', [
                    'ip' => $clientIp,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Generate unique account identifier components
        $accountNumber = $accountData['account_number'] ?? null;
        $accountHash = $accountData['account_hash'] ?? null;
        $brokerServer = $accountData['server'];

        // Build unique search criteria
        // For non-anonymized accounts: user_id + broker_server + account_number
        // For anonymized accounts: user_id + broker_server + account_hash
        $searchCriteria = [
            'user_id' => $userId,
            'broker_server' => $brokerServer,
        ];

        if (!empty($accountNumber)) {
            $searchCriteria['account_number'] = $accountNumber;
        } elseif (!empty($accountHash)) {
            $searchCriteria['account_hash'] = $accountHash;
        }

        // Use firstOrCreate with database locking to prevent race conditions
        // This will either find existing account or create new one atomically
        try {
            $tradingAccount = TradingAccount::lockForUpdate()
                ->where($searchCriteria)
                ->first();

            if (!$tradingAccount) {
                // SAFETY NET: Double-check account limit before creating
                $user = \App\Models\User::find($userId);
                $currentAccountCount = $user->tradingAccounts()->count();
                
                if ($currentAccountCount >= $user->max_accounts) {
                    Log::error('Account limit exceeded in job - should have been caught by controller', [
                        'user_id' => $userId,
                        'current_accounts' => $currentAccountCount,
                        'max_accounts' => $user->max_accounts,
                        'subscription_tier' => $user->subscription_tier,
                        'account_number' => $accountNumber,
                        'account_hash' => $accountHash,
                    ]);
                    
                    throw new \Exception(
                        "Account limit exceeded. You have {$currentAccountCount} account(s) but your {$user->subscription_tier} plan allows {$user->max_accounts}. " .
                        "Please upgrade your plan at https://thetradevisor.com/pricing to add more accounts."
                    );
                }
                
                // Create new account with all required fields
                $tradingAccount = TradingAccount::create(array_merge($searchCriteria, [
                    'account_uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'broker_name' => $accountData['broker'],
                    'account_currency' => $accountData['currency'],
                    'leverage' => $accountData['leverage'] ?? 1,
                ]));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // If we hit a unique constraint violation, it means another job created the account
            // Just fetch it again
            if (str_contains($e->getMessage(), 'unique_user_broker')) {
                $tradingAccount = TradingAccount::where($searchCriteria)->first();
                
                if (!$tradingAccount) {
                    throw $e; // Re-throw if we still can't find it
                }
            } else {
                throw $e;
            }
        }

        // Update account information
        $tradingAccount->update([
            'broker_name' => $accountData['broker'],
            'account_name' => $accountData['name'] ?? null,
            'account_currency' => $accountData['currency'],
            'leverage' => $accountData['leverage'],
            'balance' => $accountData['balance'],
            'equity' => $accountData['equity'],
            'margin' => $accountData['margin'],
            'free_margin' => $accountData['free_margin'],
            'margin_level' => $accountData['margin_level'],
            'profit' => $accountData['profit'],
            'credit' => $accountData['credit'],
            'trade_allowed' => $accountData['trade_allowed'] ?? true,
            'trade_expert' => $accountData['trade_expert'] ?? true,
            'last_seen_ip' => $clientIp,
            'detected_country' => $country,
            'country_code' => $country,
            'country_name' => $countryName,
            'detected_city' => $city,
            'detected_timezone' => $timezone,
            'last_sync_at' => now(),
            'is_active' => true,
            'platform_type' => $accountData['platform_type'] ?? null,
            'platform_build' => $accountData['platform_build'] ?? null,
            'account_mode' => $accountData['account_mode'] ?? null,
            'platform_detected_at' => isset($accountData['platform_type']) ? now() : null,
        ]);

        // Record account snapshot for historical margin/free_margin tracking
        \App\Models\AccountSnapshot::create([
            'user_id' => $tradingAccount->user_id,
            'trading_account_id' => $tradingAccount->id,
            'balance' => $accountData['balance'],
            'equity' => $accountData['equity'],
            'margin' => $accountData['margin'],
            'free_margin' => $accountData['free_margin'],
            'margin_level' => $accountData['margin_level'],
            'profit' => $accountData['profit'],
            'snapshot_time' => now(),
            'is_historical' => false,
            'source' => 'api',
        ]);

        return $tradingAccount;
    }

    /**
     * Process open positions
     */
    protected function processPositions($tradingAccount, $positions)
    {
        // Mark all positions as closed first
        Position::where('trading_account_id', $tradingAccount->id)
            ->where('is_open', true)
            ->update(['is_open' => false]);

        foreach ($positions as $posData) {
            try {
                Position::updateOrCreate(
                    [
                        'trading_account_id' => $tradingAccount->id,
                        'ticket' => $posData['ticket'],
                    ],
                    [
                        'symbol' => $posData['symbol'],
                        'comment' => $posData['comment'] ?? null,
                        'external_id' => $posData['external_id'] ?? null,
                        'type' => $posData['type'],
                        'reason' => $posData['reason'] ?? 'unknown',
                        'volume' => $posData['volume'] ?? 0,
                        'open_price' => $posData['price_open'] ?? $posData['open_price'] ?? 0,
                        'current_price' => $posData['price_current'] ?? $posData['current_price'] ?? 0,
                        'sl' => $posData['sl'] ?? null,
                        'tp' => $posData['tp'] ?? null,
                        'profit' => $posData['profit'] ?? 0,
                        'swap' => $posData['swap'] ?? 0,
                        'commission' => $posData['commission'] ?? 0,
                        'open_time' => $this->parseDateTime($posData['time'] ?? $posData['open_time'] ?? now()),
                        'update_time' => $this->parseDateTime($posData['update_time'] ?? now()),
                        'magic' => $posData['magic'] ?? 0,
                        'identifier' => $posData['identifier'] ?? $posData['ticket'],
                        'is_open' => true,
                        'platform_type' => $posData['platform_type'] ?? null,
                        'activity_type' => $posData['activity_type'] ?? null,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to process position', [
                    'ticket' => $posData['ticket'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Processed positions', [
            'account_id' => $tradingAccount->id,
            'count' => count($positions)
        ]);
    }

    /**
     * Process pending orders
     */
    protected function processOrders($tradingAccount, $orders)
    {
        // Mark all orders as inactive first
        Order::where('trading_account_id', $tradingAccount->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        foreach ($orders as $orderData) {
            try {
                Order::updateOrCreate(
                    [
                        'trading_account_id' => $tradingAccount->id,
                        'ticket' => $orderData['ticket'],
                    ],
                    [
                        'symbol' => $orderData['symbol'],
                        'comment' => $orderData['comment'] ?? null,
                        'external_id' => $orderData['external_id'] ?? null,
                        'type' => $orderData['type'],
                        'state' => $orderData['state'] ?? null,
                        'reason' => $orderData['reason'] ?? 'unknown',
                        'volume_initial' => $orderData['volume'] ?? $orderData['volume_initial'] ?? 0,
                        'volume_current' => $orderData['volume'] ?? $orderData['volume_current'] ?? 0,
                        'price_open' => $orderData['price_open'] ?? $orderData['open_price'] ?? 0,
                        'price_current' => $orderData['price_current'] ?? $orderData['current_price'] ?? 0,
                        'price_stoplimit' => $orderData['price_stoplimit'] ?? null,
                        'sl' => $orderData['sl'] ?? null,
                        'tp' => $orderData['tp'] ?? null,
                        'time_setup' => $this->parseDateTime($orderData['time_setup'] ?? now()),
                        'time_setup_msc' => $orderData['time_setup_msc'] ?? null,
                        'time_done' => $this->parseDateTime($orderData['time_done'] ?? null),
                        'time_done_msc' => $orderData['time_done_msc'] ?? null,
                        'expiration' => $this->parseDateTime($orderData['expiration'] ?? null),
                        'position_id' => $orderData['position_id'] ?? null,
                        'position_by_id' => $orderData['position_by_id'] ?? null,
                        'magic' => $orderData['magic'] ?? 0,
                        'is_active' => true,
                        'platform_type' => $orderData['platform_type'] ?? null,
                        'activity_type' => $orderData['activity_type'] ?? null,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to process order', [
                    'ticket' => $orderData['ticket'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Processed orders', [
            'account_id' => $tradingAccount->id,
            'count' => count($orders)
        ]);
    }

    /**
     * Process closed deals/history
     */
    protected function processDeals($tradingAccount, $deals)
    {
        $newDealsCount = 0;
        $duplicatesSkipped = 0;
        $nonTradingSkipped = 0;

        foreach ($deals as $dealData) {
            // Skip deals without symbols (admin fees, balance operations, etc.)
            if (empty($dealData['symbol']) || trim($dealData['symbol']) === '') {
                $nonTradingSkipped++;
                continue;
            }

            // Check if deal already exists (avoid duplicates)
            $exists = Deal::where('trading_account_id', $tradingAccount->id)
                ->where('ticket', $dealData['ticket'])
                ->exists();

            if (!$exists) {
                try {
                    // Validate and normalize deal data
                    $validator = new TradingDataValidationService();
                    $validatedData = $validator->validateDeal($dealData);
                    
                    Deal::create(array_merge(
                        ['trading_account_id' => $tradingAccount->id],
                        $validatedData
                    ));

                    $newDealsCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to create deal', [
                        'ticket' => $dealData['ticket'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $duplicatesSkipped++;
            }
        }

        Log::info('Processed deals', [
            'account_id' => $tradingAccount->id,
            'new_deals' => $newDealsCount,
            'duplicates_skipped' => $duplicatesSkipped,
            'non_trading_skipped' => $nonTradingSkipped,
        ]);
    }

    /**
     * Parse datetime string
     */
    protected function parseDateTime($dateTimeString)
    {
        if (empty($dateTimeString) || $dateTimeString === '1970-01-01 00:00:00' || $dateTimeString === '1970.01.01 00:00:00') {
            return null;
        }

        try {
            // MT5 sometimes uses dots instead of dashes (e.g., "2025.09.15 10:30:00")
            // Convert dots to dashes for proper parsing
            $normalized = str_replace('.', '-', $dateTimeString);
            
            return Carbon::parse($normalized);
        } catch (\Exception $e) {
            Log::warning('Failed to parse datetime', [
                'datetime' => $dateTimeString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Clear user-specific caches after processing new data
     */
    protected function clearUserCache($userId, $accountId)
    {
        try {
            // Clear dashboard caches
            Cache::forget("dashboard.user.{$userId}.*");
            Cache::forget("dashboard.deals.{$userId}");
            
            // Clear account detail caches
            Cache::forget("account.{$accountId}.details.*");
            
            // Clear performance caches (use pattern matching if available)
            $accountIds = TradingAccount::where('user_id', $userId)->pluck('id')->toArray();
            $perfKey = 'performance.' . md5(implode(',', $accountIds)) . ".*";
            Cache::forget($perfKey);
            
            // Clear broker analytics (global cache)
            Cache::forget("broker_analytics_*");
            
            Log::debug('Cache cleared for user', ['user_id' => $userId, 'account_id' => $accountId]);
        } catch (\Exception $e) {
            // Don't fail the job if cache clearing fails
            Log::warning('Failed to clear cache', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
