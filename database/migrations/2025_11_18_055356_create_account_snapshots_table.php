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
        Schema::create('account_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->decimal('margin', 15, 2)->default(0);
            $table->decimal('free_margin', 15, 2)->default(0);
            $table->decimal('margin_level', 15, 2)->nullable();
            $table->decimal('profit', 15, 2)->default(0);
            $table->timestamp('snapshot_time');
            $table->boolean('is_historical')->default(false);
            $table->string('source')->default('api'); // api, backfill, manual
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['trading_account_id', 'snapshot_time']);
            $table->index('snapshot_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_snapshots');
    }
};
