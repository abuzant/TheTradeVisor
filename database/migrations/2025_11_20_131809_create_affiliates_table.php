<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('slug', 12)->unique();
            $table->string('referral_url');
            
            // Status & Verification
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            
            // Payout Information
            $table->string('usdt_wallet_address')->nullable();
            $table->enum('wallet_type', ['TRC20', 'ERC20'])->nullable();
            $table->decimal('payment_threshold', 10, 2)->default(50.00);
            
            // Cached Statistics
            $table->integer('total_clicks')->default(0);
            $table->integer('total_signups')->default(0);
            $table->integer('total_paid_signups')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->decimal('pending_earnings', 10, 2)->default(0.00);
            $table->decimal('paid_earnings', 10, 2)->default(0.00);
            
            // Metadata
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('slug');
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
