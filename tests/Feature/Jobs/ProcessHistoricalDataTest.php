<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessHistoricalData;
use App\Models\User;
use App\Models\TradingAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessHistoricalDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that trading account is created if it doesn't exist
     */
    public function test_creates_trading_account_if_not_exists(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'account' => [
                'account_number' => '12345678',
                'account_hash' => 'test_hash',
                'server' => 'ICMarkets-Live',
                'broker' => 'IC Markets',
                'name' => 'Test Account',
                'currency' => 'USD',
                'leverage' => 500,
            ],
            'history' => [],
            'meta' => [
                'history_date' => '2025-09-20',
                'history_day_number' => 1,
            ],
        ];

        // Ensure no trading account exists
        $this->assertDatabaseMissing('trading_accounts', [
            'user_id' => $user->id,
            'account_number' => '12345678',
        ]);

        // Process the job
        $job = new ProcessHistoricalData($data, 'test_file.json');
        $job->handle();

        // Assert trading account was created
        $this->assertDatabaseHas('trading_accounts', [
            'user_id' => $user->id,
            'account_number' => '12345678',
            'broker_server' => 'ICMarkets-Live',
            'broker_name' => 'IC Markets',
        ]);
    }

    /**
     * Test that broker name is extracted from server if not provided
     */
    public function test_extracts_broker_name_from_server(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'account' => [
                'account_number' => '87654321',
                'account_hash' => 'test_hash_2',
                'server' => 'EquitiSecurities-Live',
                // No broker field provided
                'name' => 'Test Account 2',
                'currency' => 'USD',
                'leverage' => 100,
            ],
            'history' => [],
            'meta' => [
                'history_date' => '2025-09-20',
                'history_day_number' => 1,
            ],
        ];

        $job = new ProcessHistoricalData($data, 'test_file2.json');
        $job->handle();

        // Assert broker name was extracted from server string
        $this->assertDatabaseHas('trading_accounts', [
            'user_id' => $user->id,
            'account_number' => '87654321',
            'broker_name' => 'EquitiSecurities', // Extracted from "EquitiSecurities-Live"
        ]);
    }

    /**
     * Test that date format with dots is converted correctly
     */
    public function test_handles_date_format_with_dots(): void
    {
        $user = User::factory()->create();
        
        // Create trading account first
        $account = TradingAccount::create([
            'user_id' => $user->id,
            'account_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'account_number' => '99999999',
            'broker_name' => 'Test Broker',
            'broker_server' => 'TestBroker-Live',
            'account_currency' => 'USD',
            'leverage' => 100,
        ]);

        $data = [
            'user_id' => $user->id,
            'account' => [
                'account_number' => '99999999',
                'account_hash' => 'test_hash_3',
                'server' => 'TestBroker-Live',
                'broker' => 'Test Broker',
            ],
            'history' => [],
            'meta' => [
                'history_date' => '2025.09.20', // Date with dots instead of dashes
                'history_day_number' => 1,
            ],
        ];

        $job = new ProcessHistoricalData($data, 'test_file3.json');
        
        // Should not throw exception
        $job->handle();

        // Assert progress was updated with correctly parsed date
        $this->assertDatabaseHas('history_upload_progress', [
            'trading_account_id' => $account->id,
            'days_processed' => 1,
        ]);
    }

    /**
     * Test that job handles missing broker name gracefully
     */
    public function test_handles_missing_broker_name(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'account' => [
                'account_number' => '11111111',
                'server' => 'UnknownBroker-Live',
                // No broker or broker_name field
            ],
            'history' => [],
            'meta' => [
                'history_date' => '2025-09-20',
                'history_day_number' => 1,
            ],
        ];

        $job = new ProcessHistoricalData($data, 'test_file4.json');
        $job->handle();

        // Assert account was created with extracted broker name
        $this->assertDatabaseHas('trading_accounts', [
            'user_id' => $user->id,
            'account_number' => '11111111',
            'broker_name' => 'UnknownBroker',
        ]);
    }

    /**
     * Test that duplicate deals are skipped
     */
    public function test_skips_duplicate_deals(): void
    {
        $user = User::factory()->create();
        
        $account = TradingAccount::create([
            'user_id' => $user->id,
            'account_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'account_number' => '22222222',
            'broker_name' => 'Test Broker',
            'broker_server' => 'TestBroker-Live',
            'account_currency' => 'USD',
            'leverage' => 100,
        ]);

        $dealData = [
            'ticket' => 123456,
            'symbol' => 'EURUSD',
            'type' => 'buy',
            'entry' => 'in',
            'reason' => 'expert',
            'volume' => 0.01,
            'price' => 1.1050,
            'profit' => 5.00,
            'commission' => 0.50,
            'swap' => 0.00,
            'fee' => 0.00,
            'time' => '2025-09-20 11:00:00',
        ];

        $data = [
            'user_id' => $user->id,
            'account' => [
                'account_number' => '22222222',
                'server' => 'TestBroker-Live',
                'broker' => 'Test Broker',
            ],
            'history' => [$dealData],
            'meta' => [
                'history_date' => '2025-09-20',
                'history_day_number' => 1,
            ],
        ];

        // Process first time
        $job1 = new ProcessHistoricalData($data, 'test_file5.json');
        $job1->handle();

        $dealsCount = \App\Models\Deal::where('trading_account_id', $account->id)->count();
        $this->assertEquals(1, $dealsCount);

        // Process again with same data
        $job2 = new ProcessHistoricalData($data, 'test_file5.json');
        $job2->handle();

        // Should still be 1 (duplicate skipped)
        $dealsCount = \App\Models\Deal::where('trading_account_id', $account->id)->count();
        $this->assertEquals(1, $dealsCount);
    }
}
