<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds platform detection fields to trading_accounts table
     * to differentiate between MT4 and MT5, and Netting vs Hedging modes.
     */
    public function up(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            // Platform type: MT4 or MT5
            $table->string('platform_type', 10)->nullable()->after('account_type');
            
            // Account mode: netting or hedging (MT5 only, MT4 is always hedging)
            $table->string('account_mode', 10)->nullable()->after('platform_type');
            
            // Platform build number for version tracking
            $table->integer('platform_build')->nullable()->after('account_mode');
            
            // When platform was detected
            $table->timestamp('platform_detected_at')->nullable()->after('platform_build');
            
            // Add indexes for filtering
            $table->index('platform_type');
            $table->index(['platform_type', 'account_mode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->dropIndex(['trading_accounts_platform_type_index']);
            $table->dropIndex(['trading_accounts_platform_type_account_mode_index']);
            $table->dropColumn([
                'platform_type',
                'account_mode',
                'platform_build',
                'platform_detected_at'
            ]);
        });
    }
};
