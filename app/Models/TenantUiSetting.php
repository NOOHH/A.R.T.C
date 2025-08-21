<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantUiSetting extends Model
{
    use HasFactory;

    // Use tenant connection by default
    protected $connection = 'tenant';
    
    protected $table = 'ui_settings';
    
    protected $fillable = [
        'section',
        'setting_key',
        'setting_value',
        'setting_type'
    ];

    /**
     * Get a setting value
     */
    public static function get($section, $key, $default = null)
    {
        $setting = self::where('section', $section)
                      ->where('setting_key', $key)
                      ->first();
        
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($section, $key, $value, $type = 'text')
    {
        return self::updateOrCreate(
            ['section' => $section, 'setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type
            ]
        );
    }

    /**
     * Get all settings for a section
     */
    public static function getSection($section)
    {
        return self::where('section', $section)->pluck('setting_value', 'setting_key');
    }
}
