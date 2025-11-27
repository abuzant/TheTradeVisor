<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Use array cache for testing to avoid Redis dependency
        Config::set('cache.default', 'array');
        
        // Disable rate limiting for tests
        Config::set('app.rate_limiting_enabled', false);
    }

    public function test_api_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/data/collect', [
            'meta' => ['test' => true],
            'account' => ['trade_mode' => 0],
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'error' => 'API key is required',
                 ]);
    }

    public function test_api_rejects_invalid_key(): void
    {
        $response = $this->postJson('/api/v1/data/collect', [
            'meta' => ['test' => true],
            'account' => ['trade_mode' => 0],
        ], [
            'Authorization' => 'Bearer tvsr_' . str_repeat('a', 64),
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'error' => 'Invalid API key',
                 ]);
    }

    public function test_api_rejects_malformed_key_format(): void
    {
        $response = $this->postJson('/api/v1/data/collect', [
            'meta' => ['test' => true],
            'account' => ['trade_mode' => 0],
        ], [
            'Authorization' => 'Bearer invalid_key_123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'error' => 'Invalid API key',
                     'message' => 'The provided API key format is invalid',
                 ]);
    }

    public function test_api_accepts_valid_key(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'api_key' => User::generateApiKey(),
        ]);

        // Test that valid key passes authentication (doesn't get 401)
        $response = $this->withoutMiddleware([
            \App\Http\Middleware\ApiRateLimiter::class,
        ])->postJson('/api/v1/data/collect', [
            'meta' => [
                'version' => '2.0',
                'timestamp' => now()->format('Y.m.d H:i:s'),
                'is_historical' => false,
            ],
            'account' => [
                'account_number' => 12345,
                'broker' => 'Test Broker',
                'server' => 'TestServer-Live',
                'currency' => 'USD',
                'balance' => 10000,
                'equity' => 10000,
                'margin' => 0,
                'free_margin' => 10000,
                'margin_level' => 0,
                'profit' => 0,
                'credit' => 0,
                'leverage' => 100,
                'trade_mode' => 0,
            ],
            'positions' => [],
            'orders' => [],
            'history' => [],
        ], [
            'Authorization' => 'Bearer ' . $user->api_key,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data received successfully',
                 ]);
    }

    public function test_api_rejects_inactive_user(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'api_key' => User::generateApiKey(),
        ]);

        $response = $this->postJson('/api/v1/data/collect', [
            'meta' => ['test' => true],
            'account' => ['trade_mode' => 0],
        ], [
            'Authorization' => 'Bearer ' . $user->api_key,
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'error' => 'Invalid API key',
                 ]);
    }

    public function test_api_accepts_key_without_bearer_prefix(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'api_key' => User::generateApiKey(),
        ]);

        $response = $this->withoutMiddleware([
            \App\Http\Middleware\ApiRateLimiter::class,
        ])->postJson('/api/v1/data/collect', [
            'meta' => [
                'version' => '2.0',
                'timestamp' => now()->format('Y.m.d H:i:s'),
                'is_historical' => false,
            ],
            'account' => [
                'account_number' => 12345,
                'broker' => 'Test Broker',
                'server' => 'TestServer-Live',
                'currency' => 'USD',
                'balance' => 10000,
                'equity' => 10000,
                'margin' => 0,
                'free_margin' => 10000,
                'margin_level' => 0,
                'profit' => 0,
                'credit' => 0,
                'leverage' => 100,
                'trade_mode' => 0,
            ],
            'positions' => [],
            'orders' => [],
            'history' => [],
        ], [
            'Authorization' => $user->api_key,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);
    }
}
