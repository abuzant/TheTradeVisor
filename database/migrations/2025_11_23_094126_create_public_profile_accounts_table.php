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
        Schema::create('public_profile_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->string('account_slug', 100);
            $table->boolean('is_public')->default(false);
            $table->string('custom_title', 150)->nullable();
            $table->enum('widget_preset', ['minimal', 'full_stats', 'trader_showcase', 'custom'])
                  ->default('minimal');
            $table->json('visible_widgets')->nullable();
            $table->boolean('show_recent_trades')->default(false);
            $table->boolean('show_symbols')->default(true);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'account_slug']);
            $table->unique(['user_id', 'trading_account_id']);
            $table->index('is_public');
            $table->index(['user_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_profile_accounts');
    }
};
