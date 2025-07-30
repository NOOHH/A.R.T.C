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
        'course_id',
        'content_type',
        'created_by_professor_id',
        'content_data',
        'content_url',
        'attachment_path',
        'file_path',
        'file_name',
        'file_size',
        'file_mime',
        'has_multiple_files',
        'is_archived',
        'archived_at',
        'archived_by_professor_id',
        'max_points',
        'due_date',
        'time_limit',
        'content_order',
        'order',
        'sort_order',
        'is_required',
        'is_active',
        'enable_submission',
        'allowed_file_types',
        'max_file_size',
        'submission_instructions',
        'allow_multiple_submissions',
    ];

    protected $casts = [
        'content_data' => 'array',
        'max_points' => 'decimal:2',
        'due_date' => 'datetime',
        'time_limit' => 'integer',
        'content_order' => 'integer',
        'sort_order' => 'integer',
        'max_file_size' => 'integer',
        'file_size' => 'integer',
        'has_multiple_files' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'enable_submission' => 'boolean',
        'allow_multiple_submissions' => 'boolean',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
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
