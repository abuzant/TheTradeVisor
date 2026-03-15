<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmergencyRecovery extends Command
{
    protected $signature = 'emergency:recover';
    protected $description = 'Emergency recovery - recreate user and reprocess all data';

    public function handle()
    {
        $this->error('🚨 EMERGENCY RECOVERY MODE');
        $this->info('This will recreate your user account and reprocess all JSON data.');
        
        if (!$this->confirm('Do you want to proceed?')) {
            return;
        }

        // Step 1: Recreate Ruslan's user account
        $this->info('Step 1: Recreating user account...');
        
        $user = User::firstOrCreate(
            ['email' => 'ruslan.abuzant@gmail.com'],
            [
                'name' => 'Ruslan Abuzant',
                'password' => Hash::make('your_password_here'), // YOU NEED TO SET THIS
                'email_verified_at' => now(),
                'is_admin' => true,
                'is_active' => true,
                'subscription_tier' => 'enterprise',
                'max_accounts' => 999,
                'api_key' => \Illuminate\Support\Str::random(64),
            ]
        );
        
        $this->info("✅ User created: {$user->email} (ID: {$user->id})");
        $this->info("🔑 API Key: {$user->api_key}");
        $this->warn("⚠️  SAVE THIS API KEY - Update it in your MT5 EA!");
        
        // Step 2: Find and process JSON files
        $this->info("\nStep 2: Processing JSON files...");
        
        $this->call('emergency:reprocess-json', ['user_id' => $user->id]);
        
        $this->info("\n✅ Recovery complete!");
        $this->info("Login with: ruslan.abuzant@gmail.com");
        $this->info("API Key: {$user->api_key}");
        
        return Command::SUCCESS;
    }
}
