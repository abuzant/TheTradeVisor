<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessTradingData;
use App\Jobs\ProcessHistoricalData;
use App\Models\TradingAccount;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DataCollectionController extends Controller
{
    /**
     * Receive trading data from MT5 EA
     */
    public function collect(Request $request)
    {
        try {
            // Get authenticated user
            $user = $request->get('authenticated_user');

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'error' => 'Account suspended',
                    'message' => 'Your account has been suspended. Please contact support.'
                ], 403);
            }

            // Get client IP
            $clientIp = $request->ip();

            // Get raw JSON data
            $data = $request->all();

            // Add metadata
            $data['received_at'] = now()->toIso8601String();
            $data['client_ip'] = $clientIp;
            $data['user_id'] = $user->id;

            // Validate basic structure
            if (!isset($data['meta']) || !isset($data['account'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid data structure',
                    'message' => 'Required fields: meta, account'
                ], 400);
            }

            // REJECT DEMO ACCOUNTS IMMEDIATELY
            $accountTypeCode = $data['account']['trade_mode'] ?? $data['account']['account_type'] ?? 0;
            $accountType = $this->getAccountType($accountTypeCode);

            if ($accountType === 'demo' || $accountType === 'contest') {
                Log::warning('Demo/Contest account rejected at API level', [
                    'account' => $data['account']['account_number'] ?? $data['account']['account_hash'] ?? 'unknown',
                    'broker' => $data['account']['broker'] ?? 'unknown',
                    'type' => $accountType,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Demo account not allowed',
                    'message' => 'Demo and contest accounts are not supported. Please connect a real trading account to use TheTradeVisor.',
                    'account_type' => $accountType
                ], 403);
            }

            // Extract account info
            $accountInfo = $data['account'];
            $accountHash = $accountInfo['account_hash'] ?? hash('sha256', ($accountInfo['account_number'] ?? '') . ($accountInfo['server'] ?? ''));

            // CHECK: Check if this specific account is paused
            $existingAccount = TradingAccount::where('user_id', $user->id)
                ->where('account_hash', $accountHash)
                ->first();

            if ($existingAccount && $existingAccount->is_paused) {
                return response()->json([
                    'success' => false,
                    'error' => 'Account paused',
                    'message' => 'This trading account has been paused. ' . ($existingAccount->pause_reason ?? 'No reason provided.'),
                    'paused_at' => $existingAccount->paused_at,
                ], 403);
            }

            // CHECK: Enforce account limit for new accounts
            if (!$existingAccount) {
                $currentAccountCount = $user->tradingAccounts()->count();
                
                if ($currentAccountCount >= $user->max_accounts) {
                    Log::warning('Account limit exceeded - new account rejected', [
                        'user_id' => $user->id,
                        'current_accounts' => $currentAccountCount,
                        'max_accounts' => $user->max_accounts,
                        'subscription_tier' => $user->subscription_tier,
                        'attempted_account' => $accountNum,
                        'broker' => $broker ?? 'unknown',
                    ]);

                    return response()->json([
                        'success' => false,
                        'error' => 'ACCOUNT_LIMIT_EXCEEDED',
                        'message' => "Account limit reached. You have {$currentAccountCount} account(s) but your {$user->subscription_tier} plan allows {$user->max_accounts}. Please upgrade at https://thetradevisor.com/pricing to add more accounts.",
                        'current_accounts' => $currentAccountCount,
                        'max_accounts' => $user->max_accounts,
                        'subscription_tier' => $user->subscription_tier,
                        'upgrade_url' => 'https://thetradevisor.com/pricing'
                    ], 403);
                }
            }

            // Check if this is historical data or current data
            $isHistorical = $data['meta']['is_historical'] ?? false;
            
            // Generate unique filename for raw data backup
            $timestamp = now()->format('Y-m-d_His');
            $broker = $accountInfo['broker'] ?? 'unknown';
            $accountNum = $accountInfo['account_number'] ?? $accountInfo['account_hash'] ?? 'unknown';

            $dataType = $isHistorical ? 'historical' : 'current';
            $historyDate = $isHistorical ? ($data['meta']['history_date'] ?? 'unknown') : '';
            
            $filename = sprintf(
                '%s/%s/%s/%s_%s_%s%s.json',
                $user->id,
                date('Y-m'),
                $dataType,
                $timestamp,
                $broker,
                $accountNum,
                $isHistorical ? '_' . $historyDate : ''
            );

            // Store raw JSON to storage
            Storage::disk('trading_data')->put($filename, json_encode($data, JSON_PRETTY_PRINT));

            // Dispatch appropriate job based on data type
            if ($isHistorical) {
                ProcessHistoricalData::dispatch($data, $filename);
                
                Log::info('Historical data received from MT5 EA', [
                    'user_id' => $user->id,
                    'broker' => $broker,
                    'account' => $accountNum,
                    'history_date' => $data['meta']['history_date'] ?? 'unknown',
                    'history_day_number' => $data['meta']['history_day_number'] ?? 0,
                    'ip' => $clientIp,
                ]);
            } else {
                ProcessTradingData::dispatch($data, $filename);
                
                Log::info('Current data received from MT5 EA', [
                    'user_id' => $user->id,
                    'broker' => $broker,
                    'account' => $accountNum,
                    'ip' => $clientIp,
                    'is_first_run' => $data['meta']['is_first_run'] ?? false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data received successfully',
                'data_type' => $isHistorical ? 'historical' : 'current',
                'timestamp' => now()->toIso8601String(),
                'queued' => true,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error receiving MT5 data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's a demo account rejection
            if (str_contains($e->getMessage(), 'Demo and contest accounts are not allowed')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demo account not allowed',
                    'message' => 'Demo and contest accounts are not supported. Please connect a real trading account to use TheTradeVisor.'
                ], 403);
            }

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to process data. Please try again.'
            ], 500);
        }
    }

    /**
     * Get account type from code
     */
    private function getAccountType($code)
    {
        switch ($code) {
            case 0:
                return 'real';
            case 1:
                return 'demo';
            case 2:
                return 'contest';
            default:
                return 'unknown';
        }
    }
}
