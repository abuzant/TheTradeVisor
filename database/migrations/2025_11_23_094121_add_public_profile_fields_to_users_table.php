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
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_username', 50)->unique()->nullable()->after('email');
            $table->timestamp('public_username_set_at')->nullable()->after('public_username');
            $table->enum('public_display_mode', ['username', 'anonymous', 'custom_name'])
                  ->default('username')->after('public_username_set_at');
            $table->string('public_display_name', 100)->nullable()->after('public_display_mode');
            $table->boolean('show_on_leaderboard')->default(false)->after('public_display_name');
            $table->enum('leaderboard_rank_by', ['total_profit', 'roi', 'win_rate', 'profit_factor'])
                  ->default('total_profit')->after('show_on_leaderboard');
            
            $table->index('public_username');
            $table->index('show_on_leaderboard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['public_username']);
            $table->dropIndex(['show_on_leaderboard']);
            $table->dropColumn([
                'public_username',
                'public_username_set_at',
                'public_display_mode',
                'public_display_name',
                'show_on_leaderboard',
                'leaderboard_rank_by'
            ]);
        });
    }
};
