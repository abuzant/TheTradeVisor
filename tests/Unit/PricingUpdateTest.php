<?php

namespace Tests\Unit;

use Tests\TestCase;

class PricingUpdateTest extends TestCase
{
    /**
     * Test that validation rejects 'pro' tier
     */
    public function test_validation_rejects_pro_tier(): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['subscription_tier' => 'pro'],
            ['subscription_tier' => 'required|in:free,basic,enterprise']
        );

        $this->assertTrue($validator->fails(), 'Validation should reject "pro" tier');
        $this->assertArrayHasKey('subscription_tier', $validator->errors()->toArray());
    }

    /**
     * Test that validation accepts valid tiers
     */
    public function test_validation_accepts_valid_tiers(): void
    {
        $validTiers = ['free', 'basic', 'enterprise'];

        foreach ($validTiers as $tier) {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['subscription_tier' => $tier],
                ['subscription_tier' => 'required|in:free,basic,enterprise']
            );

            $this->assertFalse($validator->fails(), "Validation should accept '{$tier}' tier");
        }
    }

    /**
     * Test that edit view doesn't contain pro option
     */
    public function test_edit_view_has_no_pro_option(): void
    {
        $content = file_get_contents(base_path('resources/views/admin/users/edit.blade.php'));
        
        $this->assertStringNotContainsString('value="pro"', $content, 'Edit view should not have pro option');
        $this->assertStringContainsString('Pay-per-account', $content, 'Edit view should have new basic description');
    }

    /**
     * Test that admin dashboard has no pro badge
     */
    public function test_admin_dashboard_has_no_pro_badge(): void
    {
        $content = file_get_contents(base_path('resources/views/admin/dashboard.blade.php'));
        
        $this->assertStringNotContainsString("subscription_tier === 'pro'", $content, 'Dashboard should not have pro badge');
    }

    /**
     * Test that admin users show has no pro badge
     */
    public function test_admin_users_show_has_no_pro_badge(): void
    {
        $content = file_get_contents(base_path('resources/views/admin/users/show.blade.php'));
        
        $this->assertStringNotContainsString("subscription_tier === 'pro'", $content, 'User show should not have pro badge');
    }

    /**
     * Test that admin users index has no pro badge
     */
    public function test_admin_users_index_has_no_pro_badge(): void
    {
        $content = file_get_contents(base_path('resources/views/admin/users/index.blade.php'));
        
        $this->assertStringNotContainsString("subscription_tier === 'pro'", $content, 'Users index should not have pro badge');
    }

    /**
     * Test that FAQ has correct pricing
     */
    public function test_faq_has_correct_pricing(): void
    {
        $content = file_get_contents(base_path('resources/views/public/faq.blade.php'));
        
        $this->assertStringNotContainsString('2.99', $content, 'FAQ should not have $2.99 pricing');
        $this->assertStringNotContainsString('Pro plan', $content, 'FAQ should not reference Pro plan');
        $this->assertStringContainsString('9.99 one-time', $content, 'FAQ should have $9.99 one-time pricing');
    }

    /**
     * Test that pricing page has correct pricing
     */
    public function test_pricing_page_has_correct_pricing(): void
    {
        $content = file_get_contents(base_path('resources/views/public/pricing.blade.php'));
        
        $this->assertStringNotContainsString('2.99', $content, 'Pricing page should not have $2.99');
        $this->assertStringNotContainsString('24.99/month', $content, 'Pricing page should not have $24.99/month');
        $this->assertStringNotContainsString('Pro (', $content, 'Pricing page should not have Pro tier');
        $this->assertStringContainsString('$9.99', $content, 'Pricing page should have $9.99');
        $this->assertStringContainsString('One-time payment', $content, 'Pricing page should say one-time payment');
    }

    /**
     * Test that pricing page uses valid route
     */
    public function test_pricing_page_uses_valid_route(): void
    {
        $content = file_get_contents(base_path('resources/views/public/pricing.blade.php'));
        
        $this->assertStringNotContainsString('accounts.purchase', $content, 'Pricing page should not use non-existent route');
        $this->assertStringContainsString('accounts.index', $content, 'Pricing page should use accounts.index route');
    }

    /**
     * Test that controller validation is correct
     */
    public function test_controller_validation_is_correct(): void
    {
        $content = file_get_contents(base_path('app/Http/Controllers/Admin/UserManagementController.php'));
        
        $this->assertStringNotContainsString("'required|in:free,basic,pro,enterprise'", $content, 'Controller should not accept pro tier');
        $this->assertStringContainsString("'required|in:free,basic,enterprise'", $content, 'Controller should only accept 3 tiers');
    }
}
