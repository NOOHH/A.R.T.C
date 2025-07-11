<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_type',
        'enable_synchronous',
        'enable_asynchronous',
        'additional_config',
    ];

    protected $casts = [
        'enable_synchronous' => 'boolean',
        'enable_asynchronous' => 'boolean',
        'additional_config' => 'array',
    ];

    /**
     * Get learning mode settings for a specific plan type
     */
    public static function getForPlanType($planType)
    {
        return self::where('plan_type', $planType)->first();
    }

    /**
     * Get available learning modes for a plan type
     */
    public static function getAvailableModesForPlan($planType)
    {
        $setting = self::getForPlanType($planType);
        
        if (!$setting) {
            return ['synchronous' => true, 'asynchronous' => true]; // Default fallback
        }

        return [
            'synchronous' => $setting->enable_synchronous,
            'asynchronous' => $setting->enable_asynchronous,
        ];
    }
}
