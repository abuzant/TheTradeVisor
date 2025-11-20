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
        Schema::create('affiliate_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->date('date');
            
            // Daily Metrics
            $table->integer('clicks')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->integer('signups')->default(0);
            $table->integer('paid_signups')->default(0);
            $table->decimal('earnings', 10, 2)->default(0.00);
            
            // Conversion Rates
            $table->decimal('click_to_signup_rate', 5, 2)->default(0.00);
            $table->decimal('signup_to_paid_rate', 5, 2)->default(0.00);
            
            // Top Sources
            $table->char('top_country_code', 2)->nullable();
            $table->string('top_utm_source', 100)->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Unique constraint and index
            $table->unique(['affiliate_id', 'date']);
            $table->index(['affiliate_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_analytics');
    }
};
