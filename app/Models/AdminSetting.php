<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $table = 'admin_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function setValue($key, $value, $description = null)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'description' => $description,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Check if a boolean setting is enabled.
     */
    public static function isEnabled($key)
    {
        $value = self::getValue($key, 'false');
        return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
    }

    /**
     * Get all public settings.
     */
    public static function getPublicSettings()
    {
        return self::where('is_public', true)->get()->pluck('setting_value', 'setting_key');
    }

    /**
     * Scope to get public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
