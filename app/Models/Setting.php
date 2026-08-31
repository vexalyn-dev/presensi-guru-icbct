<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    protected $casts = [
        'value' => 'string',
    ];

    public static function get(string $key, $default = null): mixed
    {
        $cached = cache()->remember("setting.{$key}", 3600, function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting ? self::castValue($setting->value, $setting->type) : null;
        });
        return $cached ?? $default;
    }

    public static function forget(string $key): void
    {
        cache()->forget("setting.{$key}");
    }

    public static function forgetGroup(string $group): void
    {
        $keys = self::where('group', $group)->pluck('key');
        foreach ($keys as $k) {
            cache()->forget("setting.{$k}");
        }
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general', ?string $description = null): Setting
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group, 'description' => $description]
        );
        cache()->forget("setting.{$key}");
        return $setting;
    }

    private static function castValue(mixed $value, string $type): mixed
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float', 'number' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}