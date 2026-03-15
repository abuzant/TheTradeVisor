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
        Schema::create('backfill_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // UUID for external reference
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_account_id')->nullable()->constrained()->onDelete('cascade');
            
            // Session metadata
            $table->string('trigger_type'); // 'gap_detection', 'manual', 'scheduled'
            $table->string('priority')->default('normal'); // 'low', 'normal', 'high', 'critical'
            $table->string('status')->default('pending'); // 'pending', 'running', 'completed', 'failed', 'cancelled'
            
            // Time range targeting
            $table->dateTime('gap_start_time')->nullable();
            $table->dateTime('gap_end_time')->nullable();
            $table->integer('estimated_missing_snapshots')->default(0);
            
            // Progress tracking
            $table->integer('total_files_to_process')->default(0);
            $table->integer('files_processed')->default(0);
            $table->integer('snapshots_created')->default(0);
            $table->integer('errors_count')->default(0);
            
            // Performance metrics
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('completion_percentage', 5, 2)->default(0);
            
            // Error handling
            $table->text('error_message')->nullable();
            $table->json('failed_files')->nullable(); // List of failed files for retry
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional session data
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'priority']);
            $table->index(['user_id', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backfill_sessions');
    }
};
