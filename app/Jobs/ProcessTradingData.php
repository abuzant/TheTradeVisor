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

            // 4. Process Recent History/Deals (MT4 sends 'deals', MT5 sends 'history')
            $dealsData = $this->data['deals'] ?? $this->data['history'] ?? [];
            if (!empty($dealsData) && is_array($dealsData)) {
                $this->processDeals($tradingAccount, $dealsData);
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

        // Use firstOrCreate without explicit locking to prevent deadlocks
        // The unique constraint in the database will handle race conditions
        try {
            $tradingAccount = TradingAccount::firstOrCreate(
                $searchCriteria,
                [
                    'account_uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'broker_name' => $accountData['broker'],
                    'account_currency' => $accountData['currency'],
                    'leverage' => $accountData['leverage'] ?? 1,
                ]
            );
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

        // Calculate margin_level if not provided (MT4 doesn't always send it)
        $marginLevel = $accountData['margin_level'] ?? null;
        if ($marginLevel === null && isset($accountData['margin']) && $accountData['margin'] > 0) {
            // Margin Level = (Equity / Margin) * 100
            $marginLevel = ($accountData['equity'] / $accountData['margin']) * 100;
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
            'margin_level' => $marginLevel,
            'profit' => $accountData['profit'] ?? 0,
            'credit' => $accountData['credit'] ?? 0,
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
            'margin_level' => $marginLevel,
            'profit' => $accountData['profit'] ?? 0,
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
        // Get list of incoming position tickets
        $incomingTickets = collect($positions)->pluck('ticket')->toArray();
        
        // Mark positions as closed ONLY if they're not in the incoming data
        Position::where('trading_account_id', $tradingAccount->id)
            ->where('is_open', true)
            ->whereNotIn('ticket', $incomingTickets)
            ->update(['is_open' => false]);

        foreach ($positions as $posData) {
            try {
                // Auto-detect and create symbol mapping if needed
                $this->ensureSymbolMapping($posData['symbol']);

                // Convert MT4 numeric type to string (0=buy, 1=sell)
                $type = $this->normalizePositionType($posData['type']);

                // Check if position already exists (before any updates)
                $existingPosition = Position::where('trading_account_id', $tradingAccount->id)
                    ->where('ticket', $posData['ticket'])
                    ->first();

                $updateData = [
                    'symbol' => $posData['symbol'],
                    'comment' => $posData['comment'] ?? null,
                    'external_id' => $posData['external_id'] ?? null,
                    'type' => $type,
                    'reason' => $posData['reason'] ?? 'unknown',
                    'volume' => $posData['volume'] ?? 0,
                    'open_price' => $posData['price_open'] ?? $posData['open_price'] ?? 0,
                    'current_price' => $posData['price_current'] ?? $posData['current_price'] ?? 0,
                    'sl' => $posData['sl'] ?? null,
                    'tp' => $posData['tp'] ?? null,
                    'profit' => $posData['profit'] ?? 0,
                    'swap' => $posData['swap'] ?? 0,
                    'commission' => $posData['commission'] ?? 0,
                    'update_time' => $this->parseDateTime($posData['update_time'] ?? now()),
                    'magic' => $posData['magic'] ?? 0,
                    'identifier' => $posData['identifier'] ?? $posData['ticket'],
                    'is_open' => true,
                    'platform_type' => $posData['platform_type'] ?? null,
                    'activity_type' => $posData['activity_type'] ?? null,
                ];

                // Only set open_time for NEW positions
                if (!$existingPosition) {
                    $updateData['open_time'] = $this->parseDateTime($posData['time'] ?? $posData['open_time'] ?? $posData['time_open'] ?? now());
                }

                Position::updateOrCreate(
                    [
                        'trading_account_id' => $tradingAccount->id,
                        'ticket' => $posData['ticket'],
                    ],
                    $updateData
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

            // Auto-detect and create symbol mapping if needed
            $this->ensureSymbolMapping($dealData['symbol']);

            // Check if deal already exists (avoid duplicates)
            $exists = Deal::where('trading_account_id', $tradingAccount->id)
                ->where('ticket', $dealData['ticket'])
                ->exists();

            if (!$exists) {
                try {
                    // Check if this ticket is in quarantine (previously failed)
                    $quarantineKey = "failed_deal:{$dealData['ticket']}";
                    if (Cache::has($quarantineKey)) {
                        $duplicatesSkipped++; // Count as skipped to avoid confusion
                        continue;
                    }
                    
                    // Normalize MT4 deal data to match validator expectations
                    $normalizedDeal = $this->normalizeMT4Deal($dealData);
                    
                    // Validate and normalize deal data
                    $validator = new TradingDataValidationService();
                    $validatedData = $validator->validateDeal($normalizedDeal);
                    
                    Deal::create(array_merge(
                        ['trading_account_id' => $tradingAccount->id],
                        $validatedData
                    ));

                    $newDealsCount++;
                } catch (\Exception $e) {
                    // Put this ticket in quarantine for 2 hours to suppress repeated failures
                    Cache::put($quarantineKey, true, 7200); // 2 hours = 7200 seconds
                    
                    Log::error('Failed to create deal', [
                        'ticket' => $dealData['ticket'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'raw_data' => $dealData, // Log the raw data to see what's missing
                        'account_id' => $tradingAccount->id,
                        'platform' => $tradingAccount->platform_type,
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
     * Normalize MT4 position type (numeric to string)
     */
    protected function normalizePositionType($type)
    {
        // MT4 sends numeric types: 0=buy, 1=sell
        $typeMap = [
            0 => 'buy',
            1 => 'sell',
            '0' => 'buy',
            '1' => 'sell',
        ];

        return $typeMap[$type] ?? (is_numeric($type) ? ($type == 0 ? 'buy' : 'sell') : $type);
    }

    /**
     * Normalize MT4 deal data to match validator expectations
     */
    protected function normalizeMT4Deal($dealData)
    {
        // MT4 sends time_open/time_close, validator expects 'time'
        if (isset($dealData['time_close']) && !isset($dealData['time'])) {
            $dealData['time'] = $this->normalizeDateTime($dealData['time_close']);
        } elseif (isset($dealData['time_open']) && !isset($dealData['time'])) {
            $dealData['time'] = $this->normalizeDateTime($dealData['time_open']);
        }

        // MT4 sends price_open/price_close, validator expects 'price'
        if (isset($dealData['price_close']) && !isset($dealData['price'])) {
            $dealData['price'] = $dealData['price_close'];
        } elseif (isset($dealData['price_open']) && !isset($dealData['price'])) {
            $dealData['price'] = $dealData['price_open'];
        }

        // MT4 sends activity_type instead of entry field
        // Map MT4 activity_type to MT5 entry type
        if (!isset($dealData['entry']) || empty($dealData['entry'])) {
            if (isset($dealData['activity_type'])) {
                $activityMap = [
                    'position_opened' => 'in',
                    'position_closed' => 'out',
                    'position_modified' => 'inout',
                ];
                $dealData['entry'] = $activityMap[$dealData['activity_type']] ?? 'unknown';
            }
        }

        // MT4 might send various type field names or numeric values
        // Map MT4 command types to standard deal types
        if (!isset($dealData['type']) || empty($dealData['type']) || is_numeric($dealData['type'])) {
            // First, if type exists and is numeric, map it directly
            if (isset($dealData['type']) && is_numeric($dealData['type'])) {
                // MT4 cmd values: 0=buy, 1=sell, 2=buy_limit, 3=sell_limit, 4=buy_stop, 5=sell_stop, 6=balance, 7=credit
                $cmdMap = [
                    0 => 'buy',
                    1 => 'sell',
                    2 => 'buy_limit',
                    3 => 'sell_limit',
                    4 => 'buy_stop',
                    5 => 'sell_stop',
                    6 => 'balance',
                    7 => 'credit',
                ];
                $dealData['type'] = $cmdMap[$dealData['type']] ?? 'unknown';
            } else {
                // Try different possible field names for type
                $typeFields = ['cmd', 'cmd_type', 'command', 'operation', 'deal_cmd', 'order_cmd', 'trade_type', 'deal_type', 'order_type'];
                $typeFound = false;
                
                foreach ($typeFields as $field) {
                    if (isset($dealData[$field]) && !empty($dealData[$field])) {
                        // Handle numeric cmd values
                        if (is_numeric($dealData[$field])) {
                            // MT4 cmd values: 0=buy, 1=sell, 2=buy_limit, 3=sell_limit, 4=buy_stop, 5=sell_stop, 6=balance, 7=credit
                            $cmdMap = [
                                0 => 'buy',
                                1 => 'sell',
                                2 => 'buy_limit',
                                3 => 'sell_limit',
                                4 => 'buy_stop',
                                5 => 'sell_stop',
                                6 => 'balance',
                                7 => 'credit',
                            ];
                            $dealData['type'] = $cmdMap[$dealData[$field]] ?? 'unknown';
                        } else {
                            // Handle string type values
                            $typeValue = strtoupper($dealData[$field]);
                            $stringMap = [
                                'OP_BUY' => 'buy',
                                'OP_SELL' => 'sell',
                                'OP_BUYLIMIT' => 'buy_limit',
                                'OP_SELLLIMIT' => 'sell_limit',
                                'OP_BUYSTOP' => 'buy_stop',
                                'OP_SELLSTOP' => 'sell_stop',
                                'OP_BALANCE' => 'balance',
                                'OP_CREDIT' => 'credit',
                            ];
                            $dealData['type'] = $stringMap[$typeValue] ?? strtolower($dealData[$field]);
                        }
                        $typeFound = true;
                        break;
                    }
                }
                
                // Final fallback if no type found
                if (!$typeFound) {
                    $dealData['type'] = 'unknown';
                    Log::warning('Deal type not found, set to unknown', [
                        'ticket' => $dealData['ticket'] ?? 'unknown',
                        'available_fields' => array_keys($dealData)
                    ]);
                }
            }
        }

        return $dealData;
    }

    /**
     * Normalize MT4 datetime format (dots to dashes)
     */
    protected function normalizeDateTime($dateTime)
    {
        if (empty($dateTime)) {
            return null;
        }

        // MT4 uses dots: "2025.11.19 12:30:29"
        // Convert to standard: "2025-11-19 12:30:29"
        return str_replace('.', '-', $dateTime);
    }

    /**
     * Ensure symbol mapping exists, create if missing
     */
    protected function ensureSymbolMapping($rawSymbol)
    {
        if (empty($rawSymbol)) {
            return;
        }

        // Check if mapping already exists
        $exists = DB::table('symbol_mappings')
            ->where('raw_symbol', $rawSymbol)
            ->exists();

        if (!$exists) {
            // Extract broker suffix (e.g., .sd, .lv, etc.)
            $brokerSuffix = null;
            $normalizedSymbol = $rawSymbol;

            if (preg_match('/^([A-Z0-9]+)(\.[a-z]+)$/i', $rawSymbol, $matches)) {
                $normalizedSymbol = $matches[1];
                $brokerSuffix = $matches[2];
            }

            // Create new symbol mapping
            DB::table('symbol_mappings')->insert([
                'raw_symbol' => $rawSymbol,
                'normalized_symbol' => $normalizedSymbol,
                'broker_suffix' => $brokerSuffix,
                'is_verified' => false, // Auto-detected symbols need manual verification
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Auto-created symbol mapping', [
                'raw_symbol' => $rawSymbol,
                'normalized_symbol' => $normalizedSymbol,
                'broker_suffix' => $brokerSuffix,
            ]);
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
