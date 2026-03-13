<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get a setting value by key with automatic type casting
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting || $setting->value === null) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'datetime' => $setting->value ? \Carbon\Carbon::parse($setting->value) : null,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, mixed $value, ?int $updatedBy = null): void
    {
        $setting = static::firstOrCreate(['key' => $key]);

        // Convert value to string for storage
        $stringValue = match ($setting->type) {
            'boolean' => $value ? '1' : '0',
            'datetime' => $value ? \Carbon\Carbon::parse($value)->toDateTimeString() : null,
            'json' => json_encode($value),
            default => (string) $value,
        };

        $setting->update([
            'value' => $stringValue,
            'updated_by' => $updatedBy,
        ]);
    }
}
