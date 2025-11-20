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
            $table->foreignId('affiliate_id')->nullable()->after('is_admin')->constrained()->onDelete('set null');
            $table->foreignId('referred_by_affiliate_id')->nullable()->after('affiliate_id')->constrained('affiliates')->onDelete('set null');
            
            $table->index('affiliate_id');
            $table->index('referred_by_affiliate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['affiliate_id']);
            $table->dropForeign(['referred_by_affiliate_id']);
            $table->dropColumn(['affiliate_id', 'referred_by_affiliate_id']);
        });
    }
};
