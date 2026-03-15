<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessTradingData;
use App\Jobs\ProcessHistoricalData;
use App\Models\TradingAccount;
use App\Models\EnterpriseBroker;
use App\Models\WhitelistedBrokerUsage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

            // Validate basic structure early
            if (!is_array($data['meta'] ?? null) || !is_array($data['account'] ?? null)) {
                Log::warning('Data collect rejected: missing required meta/account structure', [
                    'user_id' => $user->id,
                    'ip' => $clientIp,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Invalid data structure',
                    'message' => 'Required fields: meta (object), account (object)',
                ], 400);
            }

            $accountInfo = $data['account'];
            $metaInfo = $data['meta'];

            // Normalize required account fields
            $accountNumber = $accountInfo['account_number'] ?? null;
            $accountHash = $accountInfo['account_hash'] ?? null;
            $brokerName = $accountInfo['broker'] ?? null;
            $serverName = $accountInfo['server'] ?? null;

            if (!$accountNumber && !$accountHash) {
                Log::warning('Data collect rejected: missing account identifiers', [
                    'user_id' => $user->id,
                    'ip' => $clientIp,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Invalid account payload',
                    'message' => 'Account number or account hash is required',
                ], 400);
            }

            if (!$brokerName) {
                Log::warning('Data collect rejected: missing broker name', [
                    'user_id' => $user->id,
                    'ip' => $clientIp,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Invalid account payload',
                    'message' => 'Account broker field is required',
                ], 400);
            }

            // Add metadata
            $data['received_at'] = now()->toIso8601String();
            $data['client_ip'] = $clientIp;
            $data['user_id'] = $user->id;

            // REJECT DEMO ACCOUNTS IMMEDIATELY
            $accountTypeCode = $accountInfo['trade_mode'] ?? $accountInfo['account_type'] ?? 0;
            $accountType = $this->getAccountType($accountTypeCode);

            if ($accountType === 'demo' || $accountType === 'contest') {
                Log::warning('Demo/Contest account rejected at API level', [
                    'account' => $accountNumber ?? $accountHash ?? 'unknown',
                    'broker' => $brokerName,
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

            $accountHash = $accountHash ?? hash('sha256', ($accountNumber ?? '') . ($serverName ?? ''));

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

            // CHECK: Check if broker is whitelisted (enterprise)
            $brokerName = $brokerName ?? 'unknown';
            $whitelistedBroker = EnterpriseBroker::where('official_broker_name', $brokerName)->first();
            
            $bypassLimits = false;
            $gracePeriodMessage = null;
            
            if ($whitelistedBroker) {
                if ($whitelistedBroker->is_active) {
                    // Active subscription - bypass limits
                    $bypassLimits = true;
                    Log::info('Whitelisted broker detected - bypassing account limits', [
                        'user_id' => $user->id,
                        'broker' => $brokerName,
                        'enterprise_broker_id' => $whitelistedBroker->id,
                    ]);
                } elseif ($whitelistedBroker->grace_period_ends_at && $whitelistedBroker->grace_period_ends_at > now()) {
                    // In grace period - still works but with warning
                    $bypassLimits = true;
                    $gracePeriodMessage = "Broker's enterprise plan expired. Grace period ends: " . 
                                         $whitelistedBroker->grace_period_ends_at->format('Y-m-d');
                    Log::warning('Whitelisted broker in grace period', [
                        'user_id' => $user->id,
                        'broker' => $brokerName,
                        'grace_period_ends' => $whitelistedBroker->grace_period_ends_at,
                    ]);
                }
            }

            // REMOVED: Account limit check - all users now have unlimited accounts
            // Enterprise brokers get 180-day view, standard brokers get 7-day view

            // Check if this is historical data or current data
            $isHistorical = $data['meta']['is_historical'] ?? false;
            
            // Find trading account for gap detection (needed before trackWhitelistedUsage)
            $accountHash = $accountInfo['account_hash'] ?? hash('sha256', ($accountInfo['account_number'] ?? '') . ($accountInfo['server'] ?? ''));
            $tradingAccount = TradingAccount::where('user_id', $user->id)
                ->where('account_hash', $accountHash)
                ->first();
            
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

            $warnings = [];

            // Track whitelisted broker usage (for broker analytics)
            if ($whitelistedBroker && $bypassLimits) {
                $usageResult = $this->trackWhitelistedUsage($user, $whitelistedBroker, $accountInfo, $tradingAccount);

                if ($usageResult !== true) {
                    $warnings[] = [
                        'type' => 'whitelisted_usage',
                        'message' => 'Failed to record whitelisted broker usage. Analytics may be incomplete.',
                    ];
                }
            }

            // Determine max days view based on broker status
            $maxDaysView = $bypassLimits ? 180 : 7;
            
            // Check for missing data gaps (only for current data, not historical uploads)
            $missingDataInfo = null;
            if (!$isHistorical && $tradingAccount) {
                $missingDataCheck = $this->checkForMissingData($tradingAccount);

                if (isset($missingDataCheck['error'])) {
                    $warnings[] = [
                        'type' => 'missing_data_detection',
                        'message' => $missingDataCheck['error'],
                    ];
                } else {
                    $missingDataInfo = $missingDataCheck;
                }
            }
            
            $response = [
                'success' => true,
                'message' => 'Data received successfully',
                'data_type' => $isHistorical ? 'historical' : 'current',
                'timestamp' => now()->toIso8601String(),
                'queued' => true,
                'whitelisted_broker' => $bypassLimits,
                'max_days_view' => $maxDaysView,
                'data_retention_days' => 180, // All data retained for 180 days
                'missing_data' => $missingDataInfo !== null,
            ];

            // Add missing data details if gaps were found
            if ($missingDataInfo) {
                $response['missing_data_range'] = [
                    'start_time' => $missingDataInfo['start_time'],
                    'end_time' => $missingDataInfo['end_time'],
                    'estimated_days' => $missingDataInfo['estimated_days'],
                    'severity' => $missingDataInfo['severity'],
                ];
            }

            if ($gracePeriodMessage) {
                $response['grace_period_warning'] = $gracePeriodMessage;
            }

            if (!empty($warnings)) {
                $response['warnings'] = $warnings;
            }

            return response()->json($response, 200);

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

    /**
     * Track whitelisted broker usage for analytics
     */
    private function trackWhitelistedUsage($user, $whitelistedBroker, $accountInfo, $tradingAccount)
    {
        if (!$tradingAccount) {
            return true; // Nothing to track yet
        }

        try {
            WhitelistedBrokerUsage::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'trading_account_id' => $tradingAccount->id,
                ],
                [
                    'enterprise_broker_id' => $whitelistedBroker->id,
                    'account_number' => $accountInfo['account_number'] ?? 0,
                    'last_seen_at' => now(),
                    'first_seen_at' => DB::raw('COALESCE(first_seen_at, NOW())'),
                ]
            );

            return true;
        } catch (\Throwable $e) {
            report($e);

            Log::warning('Failed to track whitelisted broker usage', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'broker_id' => $whitelistedBroker->id,
                'account_id' => $tradingAccount->id,
            ]);

            return false;
        }
    }

    /**
     * Check for missing data gaps in the last 24 hours
     */
    private function checkForMissingData($tradingAccount)
    {
        try {
            // Check for gaps in the last 24 hours
            $endTime = now();
            $startTime = now()->subHours(24);
            
            // Get expected snapshots (every 5 minutes = 288 per day)
            $expectedSnapshots = 288; // 24 hours * 12 snapshots per hour
            
            // Count actual snapshots in the last 24 hours
            $actualSnapshots = \App\Models\AccountSnapshot::where('trading_account_id', $tradingAccount->id)
                ->whereBetween('snapshot_time', [$startTime, $endTime])
                ->count();
            
            // If we have less than 80% of expected data, consider it a gap
            $threshold = $expectedSnapshots * 0.8;
            
            if ($actualSnapshots < $threshold) {
                // Find the actual gap range
                $latestSnapshot = \App\Models\AccountSnapshot::where('trading_account_id', $tradingAccount->id)
                    ->where('snapshot_time', '<=', $endTime)
                    ->orderBy('snapshot_time', 'desc')
                    ->first();
                
                $gapStartTime = $latestSnapshot ? $latestSnapshot->snapshot_time : $startTime;
                $missingHours = $endTime->diffInHours($gapStartTime);
                $estimatedDays = ceil($missingHours / 24);
                
                // Determine severity based on how much data is missing
                $missingPercentage = (($expectedSnapshots - $actualSnapshots) / $expectedSnapshots) * 100;
                $severity = 'normal';
                if ($missingPercentage > 50) {
                    $severity = 'critical';
                } elseif ($missingPercentage > 25) {
                    $severity = 'high';
                }
                
                Log::info('Missing data detected for EA backfill trigger', [
                    'trading_account_id' => $tradingAccount->id,
                    'account_number' => $tradingAccount->account_number ?? $tradingAccount->account_hash,
                    'gap_start' => $gapStartTime->toIso8601String(),
                    'gap_end' => $endTime->toIso8601String(),
                    'missing_hours' => $missingHours,
                    'missing_percentage' => round($missingPercentage, 1),
                    'severity' => $severity,
                ]);
                
                return [
                    'start_time' => $gapStartTime->toIso8601String(),
                    'end_time' => $endTime->toIso8601String(),
                    'estimated_days' => $estimatedDays,
                    'severity' => $severity,
                ];
            }
            
            return null; // No gaps detected
            
        } catch (\Throwable $e) {
            report($e);

            Log::warning('Failed to check for missing data', [
                'trading_account_id' => $tradingAccount->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'error' => 'Unable to determine data gaps at this time.',
            ];
        }
    }
}
