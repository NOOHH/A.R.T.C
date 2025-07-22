<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';
    protected $primaryKey = 'enrollment_id';
    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'user_id',
        'program_id',
        'package_id',
        'enrollment_type',
        'learning_mode',
        'registration_id',
        'enrollment_status',
        'payment_status',
        'batch_id',
        'batch_access_granted',
        'individual_start_date',
        'individual_end_date',
        'education_level_id',
        'inherited_registration_data',
        'inheritance_metadata',
        'progression_stage',
        'education_level_started_at',
        'education_level_completed_at',
    ];

    protected $casts = [
        'inherited_registration_data' => 'array',
        'inheritance_metadata' => 'array',
        'individual_start_date' => 'datetime',
        'individual_end_date' => 'datetime',
        'education_level_started_at' => 'datetime',
        'education_level_completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id', 'batch_id');
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id', 'id');
    }

    /**
     * Get the courses enrolled through this enrollment
     */
    public function enrollmentCourses()
    {
        return $this->hasMany(EnrollmentCourse::class, 'enrollment_id', 'enrollment_id');
    }

    /**
     * Get the courses that this enrollment has access to
     */
    public function accessibleCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollment_courses', 'enrollment_id', 'course_id')
            ->withPivot(['module_id', 'enrollment_type', 'course_price', 'is_active'])
            ->wherePivot('is_active', true);
    }

    /**
     * Check if this enrollment has access to a specific course
     */
    public function hasAccessToCourse($courseId)
    {
        return $this->enrollmentCourses()
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Inherit data from the associated registration
     */
    public function inheritFromRegistration()
    {
        if (!$this->registration) {
            return false;
        }

        $registrationData = $this->registration->toArray();
        
        // Remove non-inheritable fields
        $excludeFields = [
            'registration_id', 'id', 'created_at', 'updated_at', 
            'status', 'user_id' // Keep original enrollment user_id
        ];
        
        $inheritableData = array_diff_key($registrationData, array_flip($excludeFields));
        
        // Store inherited data
        $this->inherited_registration_data = $inheritableData;
        
        // Store metadata about the inheritance
        $this->inheritance_metadata = [
            'inherited_at' => now(),
            'source_registration_id' => $this->registration_id,
            'fields_inherited' => array_keys($inheritableData),
            'education_level_id' => $this->education_level_id,
        ];
        
        return $this->save();
    }

    /**
     * Get a specific inherited field value
     */
    public function getInheritedField($fieldName)
    {
        return $this->inherited_registration_data[$fieldName] ?? null;
    }

    /**
     * Get all inherited data combined with enrollment-specific data
     */
    public function getCombinedData()
    {
        $enrollmentData = $this->toArray();
        $inheritedData = $this->inherited_registration_data ?? [];
        
        // Enrollment data takes precedence over inherited data
        return array_merge($inheritedData, $enrollmentData);
    }

    /**
     * Check if this enrollment allows progression to a higher education level
     */
    public function canProgressToLevel($educationLevelId)
    {
        if (!$this->educationLevel) {
            return false;
        }

        $targetLevel = EducationLevel::find($educationLevelId);
        if (!$targetLevel) {
            return false;
        }

        // Check if user has completed current level
        $isCompleted = $this->enrollment_status === 'completed' && 
                      $this->education_level_completed_at !== null;

        return $isCompleted;
    }

    /**
     * Create a progression enrollment to a higher education level
     */
    public function createProgression($educationLevelId, $programId = null, $packageId = null)
    {
        if (!$this->canProgressToLevel($educationLevelId)) {
            return null;
        }

        $newEnrollment = new self([
            'user_id' => $this->user_id,
            'registration_id' => $this->registration_id,
            'education_level_id' => $educationLevelId,
            'program_id' => $programId ?? $this->program_id,
            'package_id' => $packageId ?? $this->package_id,
            'enrollment_type' => $this->enrollment_type,
            'learning_mode' => $this->learning_mode,
            'progression_stage' => 'continuing',
            'enrollment_status' => 'pending',
            'education_level_started_at' => now(),
        ]);

        if ($newEnrollment->save()) {
            $newEnrollment->inheritFromRegistration();
            return $newEnrollment;
        }

        return null;
    }
}
