<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TradingAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountLimitEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that free user can connect first account
     */
    public function test_free_user_can_connect_first_account(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'free',
            'max_accounts' => 1,
            'api_key' => 'test_key_123',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test_key_123',
        ])->postJson('/api/v1/data/collect', [
            'meta' => [
                'is_first_run' => true,
                'is_historical' => false,
            ],
            'account' => [
                'broker' => 'Test Broker',
                'server' => 'TestServer-01',
                'account_number' => '12345',
                'account_hash' => hash('sha256', '12345TestServer-01'),
                'currency' => 'USD',
                'balance' => 1000,
                'equity' => 1000,
                'margin' => 0,
                'free_margin' => 1000,
                'margin_level' => 0,
                'profit' => 0,
                'credit' => 0,
                'leverage' => 100,
                'trade_mode' => 2, // Real account
            ],
            'positions' => [],
            'orders' => [],
            'history' => [],
        ]);

        $response->assertStatus(200);
        $this->assertEquals(1, $user->tradingAccounts()->count());
    }

    /**
     * Test that free user CANNOT connect second account
     */
    public function test_free_user_cannot_connect_second_account(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'free',
            'max_accounts' => 1,
            'api_key' => 'test_key_456',
        ]);

        // Create first account
        TradingAccount::factory()->create([
            'user_id' => $user->id,
            'broker_server' => 'TestServer-01',
            'account_number' => '11111',
        ]);

        // Try to connect second account
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test_key_456',
        ])->postJson('/api/v1/data/collect', [
            'meta' => [
                'is_first_run' => true,
                'is_historical' => false,
            ],
            'account' => [
                'broker' => 'Test Broker',
                'server' => 'TestServer-02',
                'account_number' => '22222',
                'account_hash' => hash('sha256', '22222TestServer-02'),
                'currency' => 'USD',
                'balance' => 1000,
                'equity' => 1000,
                'margin' => 0,
                'free_margin' => 1000,
                'margin_level' => 0,
                'profit' => 0,
                'credit' => 0,
                'leverage' => 100,
                'trade_mode' => 2,
            ],
            'positions' => [],
            'orders' => [],
            'history' => [],
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error' => 'ACCOUNT_LIMIT_EXCEEDED',
        ]);
        
        // Verify second account was NOT created
        $this->assertEquals(1, $user->tradingAccounts()->count());
    }

    /**
     * Test that basic user can connect up to max_accounts
     */
    public function test_basic_user_can_connect_up_to_limit(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'basic',
            'max_accounts' => 3,
            'api_key' => 'test_key_789',
        ]);

        // Connect 3 accounts (should all succeed)
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer test_key_789',
            ])->postJson('/api/v1/data/collect', [
                'meta' => ['is_first_run' => true, 'is_historical' => false],
                'account' => [
                    'broker' => 'Test Broker',
                    'server' => "TestServer-0{$i}",
                    'account_number' => "1111{$i}",
                    'account_hash' => hash('sha256', "1111{$i}TestServer-0{$i}"),
                    'currency' => 'USD',
                    'balance' => 1000,
                    'equity' => 1000,
                    'margin' => 0,
                    'free_margin' => 1000,
                    'margin_level' => 0,
                    'profit' => 0,
                    'credit' => 0,
                    'leverage' => 100,
                    'trade_mode' => 2,
                ],
                'positions' => [],
                'orders' => [],
                'history' => [],
            ]);

            $response->assertStatus(200);
        }

        $this->assertEquals(3, $user->tradingAccounts()->count());
    }

    /**
     * Test that basic user CANNOT exceed max_accounts
     */
    public function test_basic_user_cannot_exceed_limit(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'basic',
            'max_accounts' => 3,
            'api_key' => 'test_key_abc',
        ]);

        // Create 3 accounts (at limit)
        for ($i = 1; $i <= 3; $i++) {
            TradingAccount::factory()->create([
                'user_id' => $user->id,
                'broker_server' => "TestServer-0{$i}",
                'account_number' => "1111{$i}",
            ]);
        }

        // Try to connect 4th account (should fail)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test_key_abc',
        ])->postJson('/api/v1/data/collect', [
            'meta' => ['is_first_run' => true, 'is_historical' => false],
            'account' => [
                'broker' => 'Test Broker',
                'server' => 'TestServer-04',
                'account_number' => '11114',
                'account_hash' => hash('sha256', '11114TestServer-04'),
                'currency' => 'USD',
                'balance' => 1000,
                'equity' => 1000,
                'margin' => 0,
                'free_margin' => 1000,
                'margin_level' => 0,
                'profit' => 0,
                'credit' => 0,
                'leverage' => 100,
                'trade_mode' => 2,
            ],
            'positions' => [],
            'orders' => [],
            'history' => [],
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment(['error' => 'ACCOUNT_LIMIT_EXCEEDED']);
        
        // Verify 4th account was NOT created
        $this->assertEquals(3, $user->tradingAccounts()->count());
    }

    /**
     * Test that enterprise user has unlimited accounts
     */
    public function test_enterprise_user_unlimited_accounts(): void
    {
        $user = User::factory()->create([
            'subscription_tier' => 'enterprise',
            'max_accounts' => 999999,
            'api_key' => 'test_key_enterprise',
        ]);

        // Connect 10 accounts (should all succeed)
        for ($i = 1; $i <= 10; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer test_key_enterprise',
            ])->postJson('/api/v1/data/collect', [
                'meta' => ['is_first_run' => true, 'is_historical' => false],
                'account' => [
                    'broker' => 'Test Broker',
                    'server' => "TestServer-{$i}",
                    'account_number' => "9999{$i}",
                    'account_hash' => hash('sha256', "9999{$i}TestServer-{$i}"),
                    'currency' => 'USD',
                    'balance' => 1000,
                    'equity' => 1000,
                    'margin' => 0,
                    'free_margin' => 1000,
                    'margin_level' => 0,
                    'profit' => 0,
                    'credit' => 0,
                    'leverage' => 100,
                    'trade_mode' => 2,
                ],
                'positions' => [],
                'orders' => [],
                'history' => [],
            ]);

            $response->assertStatus(200);
        }

        $this->assertEquals(10, $user->tradingAccounts()->count());
    }
}
