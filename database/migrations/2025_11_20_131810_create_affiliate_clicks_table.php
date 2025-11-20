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
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            
            // Tracking Data
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->string('landing_page')->nullable();
            
            // Geolocation
            $table->char('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();
            
            // UTM Parameters
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('utm_term', 100)->nullable();
            
            // Session Tracking
            $table->string('session_id', 64);
            $table->string('fingerprint', 64)->nullable();
            
            // Conversion Tracking
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('conversion_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('clicked_at')->useCurrent();
            
            // Indexes
            $table->index('affiliate_id');
            $table->index('session_id');
            $table->index('ip_address');
            $table->index('converted');
            $table->index('clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
