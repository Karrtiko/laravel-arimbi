<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
        'group',
        'sort_order',
    ];

    /**
     * Get a setting by key
     */
    public static function get(string $key, $default = null)
    {
        return static::allSettings()[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        static::clearCache();
    }

    /**
     * Get all settings as key-value array
     */
    public static function allSettings(): array
    {
        return Cache::remember('general_settings_all', 3600, function () {
            return static::orderBy('sort_order')->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('general_settings_all');
    }

    protected static function booted()
    {
        static::saved(fn() => static::clearCache());
        static::deleted(fn() => static::clearCache());
    }
}
