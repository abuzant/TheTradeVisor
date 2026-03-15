<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:check {api_key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if an API key exists and is valid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = $this->argument('api_key');
        
        $this->info("Checking API key: {$apiKey}");
        $this->newLine();
        
        // Check if key exists
        $user = User::where('api_key', $apiKey)->first();
        
        if (!$user) {
            $this->error('❌ API key NOT FOUND in database');
            $this->newLine();
            $this->info('Possible reasons:');
            $this->line('  1. The API key was never created');
            $this->line('  2. The API key was deleted or regenerated');
            $this->line('  3. There is a typo in the API key');
            $this->newLine();
            
            // Show all users with their API keys
            $this->info('Available users and their API keys:');
            $users = User::select('id', 'name', 'email', 'api_key', 'is_active')->get();
            
            if ($users->isEmpty()) {
                $this->warn('No users found in database');
            } else {
                $this->table(
                    ['ID', 'Name', 'Email', 'API Key', 'Active'],
                    $users->map(function ($user) {
                        return [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->api_key,
                            $user->is_active ? '✓' : '✗'
                        ];
                    })
                );
            }
            
            return 1;
        }
        
        // Key found - check status
        $this->info('✓ API key FOUND');
        $this->newLine();
        
        $this->table(
            ['Field', 'Value'],
            [
                ['User ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['API Key', $user->api_key],
                ['Is Active', $user->is_active ? '✓ YES' : '✗ NO'],
                ['Subscription Tier', $user->subscription_tier],
                ['Max Accounts', $user->max_accounts],
                ['Created At', $user->created_at],
            ]
        );
        
        $this->newLine();
        
        if (!$user->is_active) {
            $this->error('❌ PROBLEM: User account is INACTIVE');
            $this->info('Solution: Activate the user account');
            return 1;
        }
        
        $this->info('✓ API key is VALID and user is ACTIVE');
        $this->info('The API key should work correctly.');
        
        return 0;
    }
}
