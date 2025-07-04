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
        'user_id',
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
        'Start_Date',
        'status',
        'package_id',
        'package_name',
        'plan_id',
        'plan_name',
        'program_id',
        'program_name',
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
    ];

    protected $casts = [
        'dynamic_fields' => 'array',
        'selected_modules' => 'array', // Cast JSON field to array
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

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'registration_modules', 'registration_id', 'module_id');
    }
}
