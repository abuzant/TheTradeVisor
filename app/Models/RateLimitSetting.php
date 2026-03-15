<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RateLimitSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, int $default = null): int
    {
        $setting = static::where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, int $value, string $description = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get all active settings as key-value pairs
     */
    public static function getAllActive(): array
    {
        return static::where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
    }
}
