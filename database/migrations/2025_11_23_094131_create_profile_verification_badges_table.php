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
        Schema::create('profile_verification_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->string('badge_type', 50);
            $table->string('badge_name', 100);
            $table->string('badge_icon', 50);
            $table->string('badge_color', 20)->default('gray');
            $table->integer('badge_tier')->default(1);
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('earned_at');
            $table->timestamps();
            
            $table->unique(['trading_account_id', 'badge_type']);
            $table->index('trading_account_id');
            $table->index(['trading_account_id', 'is_favorite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_verification_badges');
    }
};
