<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds activity_type field to deals, positions, and orders tables
     * to track the specific action that created/modified the record.
     * 
     * Activity types:
     * - position_opened: New position opened
     * - position_closed: Position closed
     * - position_modified: Position SL/TP modified
     * - order_placed: Pending order placed
     * - order_modified: Pending order modified
     * - order_cancelled: Pending order cancelled
     * - order_filled: Pending order filled (became position)
     * - order_expired: Pending order expired
     */
    public function up(): void
    {
        // Add activity_type to deals table
        Schema::table('deals', function (Blueprint $table) {
            $table->string('activity_type', 50)->nullable()->after('platform_type');
            $table->index('activity_type');
        });

        // Add activity_type to positions table
        Schema::table('positions', function (Blueprint $table) {
            $table->string('activity_type', 50)->nullable()->after('platform_type');
            $table->index('activity_type');
        });

        // Add activity_type and platform_type to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('platform_type', 10)->nullable()->after('is_active');
            $table->string('activity_type', 50)->nullable()->after('platform_type');
            $table->index('platform_type');
            $table->index('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['activity_type']);
            $table->dropColumn('activity_type');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropIndex(['activity_type']);
            $table->dropColumn('activity_type');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['platform_type']);
            $table->dropIndex(['activity_type']);
            $table->dropColumn(['platform_type', 'activity_type']);
        });
    }
};
