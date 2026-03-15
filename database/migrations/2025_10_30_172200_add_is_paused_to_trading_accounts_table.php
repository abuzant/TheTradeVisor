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
		Schema::table('trading_accounts', function (Blueprint $table) {
		    $table->boolean('is_paused')->default(false)->after('is_active');
		    $table->timestamp('paused_at')->nullable()->after('is_paused');
		    $table->foreignId('paused_by')->nullable()->constrained('users')->after('paused_at');
		    $table->text('pause_reason')->nullable()->after('paused_by');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            //
        });
    }
};
