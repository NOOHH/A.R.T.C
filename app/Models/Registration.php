<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // Use 'registration_id' as primary key to match the actual table structure
    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'email', // Add email field
        'user_id',
        'package_id',
        'program_id',
        'plan_id',
        'package_name',
        'program_name',
        'plan_name',
        'enrollment_type',
        'learning_mode',
        'student_school',
        'school_name', // New dynamic field
        'street_address',
        'state_province',
        'city',
        'zipcode',
        'contact_number',
        'emergency_contact_number',
        'Telephone_Number', // New dynamic field
        'selected_modules', // New dynamic field
        'selected_courses', // Add course selections
        'Start_Date',
        'start_date', // Alternative field name
        'status',
        'good_moral',
        'PSA',
        'Course_Cert',
        'TOR',
        'Cert_of_Grad',
        'Undergraduate',
        'Graduate',
        'photo_2x2',
        'dynamic_fields',
        // Dynamic fields that can be activated/deactivated
        'phone_number',
        'telephone_number',
        'religion',
        'citizenship',
        'civil_status',
        'birthdate',
        'gender',
        'education_level',
        'work_experience',
        'preferred_schedule',
        'emergency_contact_relationship',
        'health_conditions',
        'disability_support',
        'valid_id',
        'birth_certificate',
        'diploma_certificate',
        'medical_certificate',
        'passport_photo',
        'parent_guardian_name',
        'parent_guardian_contact',
        'previous_school',
        'graduation_year',
        'course_taken',
        'special_needs',
        'scholarship_program',
        'employment_status',
        'monthly_income',
        // Custom education level file fields
        'ama_namin',
    ];

    // Explicitly guard against batch_id assignment - it should never be in registrations table
    protected $guarded = [
        'batch_id',
    ];

    protected $casts = [
        'dynamic_fields' => 'array',
        'selected_modules' => 'array', // Cast JSON field to array
        'selected_courses' => 'array', // Cast JSON field to array for course selections
    ];

    // Get display name for registration mode from dynamic fields
    public function getRegistrationModeDisplayAttribute()
    {
        $mode = $this->dynamic_fields['registration_mode'] ?? 'sync';
        return match($mode) {
            'sync' => 'Synchronous (Live Classes)',
            'async' => 'Asynchronous (Self-Paced)',
            default => 'Synchronous (Live Classes)',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'plan_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'registration_modules', 'registration_id', 'module_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'registration_id', 'registration_id');
    }

    /**
     * Get enrollments for a specific education level
     */
    public function enrollmentsForLevel($educationLevelId)
    {
        return $this->enrollments()->where('education_level_id', $educationLevelId);
    }

    /**
     * Check if user can enroll in a specific education level
     */
    public function canEnrollInLevel($educationLevelId)
    {
        // Check if already enrolled in this level
        $existingEnrollment = $this->enrollmentsForLevel($educationLevelId)->first();
        
        if ($existingEnrollment) {
            // Allow re-enrollment if previous enrollment was completed or cancelled
            return in_array($existingEnrollment->enrollment_status, ['completed', 'cancelled']);
        }

        return true;
    }
}
