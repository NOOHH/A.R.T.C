<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model
{
    use HasFactory;

    protected $table = 'content_items';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'content_title',
        'content_description',
        'lesson_id',
        'content_type',
        'content_data',
        'attachment_path',
        'max_points',
        'due_date',
        'time_limit',
        'content_order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'content_data' => 'array',
        'max_points' => 'decimal:2',
        'due_date' => 'datetime',
        'time_limit' => 'integer',
        'content_order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id', 'lesson_id');
    }

    // Accessors
    public function getTitleAttribute()
    {
        return $this->content_title;
    }

    public function getDescriptionAttribute()
    {
        return $this->content_description;
    }

    public function getTypeAttribute()
    {
        return $this->content_type;
    }

    public function getDataAttribute()
    {
        return $this->content_data;
    }

    public function getOrderAttribute()
    {
        return $this->content_order;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('content_order', 'asc');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('content_type', $type);
    }
}
