<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthFormField extends Model
{
    protected $fillable = [
        'form',
        'field_key',
        'label',
        'type',
        'is_required',
        'is_enabled',
        'placeholder',
        'help_text',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_enabled' => 'boolean',
        'options' => 'array',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeForForm($query, string $form)
    {
        return $query->where('form', $form);
    }
}
