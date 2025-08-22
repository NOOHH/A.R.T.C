<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\TenantUiSetting;

class Setting extends Model
{
    use HasFactory;

    // Use default connection (will be switched by TenantService)
    // protected $connection = 'tenant'; // Removed hardcoded connection
    
    protected $fillable = [
        'group',
        'key', 
        'value',
        'type'
    ];

    /**
     * Get a setting value
     */
    public static function get($group, $key, $default = null)
    {
        // Some tenant databases use a different table/schema (ui_settings). Detect and delegate.
        try {
            $schema = DB::getSchemaBuilder();
            // Prefer ui_settings (tenant schema) if present
            if ($schema->hasTable('ui_settings')) {
                return TenantUiSetting::get($group, $key, $default);
            }
            if ($schema->hasTable('settings')) {
                $setting = self::where('group', $group)
                              ->where('key', $key)
                              ->first();
                return $setting ? $setting->value : $default;
            }
        } catch (\Throwable $e) {
            // If anything goes wrong with schema inspection, fall back to default
        }

        return $default;
    }

    /**
     * Set a setting value
     */
    public static function set($group, $key, $value, $type = 'text')
    {
        try {
            $schema = DB::getSchemaBuilder();
            if ($schema->hasTable('ui_settings')) {
                return TenantUiSetting::set($group, $key, $value, $type);
            }
            if ($schema->hasTable('settings')) {
                return self::updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value, 'type' => $type]
                );
            }
        } catch (\Throwable $e) {
            // ignore and try direct updateOrCreate as last resort
        }

        return self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup($group)
    {
        try {
            $schema = DB::getSchemaBuilder();
            if ($schema->hasTable('ui_settings')) {
                return TenantUiSetting::getSection($group);
            }
            if ($schema->hasTable('settings')) {
                return self::where('group', $group)->pluck('value', 'key');
            }
        } catch (\Throwable $e) {
            // fall back
        }

        return self::where('group', $group)->pluck('value', 'key');
    }
}
