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
        Schema::create('uninstall_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('reason', 255);                    // Primary reason for uninstalling
            $table->string('experience_rating', 255);         // User experience rating
            $table->string('would_return', 50);               // Would they consider returning
            $table->string('email')->nullable();              // Optional email for follow-up
            $table->text('comments')->nullable();             // Additional comments
            $table->string('ip_address', 45);                 // IP address for analytics
            $table->text('user_agent')->nullable();           // User agent for device tracking
            $table->string('referer')->nullable();            // Where they came from
            $table->json('utm_parameters')->nullable();       // UTM tracking parameters
            $table->timestamp('submitted_at')->useCurrent();  // When feedback was submitted
            $table->timestamps();
            
            // Indexes for analytics queries
            $table->index('reason');
            $table->index('experience_rating');
            $table->index('would_return');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uninstall_feedback');
    }
};
