<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\User;
use App\Models\TradingAccount;
use App\Models\Deal;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class DeleteUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_deletes_user_and_all_related_data(): void
    {
        Storage::fake('trading_data');

        // Create user with trading data
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $account = TradingAccount::factory()->create([
            'user_id' => $user->id,
        ]);

        Deal::factory()->count(5)->create([
            'trading_account_id' => $account->id,
        ]);

        Position::factory()->count(3)->create([
            'trading_account_id' => $account->id,
        ]);

        // Create storage files
        Storage::disk('trading_data')->put("{$user->id}/test.json", '{}');

        // Run command
        $this->artisan('user:delete', ['email' => 'test@example.com', '--force' => true])
             ->assertExitCode(0);

        // Verify deletion
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertDatabaseMissing('trading_accounts', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('deals', ['trading_account_id' => $account->id]);
        $this->assertDatabaseMissing('positions', ['trading_account_id' => $account->id]);
        $this->assertFalse(Storage::disk('trading_data')->exists("{$user->id}/test.json"));
    }

    public function test_command_fails_for_nonexistent_user(): void
    {
        $this->artisan('user:delete', ['email' => 'nonexistent@example.com', '--force' => true])
             ->expectsOutput("User with email 'nonexistent@example.com' not found.")
             ->assertExitCode(1);
    }
}
