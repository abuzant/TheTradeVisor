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
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('click_id')->constrained('affiliate_clicks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Conversion Details
            $table->enum('subscription_tier', ['basic', 'pro', 'enterprise']);
            $table->decimal('commission_amount', 10, 2);
            $table->string('commission_currency', 3)->default('USD');
            
            // Status
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Fraud Detection
            $table->boolean('is_suspicious')->default(false);
            $table->integer('fraud_score')->default(0);
            $table->text('fraud_notes')->nullable();
            
            $table->timestamp('converted_at')->useCurrent();
            
            // Indexes
            $table->index('affiliate_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('converted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_conversions');
    }
};
