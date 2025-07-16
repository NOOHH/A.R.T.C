<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_name',
        'level_description',
        'level_order',
        'file_requirements',
        'available_for_general',
        'available_for_professional',
        'available_for_review',
        'available_full_plan',
        'available_modular_plan',
        'is_active'
    ];

    protected $casts = [
        'file_requirements' => 'array',
        'available_for_general' => 'boolean',
        'available_for_professional' => 'boolean',
        'available_for_review' => 'boolean',
        'available_full_plan' => 'boolean',
        'available_modular_plan' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Scope for active education levels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by plan type
     */
    public static function forPlan($planType)
    {
        $query = self::query();
        
        if ($planType === 'general') {
            $query->where('available_for_general', true);
        } elseif ($planType === 'professional') {
            $query->where('available_for_professional', true);
        } elseif ($planType === 'review') {
            $query->where('available_for_review', true);
        }
        
        return $query->orderBy('level_name');
    }

    /**
     * Get file requirements for this education level formatted for JavaScript
     */    public function getFileRequirementsForPlan($planType = null)
    {
        $requirements = $this->file_requirements ?: [];

        // Ensure requirements is an array
        if (is_string($requirements)) {
            $requirements = json_decode($requirements, true) ?: [];
        }
        if (!is_array($requirements)) {
            $requirements = [];
        }

        // Convert the requirements array to the format expected by JavaScript
        $formattedRequirements = [];

        foreach ($requirements as $requirement) {
            // Handle both object and array formats
            if (is_array($requirement)) {
                $formattedRequirements[] = [
                    'field_name' => $requirement['field_name'] ?? $requirement['document_type'] ?? 'unknown',
                    'display_name' => $requirement['description'] ?? $requirement['document_type'] ?? $requirement['field_name'] ?? 'Unknown Document',
                    'is_required' => $requirement['is_required'] ?? true,
                    'type' => 'file',
                    'document_type' => $requirement['document_type'] ?? $requirement['field_name'] ?? 'unknown'
                ];
            } else if (is_object($requirement)) {
                $formattedRequirements[] = [
                    'field_name' => $requirement->field_name ?? $requirement->document_type ?? 'unknown',
                    'display_name' => $requirement->description ?? $requirement->document_type ?? $requirement->field_name ?? 'Unknown Document',
                    'is_required' => $requirement->is_required ?? true,
                    'type' => 'file',
                    'document_type' => $requirement->document_type ?? $requirement->field_name ?? 'unknown'
                ];
            }
        }
        
        return $formattedRequirements;
    }
}
