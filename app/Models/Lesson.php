<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';
    protected $primaryKey = 'lesson_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'lesson_name',
        'lesson_description',
        'course_id',
        'lesson_price',
        'lesson_duration',
        'lesson_video_url',
        'lesson_order',
        'is_required',
        'is_active',
        'learning_mode',
    ];

    protected $casts = [
        'lesson_price' => 'decimal:2',
        'lesson_order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'subject_id');
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class, 'lesson_id', 'lesson_id');
    }

    // Accessors
    public function getIdAttribute()
    {
        return $this->lesson_id;
    }

    public function getNameAttribute()
    {
        return $this->lesson_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->lesson_description;
    }

    public function getPriceAttribute()
    {
        return $this->lesson_price;
    }

    public function getOrderAttribute()
    {
        return $this->lesson_order;
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
        return $query->orderBy('lesson_order', 'asc');
    }
}
