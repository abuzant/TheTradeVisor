<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TradingDataValidationService
{
    /**
     * Validate and normalize deal data
     * Ensures all critical fields are present and valid
     */
    public function validateDeal(array $dealData): array
    {
        $validated = [];
        $warnings = [];

        // Required fields
        if (empty($dealData['ticket'])) {
            throw new \Exception('Deal ticket is required');
        }
        $validated['ticket'] = $dealData['ticket'];

        if (empty($dealData['symbol'])) {
            throw new \Exception('Deal symbol is required');
        }
        $validated['symbol'] = trim($dealData['symbol']);

        // Validate and normalize timestamp - CRITICAL
        // Handle MT4 time_open/time_close fields
        $timeData = $dealData;
        if (!isset($dealData['time']) && isset($dealData['time_close'])) {
            $timeData['time'] = $dealData['time_close'];
        } elseif (!isset($dealData['time']) && isset($dealData['time_open'])) {
            $timeData['time'] = $dealData['time_open'];
        }
        $validated['time'] = $this->validateTimestamp($timeData, 'deal', $warnings);
        $validated['time_msc'] = $dealData['time_msc'] ?? null;

        // Type validation - NEVER throw exception for type, always normalize
        if (empty($dealData['type'])) {
            $warnings[] = 'Deal type was empty, set to unknown';
            $dealData['type'] = 'unknown';
        }
        $validated['type'] = $this->normalizeType($dealData['type']);

        // Entry validation
        $validated['entry'] = $this->normalizeEntry($dealData['entry'] ?? 'unknown');

        // Numeric fields with validation
        $validated['volume'] = $this->validateNumeric($dealData['volume'] ?? 0, 'volume', 0);
        $validated['price'] = $this->validateNumeric($dealData['price'] ?? 0, 'price', 0);
        $validated['profit'] = $this->validateNumeric($dealData['profit'] ?? 0, 'profit');
        $validated['swap'] = $this->validateNumeric($dealData['swap'] ?? 0, 'swap');
        $validated['commission'] = $this->validateNumeric($dealData['commission'] ?? 0, 'commission');
        $validated['fee'] = $this->validateNumeric($dealData['fee'] ?? 0, 'fee');

        // Optional fields
        $validated['order_id'] = $dealData['order'] ?? $dealData['order_id'] ?? null;
        $validated['position_id'] = $dealData['position_id'] ?? null;
        $validated['comment'] = $dealData['comment'] ?? null;
        $validated['external_id'] = $dealData['external_id'] ?? null;
        $validated['reason'] = $dealData['reason'] ?? 'unknown';
        $validated['magic'] = (int)($dealData['magic'] ?? 0);
        $validated['platform_type'] = $dealData['platform_type'] ?? null;
        $validated['activity_type'] = $dealData['activity_type'] ?? null;

        // Log warnings if any
        if (!empty($warnings)) {
            Log::warning('Deal data validation warnings', [
                'ticket' => $validated['ticket'],
                'warnings' => $warnings
            ]);
        }

        return $validated;
    }

    /**
     * Validate and normalize position data
     */
    public function validatePosition(array $posData): array
    {
        $validated = [];
        $warnings = [];

        if (empty($posData['ticket'])) {
            throw new \Exception('Position ticket is required');
        }
        $validated['ticket'] = $posData['ticket'];

        if (empty($posData['symbol'])) {
            throw new \Exception('Position symbol is required');
        }
        $validated['symbol'] = trim($posData['symbol']);

        // Validate timestamps
        $validated['open_time'] = $this->validateTimestamp(
            ['time' => $posData['time'] ?? $posData['open_time'] ?? null, 'time_msc' => $posData['time_msc'] ?? null],
            'position_open',
            $warnings
        );
        
        $validated['update_time'] = $this->validateTimestamp(
            ['time' => $posData['update_time'] ?? null, 'time_msc' => null],
            'position_update',
            $warnings,
            now() // Default to now for update time
        );

        // Type and other fields
        $validated['type'] = $this->normalizeType($posData['type'] ?? 'buy');
        $validated['volume'] = $this->validateNumeric($posData['volume'] ?? 0, 'volume', 0);
        $validated['open_price'] = $this->validateNumeric($posData['price_open'] ?? $posData['open_price'] ?? 0, 'open_price', 0);
        $validated['current_price'] = $this->validateNumeric($posData['price_current'] ?? $posData['current_price'] ?? 0, 'current_price', 0);
        $validated['profit'] = $this->validateNumeric($posData['profit'] ?? 0, 'profit');
        $validated['swap'] = $this->validateNumeric($posData['swap'] ?? 0, 'swap');
        $validated['commission'] = $this->validateNumeric($posData['commission'] ?? 0, 'commission');

        // Optional fields
        $validated['sl'] = $posData['sl'] ?? null;
        $validated['tp'] = $posData['tp'] ?? null;
        $validated['comment'] = $posData['comment'] ?? null;
        $validated['external_id'] = $posData['external_id'] ?? null;
        $validated['reason'] = $posData['reason'] ?? 'unknown';
        $validated['magic'] = (int)($posData['magic'] ?? 0);
        $validated['identifier'] = $posData['identifier'] ?? $posData['ticket'];
        $validated['platform_type'] = $posData['platform_type'] ?? null;
        $validated['activity_type'] = $posData['activity_type'] ?? null;

        if (!empty($warnings)) {
            Log::warning('Position data validation warnings', [
                'ticket' => $validated['ticket'],
                'warnings' => $warnings
            ]);
        }

        return $validated;
    }

    /**
     * Validate timestamp - handles time, time_msc, and provides fallback
     * This is the CRITICAL function that prevents NULL timestamps
     */
    private function validateTimestamp(array $data, string $context, array &$warnings, $fallback = null): Carbon
    {
        // Try to parse 'time' field first
        if (!empty($data['time'])) {
            try {
                $parsed = Carbon::parse($data['time']);
                if ($parsed->year >= 2000 && $parsed->year <= 2100) {
                    return $parsed;
                }
                $warnings[] = "Invalid year in time field for {$context}: {$parsed->year}";
            } catch (\Exception $e) {
                $warnings[] = "Failed to parse time field for {$context}: {$e->getMessage()}";
            }
        }

        // Try to convert time_msc if available
        if (!empty($data['time_msc']) && $data['time_msc'] > 0) {
            try {
                $parsed = Carbon::createFromTimestampMs($data['time_msc']);
                if ($parsed->year >= 2000 && $parsed->year <= 2100) {
                    $warnings[] = "Used time_msc for {$context} (time field was invalid/missing)";
                    return $parsed;
                }
                $warnings[] = "Invalid year in time_msc for {$context}: {$parsed->year}";
            } catch (\Exception $e) {
                $warnings[] = "Failed to parse time_msc for {$context}: {$e->getMessage()}";
            }
        }

        // Use fallback or throw exception
        if ($fallback !== null) {
            $warnings[] = "No valid timestamp for {$context}, using fallback";
            return $fallback instanceof Carbon ? $fallback : Carbon::parse($fallback);
        }

        throw new \Exception("No valid timestamp available for {$context}");
    }

    /**
     * Validate numeric field
     */
    private function validateNumeric($value, string $fieldName, $min = null): float
    {
        $numeric = (float)$value;
        
        if ($min !== null && $numeric < $min) {
            throw new \Exception("{$fieldName} cannot be less than {$min}");
        }

        return $numeric;
    }

    /**
     * Normalize deal/position type - handles ALL possible inputs
     */
    private function normalizeType($type): string
    {
        // Handle numeric types directly (MT4 sends integers)
        if (is_numeric($type)) {
            $typeMap = [
                0 => 'buy',
                1 => 'sell',
                2 => 'buy_limit',
                3 => 'sell_limit',
                4 => 'buy_stop',
                5 => 'sell_stop',
                6 => 'balance',
                7 => 'credit',
            ];
            return $typeMap[(int)$type] ?? 'unknown';
        }
        
        // Handle string types
        $type = strtolower(trim($type));
        
        // Map string numeric types
        $stringTypeMap = [
            '0' => 'buy',
            '1' => 'sell',
            '2' => 'buy_limit',
            '3' => 'sell_limit',
            '4' => 'buy_stop',
            '5' => 'sell_stop',
            '6' => 'balance',
            '7' => 'credit',
        ];
        
        if (isset($stringTypeMap[$type])) {
            return $stringTypeMap[$type];
        }
        
        // Map MT4 constants
        $constantMap = [
            'op_buy' => 'buy',
            'op_sell' => 'sell',
            'op_buylimit' => 'buy_limit',
            'op_selllimit' => 'sell_limit',
            'op_buystop' => 'buy_stop',
            'op_sellstop' => 'sell_stop',
            'op_balance' => 'balance',
            'op_credit' => 'credit',
        ];
        
        if (isset($constantMap[$type])) {
            return $constantMap[$type];
        }
        
        // Return valid types or unknown
        $validTypes = ['buy', 'sell', 'buy_limit', 'sell_limit', 'buy_stop', 'sell_stop', 'balance', 'credit'];
        return in_array($type, $validTypes) ? $type : 'unknown';
    }

    /**
     * Normalize entry type
     */
    private function normalizeEntry(string $entry): string
    {
        $entry = strtolower(trim($entry));
        
        $validEntries = ['in', 'out', 'inout', 'out_by'];
        
        return in_array($entry, $validEntries) ? $entry : 'unknown';
    }
}
