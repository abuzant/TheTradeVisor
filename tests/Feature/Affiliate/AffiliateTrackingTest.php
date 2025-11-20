<?php

namespace Tests\Feature\Affiliate;

use App\Models\Affiliate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliate_click_is_tracked()
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->get("/offers/{$affiliate->slug}");

        $response->assertRedirect();
        $this->assertDatabaseHas('affiliate_clicks', [
            'affiliate_id' => $affiliate->id,
        ]);
    }

    public function test_affiliate_cookie_is_set()
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->get("/offers/{$affiliate->slug}");

        $response->assertCookie('affiliate_ref', $affiliate->slug);
    }

    public function test_user_registration_tracks_affiliate()
    {
        $affiliate = Affiliate::factory()->create();

        $response = $this->withCookie('affiliate_ref', $affiliate->slug)
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'referred_by_affiliate_id' => $affiliate->id,
        ]);
    }

    public function test_conversion_is_created_on_paid_signup()
    {
        $affiliate = Affiliate::factory()->create();
        $user = User::factory()->create([
            'referred_by_affiliate_id' => $affiliate->id,
        ]);

        event(new \App\Events\UserUpgradedSubscription($user, 'basic'));

        $this->assertDatabaseHas('affiliate_conversions', [
            'affiliate_id' => $affiliate->id,
            'user_id' => $user->id,
            'commission_amount' => 1.99,
        ]);
    }

    public function test_inactive_affiliate_link_returns_404()
    {
        $affiliate = Affiliate::factory()->create(['is_active' => false]);

        $response = $this->get("/offers/{$affiliate->slug}");

        $response->assertStatus(404);
    }

    public function test_invalid_affiliate_slug_returns_404()
    {
        $response = $this->get("/offers/nonexistent");

        $response->assertStatus(404);
    }
}
