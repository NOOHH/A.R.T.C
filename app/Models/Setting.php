<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $setting = self::where('group', $group)
                      ->where('key', $key)
                      ->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set($group, $key, $value, $type = 'text')
    {
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
        return self::where('group', $group)->pluck('value', 'key');
    }
}
