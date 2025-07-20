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
        // Lessons table is dropped - return an empty hasMany relationship that won't break eager loading
        return $this->hasMany(ContentItem::class, 'course_id', 'subject_id')->whereRaw('1 = 0'); // Always false condition
    }

    public function contentItems()
    {
        return $this->hasMany(ContentItem::class, 'course_id', 'subject_id');
    }

    public function adminOverride()
    {
        return $this->hasOne(AdminOverride::class, 'target_id')
            ->where('override_type', 'course');
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class, 'item_id')
            ->where('item_type', 'course');
    }

    public function enrollmentCourses()
    {
        return $this->hasMany(EnrollmentCourse::class, 'course_id', 'subject_id');
    }

    public function enrollments()
    {
        return $this->belongsToMany(Enrollment::class, 'enrollment_courses', 'course_id', 'enrollment_id')
            ->withPivot(['module_id', 'enrollment_type', 'course_price', 'is_active'])
            ->wherePivot('is_active', true);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_courses', 'course_id', 'package_id');
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

    // Override system helper methods
    public function isAccessibleTo($studentId = null)
    {
        // First check if parent module is accessible
        if ($this->module && !$this->module->isAccessibleTo($studentId)) {
            return false;
        }
        
        // Then check if admin has overridden this course
        return AdminOverride::isItemAccessible('course', $this->subject_id, $studentId);
    }

    public function getLockReasonFor($studentId = null)
    {
        // Check module first
        if ($this->module && !$this->module->isAccessibleTo($studentId)) {
            return $this->module->getLockReasonFor($studentId);
        }
        
        return AdminOverride::getItemLockReason('course', $this->subject_id, $studentId);
    }

    public function isCompletedBy($studentId)
    {
        return StudentProgress::isCompleted($studentId, 'course', $this->subject_id);
    }

    public function getProgressFor($studentId)
    {
        return StudentProgress::getProgress($studentId, 'course', $this->subject_id);
    }

    public function markCompletedBy($studentId, $completionData = null)
    {
        return StudentProgress::markItemCompleted($studentId, 'course', $this->subject_id, $completionData);
    }
}
