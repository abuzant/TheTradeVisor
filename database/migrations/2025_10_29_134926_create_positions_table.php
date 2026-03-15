<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('ticket')->index();
            $table->string('symbol', 20)->index();
            $table->string('comment')->nullable();
            $table->string('external_id')->nullable();
            $table->enum('type', ['buy', 'sell'])->index();
            $table->enum('reason', ['client', 'mobile', 'web', 'expert', 'unknown'])->default('unknown');
            $table->decimal('volume', 15, 2);
            $table->decimal('open_price', 15, 5);
            $table->decimal('current_price', 15, 5);
            $table->decimal('sl', 15, 5)->nullable();
            $table->decimal('tp', 15, 5)->nullable();
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('swap', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->timestamp('open_time')->index();
            $table->timestamp('update_time')->nullable();
            $table->bigInteger('magic')->default(0);
            $table->unsignedBigInteger('identifier')->nullable();
            $table->boolean('is_open')->default(true)->index();
            $table->timestamps();
            
            $table->index(['trading_account_id', 'is_open']);
            $table->index(['symbol', 'open_time']);
            $table->index(['trading_account_id', 'ticket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
