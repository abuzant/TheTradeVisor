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
        Schema::create('history_upload_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained('trading_accounts')->onDelete('cascade');
            $table->date('last_day_uploaded')->index();
            $table->integer('days_processed')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['trading_account_id', 'completed_at']);
            $table->unique('trading_account_id'); // One progress record per account
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_upload_progress');
    }
};
