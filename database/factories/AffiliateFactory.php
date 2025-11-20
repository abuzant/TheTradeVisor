<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Affiliate>
 */
class AffiliateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $username = fake()->userName();
        
        return [
            'username' => $username,
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'slug' => \Illuminate\Support\Str::slug($username),
            'is_active' => true,
            'is_verified' => true,
            'total_clicks' => 0,
            'total_signups' => 0,
            'paid_signups' => 0,
            'pending_earnings' => 0,
            'approved_earnings' => 0,
            'total_paid' => 0,
            'total_earnings' => 0,
        ];
    }
}
