<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'field_label', 
        'field_type',
        'program_type',
        'is_required',
        'is_active',
        'field_options',
        'validation_rules',
        'sort_order'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForProgram($query, $programType)
    {
        return $query->where(function($q) use ($programType) {
            $q->where('program_type', $programType)
              ->orWhere('program_type', 'both');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
