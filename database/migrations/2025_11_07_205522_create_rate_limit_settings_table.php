<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rate_limit_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->integer('value');
            $table->string('description')->nullable();
            $table->string('type')->default('global'); // global, ip, api_key, user
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('rate_limit_settings')->insert([
            [
                'key' => 'global_ip_limit',
                'value' => 60,
                'description' => 'Maximum requests per minute per IP address',
                'type' => 'ip',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'global_api_key_limit',
                'value' => 120,
                'description' => 'Maximum requests per minute per API key',
                'type' => 'api_key',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'burst_limit',
                'value' => 200,
                'description' => 'Maximum burst requests per minute',
                'type' => 'global',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'premium_api_key_limit',
                'value' => 300,
                'description' => 'Maximum requests per minute for premium users',
                'type' => 'api_key',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limit_settings');
    }
};
