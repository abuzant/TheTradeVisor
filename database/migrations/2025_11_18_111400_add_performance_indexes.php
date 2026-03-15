<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * These indexes are critical for query performance based on the audit findings.
     * They target the most frequently executed queries in the application.
     */
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // For closedTrades scope (most critical query)
            // Used in: Dashboard, Analytics, Trades, Exports
            $table->index(['entry', 'type'], 'deals_entry_type_idx');
            
            // For position history queries
            // Used in: Position aggregation, Trade history
            $table->index('position_id', 'deals_position_id_idx');
            
            // For date range queries with account filtering
            // Used in: Analytics, Exports, Dashboard
            $table->index(['trading_account_id', 'time'], 'deals_account_time_idx');
            
            // For symbol-specific analytics
            // Used in: Symbol performance, Trade filtering
            $table->index('symbol', 'deals_symbol_idx');
            
            // For deal categorization (trade vs balance)
            // Used in: Analytics filtering
            $table->index('deal_category', 'deals_category_idx');
        });
        
        Schema::table('positions', function (Blueprint $table) {
            // For open positions queries
            // Used in: Dashboard, Account details
            $table->index(['trading_account_id', 'is_open'], 'positions_account_open_idx');
            
            // For symbol-based position queries
            // Used in: Symbol analytics
            $table->index('symbol', 'positions_symbol_idx');
            
            // For platform-specific queries (MT4/MT5)
            // Used in: Platform detection, Position aggregation
            $table->index('platform_type', 'positions_platform_idx');
        });
        
        Schema::table('trading_accounts', function (Blueprint $table) {
            // For user's active accounts
            // Used in: Dashboard, Analytics, Every authenticated page
            $table->index(['user_id', 'is_active', 'is_paused'], 'accounts_user_active_idx');
            
            // For broker analytics
            // Used in: Broker comparison, Public broker pages
            $table->index('broker_name', 'accounts_broker_idx');
            
            // For country-based analytics
            // Used in: Geographic analytics
            $table->index('country_code', 'accounts_country_idx');
            
            // For platform detection queries
            // Used in: Platform statistics
            $table->index('platform_type', 'accounts_platform_idx');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // For active orders queries
            // Used in: Dashboard, Account details
            $table->index(['trading_account_id', 'is_active'], 'orders_account_active_idx');
        });
        
        Schema::table('symbol_mappings', function (Blueprint $table) {
            // For symbol normalization (extremely frequent)
            // Used in: Every deal/position display
            $table->index('raw_symbol', 'symbol_mappings_raw_idx');
            $table->index('normalized_symbol', 'symbol_mappings_normalized_idx');
        });
        
        Schema::table('currency_rates', function (Blueprint $table) {
            // For currency conversion
            // Used in: Dashboard, Analytics, Exports
            $table->index(['from_currency', 'to_currency'], 'currency_rates_pair_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex('deals_entry_type_idx');
            $table->dropIndex('deals_position_id_idx');
            $table->dropIndex('deals_account_time_idx');
            $table->dropIndex('deals_symbol_idx');
            $table->dropIndex('deals_category_idx');
        });
        
        Schema::table('positions', function (Blueprint $table) {
            $table->dropIndex('positions_account_open_idx');
            $table->dropIndex('positions_symbol_idx');
            $table->dropIndex('positions_platform_idx');
        });
        
        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->dropIndex('accounts_user_active_idx');
            $table->dropIndex('accounts_broker_idx');
            $table->dropIndex('accounts_country_idx');
            $table->dropIndex('accounts_platform_idx');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_account_active_idx');
        });
        
        Schema::table('symbol_mappings', function (Blueprint $table) {
            $table->dropIndex('symbol_mappings_raw_idx');
            $table->dropIndex('symbol_mappings_normalized_idx');
        });
        
        Schema::table('currency_rates', function (Blueprint $table) {
            $table->dropIndex('currency_rates_pair_idx');
        });
    }
};
