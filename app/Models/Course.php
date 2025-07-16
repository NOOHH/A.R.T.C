<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    protected $primaryKey = 'subject_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'subject_name',
        'subject_description', 
        'module_id',
        'subject_price',
        'subject_order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'subject_price' => 'decimal:2',
        'subject_order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'modules_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id', 'subject_id');
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class, 'course_id', 'subject_id');
    }

    // Accessors
    public function getIdAttribute()
    {
        return $this->subject_id;
    }

    public function getNameAttribute()
    {
        return $this->subject_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->subject_description;
    }

    public function getPriceAttribute()
    {
        return $this->subject_price;
    }

    public function getOrderAttribute()
    {
        return $this->subject_order;
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
        return $query->orderBy('subject_order', 'asc');
    }
}
