<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('ticket')->index();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('symbol', 20)->index();
            $table->string('comment')->nullable();
            $table->string('external_id')->nullable();
            $table->string('type', 30); // buy, sell, balance, credit, etc.
            $table->enum('entry', ['in', 'out', 'inout', 'out_by', 'unknown'])->default('unknown');
            $table->enum('reason', ['client', 'mobile', 'web', 'expert', 'sl', 'tp', 'so', 'rollover', 'vmargin', 'split', 'unknown'])->default('unknown')->index();
            $table->decimal('volume', 15, 2);
            $table->decimal('price', 15, 5);
            $table->decimal('profit', 15, 2)->default(0)->index();
            $table->decimal('swap', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('fee', 15, 2)->default(0);
            $table->timestamp('time')->index();
            $table->bigInteger('time_msc')->nullable();
            $table->bigInteger('magic')->default(0);
            $table->timestamps();
            
            $table->index(['trading_account_id', 'time']);
            $table->index(['symbol', 'type']);
            $table->index(['trading_account_id', 'ticket']);
            $table->index(['reason', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
