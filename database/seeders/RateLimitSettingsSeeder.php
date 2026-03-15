<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RateLimitSetting;

class RateLimitSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'analytics_limit',
                'value' => 10,
                'description' => 'Analytics and performance page requests per user per minute',
                'type' => 'user',
                'is_active' => true,
            ],
            [
                'key' => 'broker_analytics_limit',
                'value' => 20,
                'description' => 'Broker analytics requests per user per minute',
                'type' => 'user',
                'is_active' => true,
            ],
            [
                'key' => 'export_limit',
                'value' => 5,
                'description' => 'Data export requests per user per minute (CSV, Excel, etc.)',
                'type' => 'user',
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            RateLimitSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Rate limit settings seeded successfully!');
    }
}
