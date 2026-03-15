<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('account_uuid')->unique();
            $table->string('account_number')->nullable(); // Nullable if anonymized
            $table->string('account_hash')->nullable(); // For anonymized accounts
            $table->string('broker_name');
            $table->string('broker_server');
            $table->string('account_name')->nullable();
            $table->string('account_currency', 10);
            $table->integer('leverage')->default(1);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('equity', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);
            $table->decimal('free_margin', 15, 2)->default(0);
            $table->decimal('margin_level', 10, 2)->nullable();
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->boolean('trade_allowed')->default(true);
            $table->boolean('trade_expert')->default(true);
            $table->string('last_seen_ip')->nullable();
            $table->string('detected_country', 10)->nullable();
            $table->string('detected_city')->nullable();
            $table->string('detected_timezone')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('account_uuid');
            $table->index('broker_server');
            $table->index('detected_country');
            $table->index('last_sync_at');
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_accounts');
    }
};
