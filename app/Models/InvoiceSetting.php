<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class InvoiceSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type', 'sort_order'];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null): ?string
    {
        return Cache::remember("invoice_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("invoice_setting_{$key}");
    }

    /**
     * Get all settings as array
     */
    public static function allSettings(): array
    {
        return Cache::remember('invoice_settings_all', 3600, function () {
            return static::orderBy('sort_order')->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('invoice_settings_all');
        foreach (static::pluck('key') as $key) {
            Cache::forget("invoice_setting_{$key}");
        }
    }

    protected static function booted(): void
    {
        static::saved(fn() => static::clearCache());
        static::deleted(fn() => static::clearCache());
    }
}
