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
use App\Models\Deal;
use App\Models\HistoryUploadProgress;
use Carbon\Carbon;

class ProcessHistoricalData implements ShouldQueue
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
            $historyDate = $this->data['meta']['history_date'] ?? null;
            $dayNumber = $this->data['meta']['history_day_number'] ?? 0;

            Log::info('Processing historical trading data', [
                'user_id' => $userId,
                'history_date' => $historyDate,
                'day_number' => $dayNumber,
                'file' => $this->filename
            ]);

            // Find or create trading account
            $tradingAccount = $this->getTradingAccount($userId);

            if (!$tradingAccount) {
                throw new \Exception('Trading account not found or could not be created');
            }

            // Process historical deals for this day
            if (isset($this->data['history']) && is_array($this->data['history'])) {
                $this->processHistoricalDeals($tradingAccount, $this->data['history'], $historyDate);
            }

            // Update progress tracking
            if ($historyDate) {
                $this->updateProgress($tradingAccount, $historyDate, $dayNumber);
            }

            DB::commit();

            // Clear user-specific caches after successful processing
            $this->clearUserCache($userId, $tradingAccount->id);

            Log::info('Successfully processed historical data', [
                'trading_account_id' => $tradingAccount->id,
                'history_date' => $historyDate,
                'day_number' => $dayNumber,
                'deals_count' => count($this->data['history'] ?? [])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process historical data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $this->filename
            ]);

            throw $e;
        }
    }

    /**
     * Get or create trading account
     */
    protected function getTradingAccount($userId)
    {
        $accountData = $this->data['account'];
        
        $accountNumber = $accountData['account_number'] ?? null;
        $accountHash = $accountData['account_hash'] ?? null;
        $brokerServer = $accountData['server'];

        // Try to find existing account first
        $tradingAccount = TradingAccount::where('user_id', $userId)
            ->where('broker_server', $brokerServer)
            ->where(function($query) use ($accountNumber, $accountHash) {
                if ($accountNumber) {
                    $query->where('account_number', $accountNumber);
                }
                if ($accountHash) {
                    $query->orWhere('account_hash', $accountHash);
                }
            })
            ->first();

        // If not found, create it
        if (!$tradingAccount) {
            // Extract broker name from server if not provided
            $brokerName = $accountData['broker'] ?? $accountData['broker_name'] ?? null;
            if (!$brokerName && $brokerServer) {
                // Try to extract from server name (e.g., "ICMarkets-Live" -> "ICMarkets")
                $brokerName = explode('-', $brokerServer)[0];
            }
            
            $tradingAccount = TradingAccount::firstOrCreate(
                [
                    'user_id' => $userId,
                    'broker_server' => $brokerServer,
                    'account_number' => $accountNumber,
                    'account_hash' => $accountHash,
                ],
                [
                    'account_uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'broker_name' => $brokerName ?: 'Unknown Broker',
                    'account_name' => $accountData['name'] ?? $accountData['account_name'] ?? 'Trading Account',
                    'account_currency' => $accountData['currency'] ?? $accountData['account_currency'] ?? 'USD',
                    'leverage' => $accountData['leverage'] ?? 100,
                ]
            );
        }

        return $tradingAccount;
    }

    /**
     * Process historical deals
     */
    protected function processHistoricalDeals($tradingAccount, $deals, $historyDate)
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
                    Deal::create([
                    'trading_account_id' => $tradingAccount->id,
                    'ticket' => $dealData['ticket'],
                    'order_id' => $dealData['order'] ?? null,
                    'position_id' => $dealData['position_id'] ?? null,
                    'symbol' => $dealData['symbol'],
                    'comment' => $dealData['comment'] ?? null,
                    'external_id' => $dealData['external_id'] ?? null,
                    'type' => $dealData['type'],
                    'entry' => $dealData['entry'] ?? 'unknown',
                    'reason' => $dealData['reason'] ?? 'unknown',
                    'volume' => $dealData['volume'],
                    'price' => $dealData['price'],
                    'profit' => $dealData['profit'],
                    'swap' => $dealData['swap'] ?? 0,
                    'commission' => $dealData['commission'] ?? 0,
                    'fee' => $dealData['fee'] ?? 0,
                    'time' => $this->parseDateTime($dealData['time']),
                    'time_msc' => $dealData['time_msc'] ?? null,
                    'magic' => $dealData['magic'] ?? 0,
                ]);

                    $newDealsCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to create historical deal', [
                        'ticket' => $dealData['ticket'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $duplicatesSkipped++;
            }
        }

        Log::info('Processed historical deals', [
            'account_id' => $tradingAccount->id,
            'history_date' => $historyDate,
            'new_deals' => $newDealsCount,
            'duplicates_skipped' => $duplicatesSkipped,
            'non_trading_skipped' => $nonTradingSkipped,
            'total_in_batch' => count($deals)
        ]);
    }

    /**
     * Update history upload progress
     */
    protected function updateProgress($tradingAccount, $historyDate, $dayNumber)
    {
        $progress = HistoryUploadProgress::firstOrCreate(
            ['trading_account_id' => $tradingAccount->id],
            ['started_at' => now()]
        );

        // Convert date format from 2025.09.20 to 2025-09-20
        $formattedDate = str_replace('.', '-', $historyDate);

        $progress->update([
            'last_day_uploaded' => Carbon::parse($formattedDate),
            'days_processed' => $dayNumber,
        ]);

        Log::info('Updated history upload progress', [
            'trading_account_id' => $tradingAccount->id,
            'last_day_uploaded' => $historyDate,
            'days_processed' => $dayNumber
        ]);
    }

    /**
     * Parse datetime string
     */
    protected function parseDateTime($dateTimeString)
    {
        if (empty($dateTimeString) || $dateTimeString === '1970-01-01 00:00:00') {
            return null;
        }

        try {
            return Carbon::parse($dateTimeString);
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
            
            // Clear performance caches
            $accountIds = TradingAccount::where('user_id', $userId)->pluck('id')->toArray();
            $perfKey = 'performance.' . md5(implode(',', $accountIds)) . ".*";
            Cache::forget($perfKey);
            
            // Clear broker analytics (global cache)
            Cache::forget("broker_analytics_*");
            
            Log::debug('Cache cleared for user (historical)', ['user_id' => $userId, 'account_id' => $accountId]);
        } catch (\Exception $e) {
            // Don't fail the job if cache clearing fails
            Log::warning('Failed to clear cache', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
