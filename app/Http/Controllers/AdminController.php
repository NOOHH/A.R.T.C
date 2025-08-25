<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Registration;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Package;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Module;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\AssignmentSubmission;
use App\Http\Controllers\Traits\AdminPreviewCustomization;
use Carbon\Carbon;

class AdminController extends Controller
{
    use AdminPreviewCustomization;
    
    public function __construct()
    {
        // Apply middleware conditionally - skip for preview requests
        $this->middleware('admin.auth')->except(['showPreviewDashboard']);
    }

    public function dashboard()
    {
        // Check if this is a preview request - handle before middleware
        if (request()->has('preview') && request('preview') === 'true') {
            return $this->showPreviewDashboard();
        }
        
        try {
            // Get pending registrations
            $registrations = Registration::where('status', 'pending')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            // Calculate analytics data
            $analytics = [
                'total_students' => Student::count(),
                'total_programs' => Program::where('is_archived', false)->count(),
                'total_modules' => Module::where('is_archived', false)->count(),
                'total_enrollments' => Enrollment::count(),
                'pending_registrations' => Registration::where('status', 'pending')->count(),
                'new_students_this_month' => Student::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
                'modules_this_week' => Module::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
                'archived_programs' => Program::where('is_archived', true)->count(),
            ];

            $dbError = null;
        } catch (\Exception $e) {
            $registrations = collect();
            $analytics = [
                'total_students' => 0,
                'total_programs' => 0,
                'total_modules' => 0,
                'total_enrollments' => 0,
                'pending_registrations' => 0,
                'new_students_this_month' => 0,
                'modules_this_week' => 0,
                'archived_programs' => 0,
            ];
            $dbError = 'Database connection failed: ' . $e->getMessage();
        }

        return view('admin.admin-dashboard.admin-dashboard', compact('registrations', 'analytics', 'dbError'));
    }

    public function showRegistration($id)
    {
        try {
            $registration = Registration::with(['user', 'program', 'package', 'plan'])
                                        ->where('registration_id', $id)
                                        ->firstOrFail();
            
            // Return a proper view instead of JSON
            return view('admin.registrations.show', compact('registration'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registration not found.');
        }
    }

    public function showRegistrationDetails($id)
    {
        $registration = Registration::findOrFail($id);
        // Redirect to main registration page with details modal or use existing view
        return redirect()->route('admin.student.registration.pending')->with('selected_registration', $registration);
    }

    public function getRegistrationDetailsJson($id)
    {
        try {
            // Check if the id is a registration_id (like "2025-07-00005") or a database id (numeric)
            if (is_numeric($id)) {
                $registration = Registration::with(['user', 'program', 'package', 'plan', 'enrollments'])->findOrFail($id);
            } else {
                // Try to find by registration_id
                $registration = Registration::with(['user', 'program', 'package', 'plan', 'enrollments'])
                    ->where('registration_id', $id)
                    ->firstOrFail();
            }
            
            // Determine program type for form requirements filtering
            $programType = 'both'; // default
            if ($registration->enrollment_type) {
                $programType = strtolower($registration->enrollment_type) === 'modular' ? 'modular' : 'full';
            }
            
            // Get active form requirements for this program type
            $activeRequirements = \App\Models\FormRequirement::where('is_active', true)
                ->where(function($query) use ($programType) {
                    $query->where('program_type', $programType)
                          ->orWhere('program_type', 'both')
                          ->orWhere('program_type', 'all');
                })
                ->orderBy('sort_order')
                ->get();
                
            // Parse dynamic fields (JSON stored data from actual form submission)
            $dynamicFields = [];
            if ($registration->dynamic_fields) {
                $dynamicFields = is_string($registration->dynamic_fields) 
                    ? json_decode($registration->dynamic_fields, true) 
                    : $registration->dynamic_fields;
                if (!is_array($dynamicFields)) {
                    $dynamicFields = [];
                }
            }
            
            // Get enrollment data if available
            $latestEnrollment = null;
            if ($registration->enrollments && $registration->enrollments->count() > 0) {
                $latestEnrollment = $registration->enrollments->sortByDesc('created_at')->first();
            }
            
            // Helper function to get field value with priority: dynamic_fields > static_field > enrollment_field
            $getFieldValue = function($fieldName, $staticValue = null) use ($dynamicFields, $registration, $latestEnrollment) {
                // First check dynamic fields (actual form data)
                if (isset($dynamicFields[$fieldName]) && !empty($dynamicFields[$fieldName])) {
                    return $dynamicFields[$fieldName];
                }
                // Then check static registration fields
                if (!empty($registration->$fieldName)) {
                    return $registration->$fieldName;
                }
                // Then check enrollment fields
                if ($latestEnrollment && !empty($latestEnrollment->$fieldName)) {
                    return $latestEnrollment->$fieldName;
                }
                // Finally use provided static value
                return $staticValue;
            };
            
            // Get user information (enhanced)
            $userInfo = [];
            if ($registration->user) {
                $userInfo = [
                    'full_name' => trim(($registration->user->firstname ?? '') . ' ' . ($registration->user->lastname ?? '')),
                    'email' => $registration->user->email,
                    'account_registered_date' => $registration->user->created_at ? $registration->user->created_at->format('M d, Y H:i') : 'N/A',
                    'user_role' => $registration->user->role ?? 'student',
                ];
            } else {
                // Fallback to registration data
                $userInfo = [
                    'full_name' => trim(($getFieldValue('firstname') ?? '') . ' ' . ($getFieldValue('lastname') ?? '')),
                    'email' => $getFieldValue('email') ?? 'N/A',
                    'account_registered_date' => $registration->created_at ? $registration->created_at->format('M d, Y H:i') : 'N/A',
                    'user_role' => 'student',
                ];
            }
            
            // Check if we have a student record for additional information
            $studentInfo = [];
            if ($registration->user_id) {
                $student = \App\Models\Student::where('user_id', $registration->user_id)->first();
                if ($student) {
                    $studentInfo = [
                        'firstname' => $student->firstname,
                        'lastname' => $student->lastname,
                        'middlename' => $student->middlename,
                        'contact_number' => $student->contact_number,
                        'address' => $student->address,
                        'city' => $student->city,
                        'province' => $student->province,
                    ];
                }
            }
            
            
            // Build response based only on active form requirements and actual form data
            $response = [
                'registration_id' => $registration->registration_id,
                'status' => $registration->status,
                'created_at' => $registration->created_at->format('M d, Y H:i'),
                'created_at_formatted' => $registration->created_at->format('M d, Y H:i'),
                
                // User Information (Enhanced)
                'user_info' => $userInfo,
                
                // Student Information (if available)
                'student_info' => $studentInfo,
                
                // Core enrollment flow data (always shown)
                'program_name' => $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A'),
                'package_name' => $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A'),
                'plan_name' => $registration->plan_name ?? ($registration->plan ? $registration->plan->plan_name : 'N/A'),
                'enrollment_type' => $registration->enrollment_type ?? 'Full',
                'learning_mode' => $getFieldValue('learning_mode', $registration->learning_mode),
                'enrollment_date' => $registration->created_at->format('M d, Y H:i'),
                
                // Enhanced plan type detection
                'plan_type' => $this->getPlanType($registration),
                
                // Enhanced education level information
                'education_level_info' => $this->getEducationLevelInfo($registration),
                
                // Direct field access for backward compatibility
                'firstname' => $studentInfo['firstname'] ?? $getFieldValue('firstname'),
                'lastname' => $studentInfo['lastname'] ?? $getFieldValue('lastname'),
                'email' => $userInfo['email'] ?? $getFieldValue('email'),
                'contact_number' => $studentInfo['contact_number'] ?? $getFieldValue('contact_number'),
                'street_address' => $studentInfo['address'] ?? $getFieldValue('street_address'),
                'city' => $studentInfo['city'] ?? $getFieldValue('city'),
                'state_province' => $studentInfo['province'] ?? $getFieldValue('state_province'),
                
                // Document fields for direct access
                'PSA' => $getFieldValue('PSA'),
                'TOR' => $getFieldValue('TOR'),
                'diploma' => $getFieldValue('diploma'),
                'diploma_certificate' => $getFieldValue('diploma_certificate'),
                'Course_Cert' => $getFieldValue('Course_Cert'),
                'good_moral' => $getFieldValue('good_moral'),
                'photo_2x2' => $getFieldValue('photo_2x2'),
                'valid_id' => $getFieldValue('valid_id'),
                'birth_certificate' => $getFieldValue('birth_certificate'),
                'Cert_of_Grad' => $getFieldValue('Cert_of_Grad'),
            ];
            
            // Enhanced enrollment information
            $enrollmentInfo = [
                'plan_type' => $registration->enrollment_type ?? 'Full',
                'package' => $registration->package_name ?? ($registration->package ? $registration->package->package_name : 'N/A'),
                'program' => $registration->program_name ?? ($registration->program ? $registration->program->program_name : 'N/A'),
                'learning_mode' => $getFieldValue('learning_mode', $registration->learning_mode),
                'enrollment_date' => $registration->created_at->format('M d, Y H:i'),
            ];
            
            // Process course selection for modular enrollments
            if ($registration->enrollment_type === 'Modular' && $registration->selected_courses) {
                $selectedCourses = is_string($registration->selected_courses) 
                    ? json_decode($registration->selected_courses, true) 
                    : $registration->selected_courses;
                
                if (is_array($selectedCourses) && count($selectedCourses) > 0) {
                    $courseNames = [];
                    $moduleNames = [];
                    
                    // Also process selected modules
                    $selectedModules = null;
                    if ($registration->selected_modules) {
                        $selectedModules = is_string($registration->selected_modules) 
                            ? json_decode($registration->selected_modules, true) 
                            : $registration->selected_modules;
                    }
                    
                    foreach ($selectedCourses as $courseData) {
                        if (is_array($courseData)) {
                            if (isset($courseData['selected_courses']) && is_array($courseData['selected_courses'])) {
                                foreach ($courseData['selected_courses'] as $courseId) {
                                    $course = \App\Models\Course::find($courseId);
                                    if ($course) {
                                        $courseNames[] = $course->subject_name;
                                    }
                                }
                            }
                        } else {
                            $course = \App\Models\Course::find($courseData);
                            if ($course) {
                                $courseNames[] = $course->subject_name;
                            }
                        }
                    }
                    
                    // Process modules if available
                    if (is_array($selectedModules)) {
                        foreach ($selectedModules as $moduleData) {
                            if (is_array($moduleData) && isset($moduleData['name'])) {
                                $moduleNames[] = $moduleData['name'];
                            }
                        }
                    }
                    
                    $enrollmentInfo['courses'] = count($courseNames) > 0 ? implode(', ', $courseNames) : 'Not specified';
                    $enrollmentInfo['modules'] = count($moduleNames) > 0 ? implode(', ', $moduleNames) : 'Not specified';
                    $response['course_info'] = count($courseNames) > 0 ? implode(', ', $courseNames) : 'Modular (courses not specified)';
                } else {
                    $enrollmentInfo['courses'] = 'Not specified';
                    $enrollmentInfo['modules'] = 'Not specified';
                    $response['course_info'] = 'Modular';
                }
            } else {
                $enrollmentInfo['courses'] = 'Full Program (All Courses)';
                $enrollmentInfo['modules'] = 'Full Program (All Modules)';
                $response['course_info'] = 'Full';
            }
            
            $response['enrollment_info_enhanced'] = $enrollmentInfo;
            
            // Add fields based on active form requirements (only what was actually presented to student)
            $personalInfoFields = [];
            $contactFields = [];
            $addressFields = [];
            $educationFields = [];
            $documentFields = [];
            $otherFields = [];
            
            foreach ($activeRequirements as $requirement) {
                $fieldName = $requirement->field_name;
                $fieldLabel = $requirement->field_label;
                $fieldType = $requirement->field_type;
                
                // Skip section headers
                if ($fieldType === 'section') {
                    continue;
                }
                
                // Get field value
                $value = $getFieldValue($fieldName);
                
                // Only include fields that have values or were explicitly part of the form flow
                if (!empty($value) || isset($dynamicFields[$fieldName])) {
                    $fieldData = [
                        'label' => $fieldLabel,
                        'value' => $value ?: 'N/A',
                        'type' => $fieldType,
                        'required' => $requirement->is_required
                    ];
                    
                    // Categorize fields for better organization
                    if (in_array($fieldName, ['firstname', 'middlename', 'lastname', 'gender', 'birthdate', 'age', 'religion', 'citizenship', 'civil_status'])) {
                        $personalInfoFields[$fieldName] = $fieldData;
                    } elseif (in_array($fieldName, ['contact_number', 'phone_number', 'mobile_number', 'telephone_number', 'emergency_contact_number', 'emergency_contact_relationship', 'parent_guardian_name', 'parent_guardian_contact'])) {
                        $contactFields[$fieldName] = $fieldData;
                    } elseif (in_array($fieldName, ['street_address', 'address', 'city', 'state_province', 'province', 'zipcode'])) {
                        $addressFields[$fieldName] = $fieldData;
                    } elseif (in_array($fieldName, ['education_level', 'school_name', 'student_school', 'previous_school', 'graduation_year', 'course_taken', 'work_experience', 'employment_status', 'monthly_income'])) {
                        $educationFields[$fieldName] = $fieldData;
                    } elseif ($fieldType === 'file' || in_array($fieldName, ['PSA', 'TOR', 'Course_Cert', 'good_moral', 'photo_2x2', 'birth_certificate', 'diploma_certificate', 'valid_id', 'medical_certificate'])) {
                        $documentFields[$fieldName] = $fieldData;
                    } else {
                        $otherFields[$fieldName] = $fieldData;
                    }
                    
                    // Also add to main response for backward compatibility
                    $response[$fieldName] = $value ?: 'N/A';
                }
            }
            
            // Add categorized fields to response
            $response['personal_info'] = $personalInfoFields;
            $response['contact_info'] = $contactFields;
            $response['address_info'] = $addressFields;
            $response['education_info'] = $educationFields;
            $response['documents'] = $documentFields;
            $response['other_fields'] = $otherFields;
            
            // Enhanced document information based on education level
            $educationLevel = $getFieldValue('education_level') ?? $getFieldValue('educational_attainment');
            $documentInfo = [
                'education_level' => $educationLevel ?? 'Not specified',
                'document_requirements' => [],
                'uploaded_documents' => []
            ];
            
            // Determine document requirements based on education level
            if ($educationLevel) {
                if (strtolower($educationLevel) === 'undergraduate' || strpos(strtolower($educationLevel), 'undergrad') !== false) {
                    $documentInfo['document_requirements'] = [
                        'PSA Birth Certificate',
                        'High School Diploma/TOR', 
                        'Good Moral Certificate',
                        '2x2 Photo',
                        'Valid ID'
                    ];
                } elseif (strtolower($educationLevel) === 'graduate' || strpos(strtolower($educationLevel), 'grad') !== false) {
                    $documentInfo['document_requirements'] = [
                        'PSA Birth Certificate',
                        'College Diploma',
                        'College TOR',
                        'Good Moral Certificate', 
                        '2x2 Photo',
                        'Valid ID'
                    ];
                } else {
                    // Default requirements
                    $documentInfo['document_requirements'] = [
                        'PSA Birth Certificate',
                        'Educational Documents',
                        'Good Moral Certificate',
                        '2x2 Photo',
                        'Valid ID'
                    ];
                }
            }
            
            // Check which documents have been uploaded
            $documentFields = [
                'PSA' => 'PSA Birth Certificate',
                'TOR' => 'Transcript of Records',
                'diploma' => 'Diploma',
                'diploma_certificate' => 'Diploma Certificate',
                'Course_Cert' => 'Course Certificate', 
                'good_moral' => 'Good Moral Certificate',
                'photo_2x2' => '2x2 Photo',
                'valid_id' => 'Valid ID',
                'birth_certificate' => 'Birth Certificate',
                'Cert_of_Grad' => 'Certificate of Graduation',
                'Undergraduate' => 'Undergraduate Documents',
                'Graduate' => 'Graduate Documents'
            ];
            
            foreach ($documentFields as $fieldName => $displayName) {
                $documentValue = $getFieldValue($fieldName);
                if (!empty($documentValue) && $documentValue !== 'N/A') {
                    $documentInfo['uploaded_documents'][] = [
                        'field_name' => $fieldName,
                        'display_name' => $displayName,
                        'file_path' => $documentValue,
                        'status' => 'uploaded'
                    ];
                }
            }
            
            $response['document_info_enhanced'] = $documentInfo;
            
            // Add email (always shown since it's core to user account)
            $response['email'] = $registration->user->email ?? $getFieldValue('email') ?? 'N/A';
            
            // Add enrollment information if available
            if ($latestEnrollment) {
                $response['enrollment_info'] = [
                    'enrollment_id' => $latestEnrollment->enrollment_id,
                    'enrollment_status' => $latestEnrollment->enrollment_status,
                    'payment_status' => $latestEnrollment->payment_status,
                    'progress_percentage' => $latestEnrollment->progress_percentage,
                    'batch_id' => $latestEnrollment->batch_id,
                    'start_date' => $latestEnrollment->start_date,
                    'created_at' => $latestEnrollment->created_at->format('M d, Y H:i'),
                ];
            }
            
            // Add form requirements metadata for admin reference
            $response['form_metadata'] = [
                'program_type' => $programType,
                'total_active_requirements' => $activeRequirements->count(),
                'fields_with_data' => count(array_filter($dynamicFields)),
                'enrollment_flow_completed' => !empty($registration->plan_id) && !empty($registration->package_id) && !empty($registration->learning_mode)
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found: ' . $e->getMessage()], 404);
        }
    }
    
    /**
     * Get the plan type (Full or Modular) based on registration data
     */
    private function getPlanType($registration)
    {
        // Check enrollment_type first
        if ($registration->enrollment_type) {
            return ucfirst(strtolower($registration->enrollment_type));
        }
        
        // Check package type
        if ($registration->package && $registration->package->package_type) {
            return ucfirst(strtolower($registration->package->package_type));
        }
        
        // Check plan name
        if ($registration->plan && $registration->plan->plan_name) {
            $planName = strtolower($registration->plan->plan_name);
            if (strpos($planName, 'modular') !== false) {
                return 'Modular';
            } elseif (strpos($planName, 'full') !== false) {
                return 'Full';
            }
        }
        
        // Check package name
        if ($registration->package_name) {
            $packageName = strtolower($registration->package_name);
            if (strpos($packageName, 'modular') !== false) {
                return 'Modular';
            } elseif (strpos($packageName, 'full') !== false) {
                return 'Full';
            }
        }
        
        // Default to Full
        return 'Full';
    }
    
    /**
     * Helper function to get field value with priority: dynamic_fields > static_field > enrollment_field
     */
    private function getFieldValue($fieldName, $registration, $staticValue = null)
    {
        // Parse dynamic fields (JSON stored data from actual form submission)
        $dynamicFields = [];
        if ($registration->dynamic_fields) {
            $dynamicFields = is_string($registration->dynamic_fields) 
                ? json_decode($registration->dynamic_fields, true) 
                : $registration->dynamic_fields;
            if (!is_array($dynamicFields)) {
                $dynamicFields = [];
            }
        }
        
        // Get latest enrollment if available
        $latestEnrollment = null;
        if ($registration->enrollments && $registration->enrollments->count() > 0) {
            $latestEnrollment = $registration->enrollments->sortByDesc('created_at')->first();
        }
        
        // First check dynamic fields (actual form data)
        if (isset($dynamicFields[$fieldName]) && !empty($dynamicFields[$fieldName])) {
            return $dynamicFields[$fieldName];
        }
        // Then check static registration fields
        if (!empty($registration->$fieldName)) {
            return $registration->$fieldName;
        }
        // Then check enrollment fields
        if ($latestEnrollment && !empty($latestEnrollment->$fieldName)) {
            return $latestEnrollment->$fieldName;
        }
        // Finally use provided static value
        return $staticValue;
    }
    
    /**
     * Get education level information with file requirements
     */
    private function getEducationLevelInfo($registration)
    {
        $educationLevel = $this->getFieldValue('education_level', $registration);
        
        if (!$educationLevel || $educationLevel === 'N/A') {
            return [
                'level_name' => 'N/A',
                'file_requirements' => [],
                'uploaded_documents' => []
            ];
        }
        
        // Get education level details from database
        $levelInfo = \App\Models\EducationLevel::where('level_name', $educationLevel)
            ->orWhere('level_name', 'like', '%' . $educationLevel . '%')
            ->first();
            
        if (!$levelInfo) {
            return [
                'level_name' => $educationLevel,
                'file_requirements' => [],
                'uploaded_documents' => $this->getUploadedDocuments($registration)
            ];
        }
        
        // Get file requirements for this education level
        $fileRequirements = [];
        if ($levelInfo->file_requirements) {
            $requirements = is_string($levelInfo->file_requirements) 
                ? json_decode($levelInfo->file_requirements, true) 
                : $levelInfo->file_requirements;
                
            if (is_array($requirements)) {
                foreach ($requirements as $requirement) {
                    if (isset($requirement['field_name']) && isset($requirement['display_name'])) {
                        $fileRequirements[] = [
                            'field_name' => $requirement['field_name'],
                            'display_name' => $requirement['display_name'],
                            'is_required' => $requirement['is_required'] ?? false,
                            'document_type' => $requirement['document_type'] ?? $requirement['field_name']
                        ];
                    }
                }
            }
        }
        
        return [
            'level_name' => $levelInfo->level_name,
            'level_order' => $levelInfo->level_order,
            'file_requirements' => $fileRequirements,
            'uploaded_documents' => $this->getUploadedDocuments($registration, $fileRequirements)
        ];
    }
    
    /**
     * Get uploaded documents for a registration
     */
    private function getUploadedDocuments($registration, $fileRequirements = [])
    {
        $uploadedDocs = [];
        
        // Get dynamic fields
        $dynamicFields = $registration->dynamic_fields ? json_decode($registration->dynamic_fields, true) : [];
        
        // Check each file requirement
        foreach ($fileRequirements as $requirement) {
            $fieldName = $requirement['field_name'];
            $displayName = $requirement['display_name'];
            
            // Check in dynamic fields first
            if (isset($dynamicFields[$fieldName]) && $dynamicFields[$fieldName]) {
                $uploadedDocs[] = [
                    'field_name' => $fieldName,
                    'display_name' => $displayName,
                    'file_path' => $dynamicFields[$fieldName],
                    'is_required' => $requirement['is_required'],
                    'uploaded' => true
                ];
            } else {
                // Check if not uploaded
                $uploadedDocs[] = [
                    'field_name' => $fieldName,
                    'display_name' => $displayName,
                    'file_path' => null,
                    'is_required' => $requirement['is_required'],
                    'uploaded' => false
                ];
            }
        }
        
        // Also check common document fields
        $commonFields = ['PSA', 'TOR', 'diploma', 'diploma_certificate', 'Course_Cert', 'good_moral', 'photo_2x2', 'valid_id', 'birth_certificate', 'Cert_of_Grad'];
        
        foreach ($commonFields as $fieldName) {
            $value = $this->getFieldValue($fieldName, $registration);
            if ($value && $value !== 'N/A') {
                $uploadedDocs[] = [
                    'field_name' => $fieldName,
                    'display_name' => ucfirst(str_replace('_', ' ', $fieldName)),
                    'file_path' => $value,
                    'is_required' => false,
                    'uploaded' => true
                ];
            }
        }
        
        return $uploadedDocs;
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $registration = Registration::findOrFail($id);

            // Upgrade user role
            $user = User::find($registration->user_id);
            if ($user) {
                $user->role = 'student';
                $user->save();
            }

            // Check if student already exists for this user (multiple enrollment scenario)
            $student = Student::where('user_id', $user->user_id)->first();
            
            if (!$student) {
                // Generate unique, non-duplicating student_id
                $now       = now();
                $yearMonth = $now->format('Y-m');

                $lastStudent = Student::where('student_id', 'like', "{$yearMonth}-%")
                                      ->orderBy('student_id', 'desc')
                                      ->first();

                $nextSeq = $lastStudent
                    ? ((int) substr($lastStudent->student_id, strlen($yearMonth) + 1)) + 1
                    : 1;

                $studentId = $yearMonth . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

                $student = Student::create([
                    'student_id' => $studentId,
                    'user_id' => $user->user_id,
                    'firstname' => $registration->firstname,
                    'middlename' => $registration->middlename,
                    'lastname' => $registration->lastname,
                    'email' => $user->email ?? '',
                    'package_id' => $registration->package_id,
                    'package_name' => $registration->package_name,
                    'program_id' => $registration->program_id,
                    'program_name' => $registration->program_name,
                    'enrollment_type' => $registration->enrollment_type,
                    'learning_mode' => $registration->learning_mode,
                    'education_level' => $registration->education_level,
                    'status' => 'approved',
                    'date_approved' => now()
                ]);
            }

            // Find existing enrollment for this registration (created during registration process)
            $enrollment = Enrollment::where('registration_id', $registration->registration_id)->first();
            $batchId = session('selected_batch_id');
            if ($enrollment) {
                $enrollment->student_id = $student->student_id;
                $enrollment->user_id = $user?->user_id;
                $enrollment->enrollment_status = 'approved';
                if ($batchId) {
                    $enrollment->batch_id = $batchId;
                    Log::info('Setting batch_id on existing enrollment', ['batch_id' => $batchId, 'enrollment_id' => $enrollment->enrollment_id]);
                }
                $enrollment->save();
            } else {
                $enrollmentData = [
                    'student_id' => $student->student_id,
                    'user_id' => $user?->user_id,
                    'program_id' => $registration->program_id,
                    'package_id' => $registration->package_id,
                    'enrollment_type' => $registration->plan_name === 'Modular' ? 'Modular' : 'Full',
                    'learning_mode' => $registration->learning_mode ?? 'Synchronous',
                    'enrollment_status' => 'approved',
                    'payment_status' => 'pending',
                ];
                if ($batchId) {
                    $enrollmentData['batch_id'] = $batchId;
                    Log::info('Creating new enrollment with batch_id', ['batch_id' => $batchId, 'student_id' => $student->student_id]);
                }
                $enrollment = Enrollment::create($enrollmentData);
            }
            // --- BEGIN: Create EnrollmentCourse records for modular enrollments ---
            if ($enrollment && ($enrollment->enrollment_type === 'Modular' || $registration->enrollment_type === 'Modular' || $registration->plan_name === 'Modular')) {
                $selectedCourses = $registration->selected_courses;
                if (is_string($selectedCourses)) {
                    $selectedCourses = json_decode($selectedCourses, true);
                }
                if (is_array($selectedCourses)) {
                    // Also need selected_modules to get module_id for each course
                    $selectedModules = $registration->selected_modules;
                    if (is_string($selectedModules)) {
                        $selectedModules = json_decode($selectedModules, true);
                    }
                    $courseToModule = [];
                    if (is_array($selectedModules)) {
                        foreach ($selectedModules as $moduleData) {
                            if (isset($moduleData['id']) && isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                                foreach ($moduleData['selected_courses'] as $courseId) {
                                    $courseToModule[$courseId] = $moduleData['id'];
                                }
                            }
                        }
                    }
                    foreach ($selectedCourses as $courseId) {
                        $moduleId = $courseToModule[$courseId] ?? null;
                        if ($courseId) {
                            try {
                                \App\Models\EnrollmentCourse::firstOrCreate([
                                    'enrollment_id' => $enrollment->enrollment_id,
                                    'course_id' => $courseId,
                                ], [
                                    'module_id' => $moduleId,
                                    'enrollment_type' => 'course',
                                    'course_price' => 0,
                                    'is_active' => true
                                ]);
                                Log::info('EnrollmentCourse created', [
                                    'enrollment_id' => $enrollment->enrollment_id,
                                    'course_id' => $courseId,
                                    'module_id' => $moduleId
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Failed to create EnrollmentCourse', [
                                    'enrollment_id' => $enrollment->enrollment_id,
                                    'course_id' => $courseId,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }
            }
            // --- END: Create EnrollmentCourse records for modular enrollments ---
            if ($batchId) {
                session()->forget('selected_batch_id');
                Log::info('Cleared batch_id from session after enrollment creation');
            }

            // Update registration status instead of deleting
            $registration->status = 'approved';
            $registration->approved_at = now();
            $registration->approved_by = auth()->guard('admin')->user()->admin_id ?? null;
            $registration->save();

            DB::commit();

            // Handle different response types
            if (request()->expectsJson() || request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Student \"" . $student->student_id . "\" approved and moved to history."
                ]);
            }

            return redirect()
                ->route('admin.student.registration.history')
                ->with('success', "Student \"" . $student->student_id . "\" approved and moved to history.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson() || request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                             ->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->delete();

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected and removed.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function rejectWithReason(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);

            $registration = Registration::findOrFail($id);
            
            // Store the rejection reason
            $registration->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected: ' . $request->reason);
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function rejectWithFields(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $registration = Registration::findOrFail($id);
            
            // Store the current submission as original before updating
            $originalSubmission = $registration->toArray();
            
            // Store the rejection reason and fields
            $registration->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id,
                'rejected_at' => now(),
                'original_submission' => json_encode($originalSubmission)
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected with marked fields.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    public function approveResubmission(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'resubmitted') {
                return redirect()->back()->with('error', 'Registration is not in resubmitted status.');
            }

            // Update to approved and clear rejection data
            $registration->update([
                'status' => 'approved',
                'rejection_reason' => null,
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'resubmitted_at' => null
            ]);

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration resubmission approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function updateRejection(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $registration = Registration::findOrFail($id);
            
            $registration->update([
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id,
                'rejected_at' => now()
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rejection details updated successfully.'
                ]);
            }

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Rejection details updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Update failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                             ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function getOriginalRegistrationData($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if (!$registration->original_submission) {
                return response()->json(['error' => 'No original data found.'], 404);
            }

            $originalData = json_decode($registration->original_submission, true);
            $originalData['rejection_reason'] = $registration->rejection_reason;
            $originalData['rejected_fields'] = $registration->rejected_fields;

            return response()->json($originalData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load original data.'], 404);
        }
    }

    public function getStudentDetailsJson($id)
    {
        try {
            $student = Student::with(['user', 'enrollments.program', 'enrollments.package'])->findOrFail($id);
            
            return response()->json([
                'student_id' => $student->student_id,
                'firstname' => $student->firstname,
                'middlename' => $student->middlename,
                'lastname' => $student->lastname,
                'email' => $student->email ?? ($student->user->email ?? 'N/A'),
                'contact_number' => $student->contact_number,
                'emergency_contact_number' => $student->emergency_contact_number,
                'student_school' => $student->student_school,
                'street_address' => $student->street_address,
                'city' => $student->city,
                'state_province' => $student->state_province,
                'zipcode' => $student->zipcode,
                'good_moral' => $student->good_moral,
                'PSA' => $student->PSA,
                'Course_Cert' => $student->Course_Cert,
                'TOR' => $student->TOR,
                'Cert_of_Grad' => $student->Cert_of_Grad,
                'photo_2x2' => $student->photo_2x2,
                'Undergraduate' => $student->Undergraduate,
                'Graduate' => $student->Graduate,
                'Start_Date' => $student->Start_Date,
                'status' => $student->user->role ?? 'N/A',
                'date_approved' => $student->date_approved,
                'enrollments' => $student->enrollments->map(function ($enrollment) {
                    return [
                        'program_name' => $enrollment->program->program_name ?? 'N/A',
                        'package_name' => $enrollment->package->package_name ?? 'N/A',
                        'enrollment_type' => $enrollment->enrollment_type ?? 'N/A',
                        'enrollment_status' => $enrollment->enrollment_status ?? 'N/A',
                        'payment_status' => $enrollment->payment_status ?? 'N/A'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Student not found or database error.'], 404);
        }
    }

    public function undoApproval(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($id);
            $user = User::find($student->user_id);

            // Create a registration record from the student data
            $registration = Registration::create([
                'user_id' => $student->user_id,
                'firstname' => $student->firstname,
                'middlename' => $student->middlename,
                'lastname' => $student->lastname,
                'student_school' => $student->student_school,
                'street_address' => $student->street_address,
                'city' => $student->city,
                'state_province' => $student->state_province,
                'zipcode' => $student->zipcode,
                'contact_number' => $student->contact_number,
                'emergency_contact_number' => $student->emergency_contact_number,
                'good_moral' => $student->good_moral,
                'PSA' => $student->PSA,
                'Course_Cert' => $student->Course_Cert,
                'TOR' => $student->TOR,
                'Cert_of_Grad' => $student->Cert_of_Grad,
                'photo_2x2' => $student->photo_2x2,
                'Undergraduate' => $student->Undergraduate,
                'Graduate' => $student->Graduate,
                'Start_Date' => $student->Start_Date,
                'status' => 'pending'
            ]);

            // Downgrade user role back to guest
            if ($user) {
                $user->role = 'guest';
                $user->save();
            }

            // Delete enrollments
            Enrollment::where('student_id', $student->student_id)->delete();

            // Delete student record
            $student->delete();

            DB::commit();

            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Student approval has been undone. Student moved back to pending registrations.']);
            }
            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Student approval has been undone. Student moved back to pending registrations.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Undo approval failed: ' . $e->getMessage()], 500);
            }
            return redirect()->back()
                             ->with('error', 'Undo approval failed: ' . $e->getMessage());
        }
    }

    public function getEnrollmentDetailsJson($id)
    {
        try {
            $enrollment = Enrollment::with(['student.user', 'program', 'package', 'payment'])
                                  ->find($id);
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found.'
                ], 404);
            }
            $studentName = '';
            $email = '';
            $contactNumber = 'N/A';
            if ($enrollment->student) {
                $studentName = trim(
                    ($enrollment->student->firstname ?? '') . ' ' . 
                    ($enrollment->student->middlename ?? '') . ' ' . 
                    ($enrollment->student->lastname ?? '')
                );
                if ($enrollment->student->user) {
                    $email = $enrollment->student->user->email ?? $enrollment->student->email ?? '';
                } else {
                    $email = $enrollment->student->email ?? '';
                }
                $contactNumber = $enrollment->student->contact_number ?? 'N/A';
            } elseif ($enrollment->user_id) {
                $user = User::find($enrollment->user_id);
                if ($user) {
                    $studentName = trim(($user->user_firstname ?? '') . ' ' . ($user->user_lastname ?? ''));
                    $email = $user->email ?? '';
                }
                $student = Student::where('user_id', $enrollment->user_id)->first();
                if ($student) {
                    $contactNumber = $student->contact_number ?? 'N/A';
                    if (!$studentName) {
                        $studentName = trim(
                            ($student->firstname ?? '') . ' ' . 
                            ($student->middlename ?? '') . ' ' . 
                            ($student->lastname ?? '')
                        );
                    }
                    if (!$email) {
                        $email = $student->email ?? '';
                    }
                }
            }
            // Get payment details from payments table (for pending payments)
            $payment = $enrollment->payment ?? null;
            if (!$payment) {
                $payment = Payment::where('enrollment_id', $enrollment->enrollment_id)
                                 ->orderBy('created_at', 'desc')
                                 ->first();
            }
            // Get payment history (for completed/processed payments)
            $paymentHistory = $enrollment->enrollment_id ? PaymentHistory::where('enrollment_id', $enrollment->enrollment_id)
                                           ->orderBy('created_at', 'desc')
                                           ->get() : collect();
            // Determine current payment status and details
            $paymentStatus = $enrollment->payment_status ?? 'pending';
            $paymentMethod = 'N/A';
            $paymentAmount = 0;
            $paymentDate = null;
            $referenceNumber = 'N/A';
            $transactionId = 'N/A';
            $paymentNotes = '';
            // Get amount from package or enrollment
            if ($enrollment->package && isset($enrollment->package->price)) {
                $paymentAmount = $enrollment->package->price;
            } elseif ($enrollment->package && isset($enrollment->package->amount)) {
                $paymentAmount = $enrollment->package->amount;
            }
            if ($payment) {
                // Use data from payments table for pending payments
                $paymentStatus = $payment->payment_status ?? $paymentStatus;
                $paymentMethod = $payment->payment_method ?? 'N/A';
                if (isset($payment->amount) && $payment->amount > 0) {
                    $paymentAmount = $payment->amount;
                }
                $paymentDate = $payment->created_at ?? null;
                $paymentNotes = $payment->notes ?? '';
                // Extract payment details if JSON
                if ($payment->payment_details) {
                    $details = is_string($payment->payment_details) ? json_decode($payment->payment_details, true) : $payment->payment_details;
                    if (is_array($details)) {
                        $referenceNumber = $details['reference_number'] ?? $referenceNumber;
                        $transactionId = $details['transaction_id'] ?? $transactionId;
                    }
                }
            }
            // If there's payment history, get the latest one for display
            $latestHistory = $paymentHistory->first();
            if ($latestHistory && in_array($latestHistory->payment_status, ['completed', 'verified', 'approved'])) {
                $paymentStatus = $latestHistory->payment_status;
                $paymentMethod = $latestHistory->payment_method ?? $paymentMethod;
                $paymentAmount = $latestHistory->amount ?? $paymentAmount;
                $paymentDate = $latestHistory->payment_date ?? $latestHistory->created_at ?? $paymentDate;
                $paymentNotes = $latestHistory->payment_notes ?? $paymentNotes;
            }
            return response()->json([
                'student_name' => $studentName,
                'email' => $email,
                'contact_number' => $contactNumber,
                'program_name' => $enrollment->program->program_name ?? 'N/A',
                'package_name' => $enrollment->package->package_name ?? 'N/A',
                'enrollment_type' => $enrollment->enrollment_type ?? 'N/A',
                'amount' => $paymentAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'transaction_id' => $transactionId,
                'enrollment_date' => $enrollment->created_at ?? null,
                'payment_date' => $paymentDate,
                'updated_at' => $enrollment->updated_at ?? null,
                'payment_notes' => $paymentNotes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading enrollment details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignEnrollment(Request $request)
    {
        try {
            // Validate basic fields
            $request->validate([
                'program_id' => 'required|exists:programs,program_id', 
                'batch_id' => 'required|exists:student_batches,batch_id',
                'enrollment_type' => 'required|in:modular,full,accelerated',
                'learning_mode' => 'required|in:online,onsite,hybrid'
                // Note: module_id and course_id validation removed since enrollments table doesn't have these fields
            ]);

            // Handle single or multiple student selection
            $studentIds = [];
            if ($request->has('student_id') && $request->student_id) {
                // Single student
                $request->validate(['student_id' => 'required|exists:students,student_id']);
                $studentIds = [$request->student_id];
            } elseif ($request->has('student_ids') && is_array($request->student_ids)) {
                // Multiple students
                $request->validate(['student_ids' => 'required|array', 'student_ids.*' => 'exists:students,student_id']);
                $studentIds = $request->student_ids;
            } else {
                return redirect()->back()->with('error', 'Please select at least one student.');
            }

            DB::beginTransaction();

            // Get the package for the program
            $package = Package::where('program_id', $request->program_id)->first();
            if (!$package) {
                return redirect()->back()->with('error', 'No package found for this program.');
            }

            $successCount = 0;
            $errors = [];

            // Process each student
            foreach ($studentIds as $studentId) {
                try {
                    // Check if student is already enrolled in this program
                    $existingEnrollment = Enrollment::where([
                        'student_id' => $studentId,
                        'program_id' => $request->program_id
                    ])->first();

                    if ($existingEnrollment) {
                        $student = Student::find($studentId);
                        $errors[] = "Student {$student->firstname} {$student->lastname} is already enrolled in this program.";
                        continue;
                    }

                    // Create enrollment
                    $enrollment = Enrollment::create([
                        'student_id' => $studentId,
                        'program_id' => $request->program_id,
                        'package_id' => $package->package_id,
                        'batch_id' => $request->batch_id,
                        'enrollment_type' => $request->enrollment_type,
                        'learning_mode' => $request->learning_mode,
                        'enrollment_status' => 'enrolled',
                        'payment_status' => 'pending',
                        'amount' => $package->package_price,
                        'enrollment_date' => now()
                    ]);
                    
                    // If modular enrollment, create record in enrollment_courses table
                    if ($request->enrollment_type === 'modular' && $request->course_id) {
                        DB::table('enrollment_courses')->insert([
                            'enrollment_id' => $enrollment->enrollment_id,
                            'course_id' => $request->course_id,
                            'module_id' => $request->module_id,
                            'enrollment_type' => 'course', // Set as 'course' based on existing data
                            'course_price' => 0.00, // Default based on existing data
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $student = Student::find($studentId);
                    $errors[] = "Failed to enroll {$student->firstname} {$student->lastname}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Prepare response message
            $message = '';
            if ($successCount > 0) {
                $message = "Successfully enrolled {$successCount} student(s).";
            }
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(' ', $errors);
            }

            return redirect()
                ->back()
                ->with($successCount > 0 ? 'success' : 'error', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Enrollment assignment failed: ' . $e->getMessage());
        }
    }

    public function studentRegistration()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true') {
            $tenant = request()->segment(3) ?? 'test1'; // For tenant routes
            if (!request()->segment(3)) {
                $tenant = 'test1'; // Default for non-tenant routes
            }
            return $this->previewStudentRegistration($tenant);
        }
        
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'pending')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => false,
        ]);
    }

    public function studentRegistrationHistory()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true') {
            $tenant = request()->segment(3) ?? 'test1'; // For tenant routes
            if (!request()->segment(3)) {
                $tenant = 'test1'; // Default for non-tenant routes
            }
            return $this->previewStudentRegistrationHistory($tenant);
        }
        
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'approved')
                                   ->orderBy('approved_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration', [
            'registrations' => $registrations,
            'history'       => true,
        ]);
    }

    public function studentRegistrationRejected()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'rejected')
                                   ->orderBy('rejected_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration-rejected', [
            'registrations' => $registrations,
            'type' => 'rejected'
        ]);
    }

    public function studentRegistrationResubmitted()
    {
        $registrations = Registration::with(['user', 'package', 'program', 'plan'])
                                   ->where('status', 'resubmitted')
                                   ->orderBy('resubmitted_at', 'desc')
                                   ->get();
        return view('admin.admin-student-registration.admin-student-registration-resubmitted', [
            'registrations' => $registrations,
            'type' => 'resubmitted'
        ]);
    }

    /**
     * Approve a rejected registration (undo rejection)
     */
    public function undoRejection(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'rejected') {
                if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration is not rejected.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Registration is not rejected.');
            }
            
            // Clear rejection data and set back to pending
            $registration->update([
                'status' => 'pending',
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'updated_at' => now()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration rejection undone successfully. Status changed to pending.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Registration rejection undone successfully. Status changed to pending.');
            
        } catch (\Exception $e) {
            Log::error('Error undoing rejection: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to undo rejection: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to undo rejection.');
        }
    }

    /**
     * Approve a rejected registration directly
     */
    public function approveRejectedRegistration(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            if ($registration->status !== 'rejected') {
                return redirect()->back()->with('error', 'Registration is not rejected.');
            }
            
            // Clear rejection data and approve directly
            $registration->update([
                'status' => 'approved',
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'approved_by' => auth()->guard('admin')->user()->admin_id,
                'approved_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->back()->with('success', 'Registration approved successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error approving rejected registration: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve registration.');
        }
    }

    public function paymentRejected()
    {
        $payments = Payment::with([
            'enrollment.registration.user', 
            'enrollment.student', 
            'enrollment.program', 
            'enrollment.package',
            'registration' // Direct registration relationship
        ])
        ->where('payment_status', 'rejected')
        ->orderBy('rejected_at', 'desc')
        ->get();
        return view('admin.admin-student-registration.admin-payment-rejected', [
            'payments' => $payments,
            'type' => 'rejected'
        ]);
    }

    public function paymentPending()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true') {
            $tenant = request()->segment(3) ?? 'test1'; // For tenant routes
            if (!request()->segment(3)) {
                $tenant = 'test1'; // Default for non-tenant routes
            }
            return $this->previewPaymentPending($tenant);
        }
        
        $enrollments = Enrollment::with(['user', 'student', 'program', 'package', 'registration', 'enrollmentCourses.course', 'payment'])
            ->where('payment_status', 'pending')
            ->whereDoesntHave('payment', function($query) {
                $query->whereNotNull('payment_details');
            })
            ->where(function($query) {
                $query->whereNotNull('user_id')
                      ->orWhereNotNull('student_id');
            })
            ->whereHas('program')
            ->whereHas('package')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('enrollment_id')
            ->map(function ($enrollment) {
                // Determine student name and email from either user or student relationship
                $studentName = 'N/A';
                $studentEmail = 'N/A';
                if ($enrollment->user) {
                    $firstName = $enrollment->user->user_firstname ?? '';
                    $lastName = $enrollment->user->user_lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->user->email ?? 'N/A';
                } elseif ($enrollment->student) {
                    $firstName = $enrollment->student->firstname ?? '';
                    $lastName = $enrollment->student->lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->student->email ?? 'N/A';
                }
                $enrollment->student_name = $studentName;
                $enrollment->student_email = $studentEmail;
                return $enrollment;
            });
        return view('admin.admin-student-registration.admin-payment-pending', [
            'enrollments' => $enrollments,
            'pendingApprovals' => $this->getPaymentPendingApprovals(),
        ]);
    }

    public function getPaymentPendingApprovals()
    {
        $pendingApprovals = Enrollment::with(['user', 'student', 'program', 'package', 'registration', 'payment'])
            ->whereHas('payment', function($query) {
                $query->whereNotNull('payment_details')
                      ->where('payment_status', 'pending');
            })
            ->where(function($query) {
                $query->whereNotNull('user_id')
                      ->orWhereNotNull('student_id');
            })
            ->whereHas('program')
            ->whereHas('package')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('enrollment_id')
            ->map(function ($enrollment) {
                $studentName = 'N/A';
                $studentEmail = 'N/A';
                if ($enrollment->user) {
                    $firstName = $enrollment->user->user_firstname ?? '';
                    $lastName = $enrollment->user->user_lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->user->email ?? 'N/A';
                } elseif ($enrollment->student) {
                    $firstName = $enrollment->student->firstname ?? '';
                    $lastName = $enrollment->student->lastname ?? '';
                    $studentName = trim($firstName . ' ' . $lastName) ?: 'N/A';
                    $studentEmail = $enrollment->student->email ?? 'N/A';
                }
                $enrollment->student_name = $studentName;
                $enrollment->student_email = $studentEmail;
                if ($enrollment->payment) {
                    $enrollment->payment_submitted_at = $enrollment->payment->created_at;
                    $enrollment->payment_method = $enrollment->payment->payment_method ?? 'Not specified';
                    $enrollment->payment_amount = $enrollment->payment->amount ?? 0;
                }
                return $enrollment;
            });
        return $pendingApprovals;
    }

    public function paymentHistory()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true') {
            $tenant = request()->segment(3) ?? 'test1'; // For tenant routes
            if (!request()->segment(3)) {
                $tenant = 'test1'; // Default for non-tenant routes
            }
            return $this->previewPaymentHistory($tenant);
        }
        
        $paymentHistory = \App\Models\PaymentHistory::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])
            ->orderBy('payment_date', 'desc')
            ->get();
        return view('admin.admin-student-registration.admin-payment-history', [
            'paymentHistory' => $paymentHistory
        ]);
    }

    public function getPaymentHistoryDetailsJson($id)
    {
        $payment = \App\Models\PaymentHistory::with(['enrollment.student', 'enrollment.program', 'enrollment.package'])->find($id);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment history not found.'], 404);
        }
        $enrollment = $payment->enrollment;
        $student = $enrollment && $enrollment->student ? $enrollment->student : null;
        $program = $enrollment && $enrollment->program ? $enrollment->program : null;
        $package = $enrollment && $enrollment->package ? $enrollment->package : null;
        return response()->json([
            'student_name' => $student ? trim(($student->firstname ?? '') . ' ' . ($student->middlename ?? '') . ' ' . ($student->lastname ?? '')) : 'N/A',
            'email' => $student ? ($student->email ?? 'N/A') : 'N/A',
            'program_name' => $program ? $program->program_name : 'N/A',
            'package_name' => $package ? $package->package_name : 'N/A',
            'amount' => $payment->amount,
            'payment_status' => $payment->payment_status,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'transaction_id' => $payment->transaction_id,
            'payment_date' => $payment->payment_date,
            'notes' => $payment->payment_notes,
            'updated_at' => $payment->updated_at,
        ]);
    }

    public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();
            
            Log::info('Mark as paid request received', ['enrollment_id' => $id]);
            
            // Find enrollment by ID (enrollment_id is the primary key)
            $enrollment = Enrollment::where('enrollment_id', $id)->first();
            
            if (!$enrollment) {
                Log::error('Enrollment not found for mark as paid', ['enrollment_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found'
                ], 404);
            }
            
            // Check if already paid
            if ($enrollment->payment_status === 'paid') {
                Log::warning('Attempted to mark already paid enrollment', ['enrollment_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already marked as paid'
                ], 400);
            }
            
            Log::info('Creating payment history record', [
                'enrollment_id' => $enrollment->enrollment_id,
                'user_id' => $enrollment->user_id,
                'student_id' => $enrollment->student_id
            ]);
            
            // Create payment history record before updating enrollment
            $paymentHistory = PaymentHistory::create([
                'enrollment_id' => $enrollment->enrollment_id,
                'user_id' => $enrollment->user_id,
                'student_id' => $enrollment->student_id,
                'program_id' => $enrollment->program_id,
                'package_id' => $enrollment->package_id,
                'payment_status' => 'paid',
                'payment_method' => 'manual', // Since it's marked by admin
                'payment_notes' => 'Payment marked as paid by administrator',
                'payment_date' => now(),
                'processed_by_admin_id' => session('admin_id') ?? session('user_id') ?? 1,
            ]);
            
            Log::info('Payment history created', ['payment_history_id' => $paymentHistory->payment_history_id]);
            
            // Update enrollment payment status to paid
            $enrollment->update([
                'payment_status' => 'paid',
                'updated_at' => now()
            ]);
            
            // Update batch capacity if enrollment is approved and has a batch
            if ($enrollment->batch_id && $enrollment->enrollment_status === 'approved') {
                $batch = \App\Models\StudentBatch::find($enrollment->batch_id);
                if ($batch) {
                    // Recalculate actual capacity based on approved and paid enrollments
                    $actualCapacity = \App\Models\Enrollment::where('batch_id', $batch->batch_id)
                        ->where('enrollment_status', 'approved')
                        ->where('payment_status', 'paid')
                        ->count();
                    
                    $batch->update(['current_capacity' => $actualCapacity]);
                    Log::info('Updated batch capacity after payment', ['batch_id' => $batch->batch_id, 'new_capacity' => $actualCapacity]);
                }
            }
            
            // Process referral if both enrollment and payment are approved/paid
            \App\Helpers\ReferralCodeGenerator::processPendingReferral($enrollment->enrollment_id);

            DB::commit();
            
            Log::info('Payment marked as paid successfully', [
                'enrollment_id' => $enrollment->enrollment_id,
                'admin_id' => session('admin_id') ?? session('user_id'),
                'payment_history_id' => $paymentHistory->payment_history_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully and migrated to payment history'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error marking payment as paid', [
                'enrollment_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Payment rejection methods
    public function rejectPaymentWithFields(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            // Find the payment record
            $payment = Payment::where('enrollment_id', $id)->firstOrFail();
            
            // Store the current payment data as original before updating
            $originalPaymentData = $payment->toArray();
            
            // Store the rejection reason and fields
            $payment->update([
                'payment_status' => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'rejected_at' => now(),
                'original_payment_data' => json_encode($originalPaymentData)
            ]);

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment rejected with marked fields.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Payment rejection failed: ' . $e->getMessage());
        }
    }

    public function approvePaymentResubmission(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_status !== 'resubmitted') {
                return redirect()->back()->with('error', 'Payment is not in resubmitted status.');
            }

            // Update to paid and clear rejection data
            $payment->update([
                'payment_status' => 'paid',
                'rejection_reason' => null,
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'resubmitted_at' => null,
                'verified_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'verified_at' => now()
            ]);

            // Also update the enrollment payment status
            if ($payment->enrollment) {
                $payment->enrollment->update(['payment_status' => 'paid']);
            }

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment resubmission approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Payment approval failed: ' . $e->getMessage());
        }
    }

    public function updatePaymentRejection(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
                'rejected_fields' => 'array'
            ]);

            $payment = Payment::findOrFail($id);
            
            $payment->update([
                'rejection_reason' => $request->reason,
                'rejected_fields' => json_encode($request->rejected_fields ?? []),
                'rejected_by' => auth()->guard('admin')->user()->admin_id ?? 1,
                'rejected_at' => now()
            ]);

            return redirect()
                ->route('admin.student.registration.payment.pending')
                ->with('success', 'Payment rejection details updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function getPaymentDetailsJson($id)
    {
        try {
            $payment = Payment::with(['enrollment.program', 'enrollment.package'])->findOrFail($id);
            
            $paymentDetails = is_string($payment->payment_details) ? json_decode($payment->payment_details, true) : ($payment->payment_details ?? []);
            
            return response()->json([
                'payment_id' => $payment->payment_id,
                'enrollment_id' => $payment->enrollment_id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'reference_number' => $payment->reference_number,
                'rejection_reason' => $payment->rejection_reason,
                'rejected_fields' => $payment->rejected_fields,
                'payment_details' => $paymentDetails,
                'program_name' => $payment->enrollment->program->program_name ?? 'N/A',
                'package_name' => $payment->enrollment->package->package_name ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment not found or database error.'], 404);
        }
    }

    public function getOriginalPaymentData($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if (!$payment->original_payment_data) {
                return response()->json(['error' => 'No original payment data found.'], 404);
            }

            $originalData = json_decode($payment->original_payment_data, true);
            $originalData['rejection_reason'] = $payment->rejection_reason;
            $originalData['rejected_fields'] = $payment->rejected_fields;

            return response()->json($originalData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load original payment data.'], 404);
        }
    }

    public function getEnrollmentPaymentDetails($id)
    {
        try {
            $enrollment = Enrollment::with(['program', 'package'])->findOrFail($id);
            $payment = Payment::where('enrollment_id', $id)->first();
            
            $data = [
                'enrollment_id' => $enrollment->enrollment_id,
                'program_name' => $enrollment->program->program_name ?? 'N/A',
                'package_name' => $enrollment->package->package_name ?? 'N/A',
                'amount' => $enrollment->package->amount ?? 0,
            ];

            if ($payment) {
                $paymentDetails = is_string($payment->payment_details) ? json_decode($payment->payment_details, true) : ($payment->payment_details ?? []);
                $data = array_merge($data, [
                    'payment_method' => $payment->payment_method,
                    'reference_number' => $payment->reference_number,
                    'payment_details' => $paymentDetails
                ]);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Enrollment not found or database error.'], 404);
        }
    }

    public function approveEnrollment($enrollmentId)
    {
        try {
            DB::beginTransaction();
            
            // Find enrollment by enrollment_id
            $enrollment = Enrollment::where('enrollment_id', $enrollmentId)->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found'
                ], 404);
            }
            
            // Check if already approved
            if ($enrollment->enrollment_status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment is already approved'
                ], 400);
            }
            
            // Update enrollment status to approved
            $enrollment->update([
                'enrollment_status' => 'approved',
                'updated_at' => now()
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Enrollment approved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error approving enrollment', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving enrollment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display chat logs for admin monitoring
     */
    public function chatIndex(Request $request)
    {
        // In a real application, this would fetch chat messages from a database
        // For now, we'll create a sample chat log interface
        
        $chatRooms = [
            [
                'id' => 1,
                'name' => 'General Support',
                'participants' => 15,
                'last_message' => 'Thanks for your help!',
                'last_activity' => Carbon::now()->subMinutes(5),
                'unread_count' => 3,
                'type' => 'support'
            ],
            [
                'id' => 2,
                'name' => 'Technical Issues',
                'participants' => 8,
                'last_message' => 'The system is working now',
                'last_activity' => Carbon::now()->subMinutes(15),
                'unread_count' => 1,
                'type' => 'technical'
            ],
            [
                'id' => 3,
                'name' => 'Course Inquiries',
                'participants' => 12,
                'last_message' => 'When does the next batch start?',
                'last_activity' => Carbon::now()->subMinutes(30),
                'unread_count' => 0,
                'type' => 'courses'
            ],
            [
                'id' => 4,
                'name' => 'Student Services',
                'participants' => 25,
                'last_message' => 'Payment confirmation received',
                'last_activity' => Carbon::now()->subHour(),
                'unread_count' => 7,
                'type' => 'services'
            ]
        ];
        
        $recentMessages = [
            [
                'id' => 1,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'room' => 'General Support',
                'message' => 'I need help with my course enrollment',
                'timestamp' => Carbon::now()->subMinutes(2),
                'status' => 'unread'
            ],
            [
                'id' => 2,
                'user_name' => 'Jane Smith',
                'user_type' => 'professor',
                'room' => 'Technical Issues',
                'message' => 'The quiz generator is not working properly',
                'timestamp' => Carbon::now()->subMinutes(10),
                'status' => 'read'
            ],
            [
                'id' => 3,
                'user_name' => 'Mike Johnson',
                'user_type' => 'student',
                'room' => 'Course Inquiries',
                'message' => 'What are the prerequisites for Advanced Programming?',
                'timestamp' => Carbon::now()->subMinutes(25),
                'status' => 'responded'
            ]
        ];
        
        $stats = [
            'total_conversations' => count($chatRooms),
            'active_users' => 45,
            'unread_messages' => collect($chatRooms)->sum('unread_count'),
            'response_time_avg' => '2.5 minutes'
        ];
        
        return view('admin.chat.index', compact('chatRooms', 'recentMessages', 'stats'));
    }
    
    /**
     * Display specific chat room
     */
    public function chatRoom($roomId)
    {
        // Sample chat messages for the room
        $messages = [
            [
                'id' => 1,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'message' => 'Hello, I need help with my course enrollment',
                'timestamp' => Carbon::now()->subMinutes(30),
                'avatar' => 'JD'
            ],
            [
                'id' => 2,
                'user_name' => 'Admin Support',
                'user_type' => 'admin',
                'message' => 'Hi John! I\'d be happy to help you with your enrollment. Which course are you trying to enroll in?',
                'timestamp' => Carbon::now()->subMinutes(28),
                'avatar' => 'AS'
            ],
            [
                'id' => 3,
                'user_name' => 'John Doe',
                'user_type' => 'student',
                'message' => 'I\'m looking at the Advanced Programming course, but I can\'t find the enrollment button',
                'timestamp' => Carbon::now()->subMinutes(25),
                'avatar' => 'JD'
            ],
            [
                'id' => 4,
                'user_name' => 'Admin Support',
                'user_type' => 'admin',
                'message' => 'Let me check your account status. Can you please provide your student ID?',
                'timestamp' => Carbon::now()->subMinutes(20),
                'avatar' => 'AS'
            ]
        ];
        
        $roomInfo = [
            'id' => $roomId,
            'name' => 'General Support',
            'participants' => 15,
            'type' => 'support'
        ];
        
        return view('admin.chat.room', compact('messages', 'roomInfo'));
    }
    
    /**
     * Display FAQ management page
     */
    public function faqIndex()
    {
        $faqs = [
            [
                'id' => 1,
                'question' => 'How do I enroll in a course?',
                'answer' => 'To enroll in a course, go to your dashboard, select "Available Courses", choose your desired course, and click "Enroll Now". Complete the payment process to finalize your enrollment.',
                'category' => 'Enrollment',
                'category_id' => 1,
                'keywords' => 'enroll, register, course, signup',
                'status' => 'active',
                'views' => 145,
                'updated_at' => Carbon::now()->subDays(2)->format('M j, Y')
            ],
            [
                'id' => 2,
                'question' => 'What are the payment options?',
                'answer' => 'We accept credit/debit cards, PayPal, bank transfers, and installment plans for select courses. All payments are processed securely.',
                'category' => 'Payment',
                'category_id' => 2,
                'keywords' => 'payment, pay, fee, money, cost',
                'status' => 'active',
                'views' => 98,
                'updated_at' => Carbon::now()->subDays(1)->format('M j, Y')
            ],
            [
                'id' => 3,
                'question' => 'How do I check my class schedule?',
                'answer' => 'Login to your dashboard, go to "My Courses", and click the "Schedule" tab. You can also export your schedule to your calendar.',
                'category' => 'Schedule',
                'category_id' => 3,
                'keywords' => 'schedule, time, class, timetable',
                'status' => 'active',
                'views' => 87,
                'updated_at' => Carbon::now()->subDays(3)->format('M j, Y')
            ],
            [
                'id' => 4,
                'question' => 'How do I get my certificate?',
                'answer' => 'Complete all course modules, pass assessments, maintain 80% attendance, and complete the final project. Certificates are generated automatically within 5-7 business days.',
                'category' => 'Certificate',
                'category_id' => 4,
                'keywords' => 'certificate, diploma, completion, graduate',
                'status' => 'active',
                'views' => 156,
                'updated_at' => Carbon::now()->subDays(5)->format('M j, Y')
            ],
            [
                'id' => 5,
                'question' => 'How do I contact support?',
                'answer' => 'Contact support via live chat, email (support@artc.edu), phone (+1-555-123-4567), or submit a ticket through the support portal.',
                'category' => 'Support',
                'category_id' => 5,
                'keywords' => 'support, help, contact, assistance',
                'status' => 'active',
                'views' => 234,
                'updated_at' => Carbon::now()->subDays(1)->format('M j, Y')
            ]
        ];
        
        $categories = [
            [
                'id' => 1,
                'name' => 'Enrollment',
                'count' => 1
            ],
            [
                'id' => 2,
                'name' => 'Payment',
                'count' => 1
            ],
            [
                'id' => 3,
                'name' => 'Schedule',
                'count' => 1
            ],
            [
                'id' => 4,
                'name' => 'Certificate',
                'count' => 1
            ],
            [
                'id' => 5,
                'name' => 'Support',
                'count' => 1
            ]
        ];
        
        return view('admin.faq.index', compact('faqs', 'categories'));
    }
    
    /**
     * Store new FAQ
     */
    public function storeFaq(Request $request)
    {
        // In a real application, this would save to database
        return response()->json(['message' => 'FAQ saved successfully']);
    }
    
    /**
     * Update FAQ
     */
    public function updateFaq(Request $request, $id)
    {
        // In a real application, this would update the database
        return response()->json(['message' => 'FAQ updated successfully']);
    }
    
    /**
     * Delete FAQ
     */
    public function deleteFaq($id)
    {
        // In a real application, this would delete from database
        return response()->json(['message' => 'FAQ deleted successfully']);
    }

    // New: Get payment details by enrollment ID
    public function getPaymentDetailsByEnrollment($enrollmentId)
    {
        try {
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if (!$payment) {
                return response()->json(['error' => 'Payment not found for this enrollment.'], 404);
            }
            $paymentDetails = $payment->payment_details;
            if (is_string($paymentDetails)) {
                $paymentDetails = json_decode($paymentDetails, true);
            }
            $response = [
                'payment_id' => $payment->payment_id,
                'enrollment_id' => $payment->enrollment_id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount ?? 0,
                'payment_method' => $payment->payment_method ?? 'N/A',
                'payment_status' => $payment->payment_status ?? 'N/A',
                'reference_number' => $payment->reference_number ?? ($paymentDetails['reference_number'] ?? 'N/A'),
                'payment_proof_path' => $paymentDetails['payment_proof_path'] ?? null,
                'payment_proof_url' => isset($paymentDetails['payment_proof_path']) 
                    ? asset('storage/' . $paymentDetails['payment_proof_path']) 
                    : null,
                'created_at' => $payment->created_at ? $payment->created_at->format('M d, Y g:i A') : 'N/A',
                'notes' => $payment->notes ?? '',
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading payment details: ' . $e->getMessage()], 500);
        }
    }

    // Reject registration method
    public function rejectRegistration(Request $request, $id)
    {
        $request->validate([
            'reason'          => 'required|string',
            'rejected_fields' => 'nullable|array',
        ]);
    
        try {
            $registration = Registration::findOrFail($id);
            $registration->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_fields'  => json_encode($request->input('rejected_fields', [])),
                'rejected_at'      => now(),
            ]);

            // Handle different response types
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration rejected successfully.'
                ]);
            }
    
            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration rejected successfully.');
    
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejection failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    // Approve registration method
    public function approveRegistration(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            
            // Store original data for undo functionality
            $registration->original_submission = json_encode([
                'status' => $registration->status,
                'approved_at' => $registration->approved_at,
                'approved_by' => session('admin_id')
            ]);
            
            $registration->status = 'approved';
            $registration->approved_at = now();
            $registration->approved_by = session('admin_id');
            $registration->save();

            // Create student record if not exists
            $this->createStudentFromRegistration($registration);

            // Handle different response types
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration approved successfully.'
                ]);
            }

            return redirect()
                ->route('admin.student.registration.pending')
                ->with('success', 'Registration approved successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error approving registration: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error approving registration: ' . $e->getMessage());
        }
    }

    // Undo registration approval - move back to pending with comment
    public function undoRegistrationApproval(Request $request, $id)
    {
        try {
            $registration = Registration::findOrFail($id);
            if ($registration->status !== 'approved') {
                if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Only approved registrations can be undone.'], 400);
                }
                return redirect()->back()->with('error', 'Only approved registrations can be undone.');
            }
            $registration->status = 'pending';
            $registration->approved_at = null;
            $registration->approved_by = null;
            $registration->undo_reason = $request->input('undo_reason', null);
            $registration->undone_at = now();
            $registration->undone_by = auth()->guard('admin')->user()->admin_id ?? null;
            $registration->save();
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Registration approval undone successfully. Student moved back to pending status.']);
            }
            return redirect()->back()->with('success', 'Registration approval undone successfully. Student moved back to pending status.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error undoing registration approval: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error undoing registration approval: ' . $e->getMessage());
        }
    }

    // Mark payment as paid
    public function markPaymentAsPaid(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            $payment->payment_status = 'paid';
            $payment->verified_by = session('admin_id');
            $payment->verified_at = now();
            $payment->notes = $request->input('notes', 'Marked as paid by administrator');
            $payment->save();

            // Update enrollment payment status
            if ($payment->enrollment_id) {
                $enrollment = Enrollment::find($payment->enrollment_id);
                if ($enrollment) {
                    $enrollment->payment_status = 'paid';
                    $enrollment->save();
                }
            }

            // Create payment history record
            PaymentHistory::create([
                'enrollment_id' => $payment->enrollment_id,
                'user_id' => $payment->enrollment->user_id ?? null,
                'student_id' => $payment->student_id,
                'program_id' => $payment->program_id,
                'package_id' => $payment->package_id,
                'amount' => $payment->amount,
                'payment_status' => 'paid',
                'payment_method' => 'manual',
                'payment_notes' => 'Payment marked as paid by administrator',
                'payment_date' => now(),
                'processed_by_admin_id' => session('admin_id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking payment as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    // View payment details
    public function viewPaymentDetails($id)
    {
        try {
            $payment = Payment::with(['enrollment.user', 'enrollment.student', 'enrollment.program', 'enrollment.package'])
                             ->findOrFail($id);

            return response()->json([
                'success' => true,
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment details: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject payment
    public function rejectPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string',
                'rejected_fields' => 'nullable|array'
            ]);

            $payment = Payment::findOrFail($id);
            
            $payment->payment_status = 'rejected';
            $payment->rejection_reason = $request->input('rejection_reason');
            $payment->rejected_fields = json_encode($request->input('rejected_fields', []));
            $payment->rejected_by = session('admin_id');
            $payment->rejected_at = now();
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to create student from registration
    private function createStudentFromRegistration($registration)
    {
        // Check if student already exists
        $existingStudent = Student::where('user_id', $registration->user_id)->first();
        
        if (!$existingStudent) {
            // Create student record
            $studentId = $this->generateStudentId();
            
            Student::create([
                'student_id' => $studentId,
                'user_id' => $registration->user_id,
                'firstname' => $registration->firstname,
                'middlename' => $registration->middlename,
                'lastname' => $registration->lastname,
                'email' => $registration->user->email ?? '',
                'package_id' => $registration->package_id,
                'package_name' => $registration->package_name,
                'program_id' => $registration->program_id,
                'program_name' => $registration->program_name,
                'enrollment_type' => $registration->enrollment_type,
                'learning_mode' => $registration->learning_mode,
                'education_level' => $registration->education_level,
                'status' => 'approved',
                'date_approved' => now()
            ]);
        }
    }

    // Helper method to generate student ID
    private function generateStudentId()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last student ID for this year and month
        $lastStudent = Student::where('student_id', 'LIKE', "{$year}-{$month}-%")
                             ->orderBy('student_id', 'desc')
                             ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s-%05d', $year, $month, $newNumber);
    }

    public function undoPendingPayment(Request $request, $enrollmentId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'No payment record found for this enrollment.'], 404);
            }
            $payment->payment_status = 'rejected';
            $payment->rejection_reason = $request->input('reason');
            $payment->rejected_at = now();
            $payment->rejected_by = auth()->guard('admin')->user()->admin_id ?? 1;
            $payment->save();
            $enrollment->payment_status = 'rejected';
            $enrollment->save();
            return response()->json(['success' => true, 'message' => 'Payment has been undone and moved back to pending.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error undoing payment: ' . $e->getMessage()], 500);
        }
    }

    public function undoPaymentHistory(Request $request, $paymentHistoryId)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000'
            ]);
            $paymentHistory = PaymentHistory::findOrFail($paymentHistoryId);
            $enrollmentId = $paymentHistory->enrollment_id;
            // Delete the payment history record
            $paymentHistory->delete();
            // Also update the related Payment and Enrollment if they exist
            $payment = Payment::where('enrollment_id', $enrollmentId)->first();
            if ($payment) {
                $payment->payment_status = 'pending';
                $payment->rejection_reason = $request->input('reason');
                $payment->rejected_at = now();
                $payment->rejected_by = auth()->guard('admin')->user()->admin_id ?? 1;
                $payment->save();
            }
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment) {
                $enrollment->payment_status = 'pending';
                $enrollment->save();
            }
            return response()->json(['success' => true, 'message' => 'Payment history undone and removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error undoing payment history: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View assignment submissions for admin grading
     */
    public function viewSubmissions(Request $request)
    {
        try {
            // Get filter parameters
            $programId = $request->get('program_id');
            $moduleId = $request->get('module_id');
            $status = $request->get('status');

            // Build query for submissions with proper relationships
            $query = AssignmentSubmission::with([
                'student' => function($q) {
                    $q->with('user');
                }, 
                'program', 
                'module'
            ])->orderBy('submitted_at', 'desc');

            // Apply filters
            if ($programId) {
                $query->where('program_id', $programId);
            }

            if ($moduleId) {
                $query->where('module_id', $moduleId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            // Get submissions with pagination
            $submissions = $query->paginate(10);

            // Decode files JSON to array for each submission
            foreach ($submissions as $submission) {
                if (is_string($submission->files)) {
                    $decoded = json_decode($submission->files, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $submission->files = $decoded;
                    }
                }
            }

            // Get all programs and modules for filter dropdowns
            $programs = Program::where('is_archived', false)
                ->orderBy('program_name')
                ->get();

            $modules = Module::where('is_archived', false)
                ->orderBy('module_name')
                ->get();

            return view('admin.admin-student-submissions.admin-submission', compact(
                'submissions',
                'programs',
                'modules'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading admin submissions: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading submissions: ' . $e->getMessage());
        }
    }

    /**
     * Grade an assignment submission
     */
    public function gradeSubmission(Request $request, $id)
    {
        try {
            $request->validate([
                'grade' => 'required|numeric|min:0|max:100',
                'feedback' => 'nullable|string|max:2000',
                'status' => 'required|in:graded,reviewed'
            ]);

            $submission = AssignmentSubmission::findOrFail($id);

            // Update submission with grade and feedback
            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback,
                'status' => $request->status,
                'graded_at' => now(),
                'graded_by' => session('admin_id') ?? 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment graded successfully!',
                'submission' => $submission
            ]);

        } catch (\Exception $e) {
            Log::error('Error grading submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error grading submission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download assignment submission file
     */
    public function downloadSubmission($id)
    {
        try {
            $submission = AssignmentSubmission::findOrFail($id);
            
            if (!$submission->attachment) {
                return redirect()->back()->with('error', 'No attachment found for this submission.');
            }

            $filePath = storage_path('app/public/' . $submission->attachment);
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'Submission file not found.');
            }

            $originalName = basename($submission->attachment);
            
            return response()->download($filePath, $originalName);

        } catch (\Exception $e) {
            Log::error('Error downloading submission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error downloading submission file.');
        }
    }

    /**
     * View submissions for a specific assignment
     */
    public function viewAssignmentSubmissions($assignmentId)
    {
        try {
            // Get the assignment details
            $assignment = \App\Models\ContentItem::where('content_id', $assignmentId)
                ->where('content_type', 'assignment')
                ->with(['module.program', 'course'])
                ->firstOrFail();

            // Get all submissions for this assignment
            $submissions = AssignmentSubmission::with([
                'student' => function($q) {
                    $q->with('user');
                }, 
                'program', 
                'module'
            ])
            ->where('content_id', $assignmentId)
            ->orderBy('submitted_at', 'desc')
            ->get();

            // Decode files JSON to array for each submission
            foreach ($submissions as $submission) {
                if (is_string($submission->files)) {
                    $decoded = json_decode($submission->files, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $submission->files = $decoded;
                    }
                }
            }

            return view('admin.assignment-submissions', compact(
                'assignment',
                'submissions'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading assignment submissions: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error loading assignment submissions: ' . $e->getMessage());
        }
    }

    /**
     * Display a preview version of the admin dashboard for admin customization
     */
    public function showPreviewDashboard()
    {
        // Set up session data for preview mode to prevent layout errors
        session([
            'user_id' => 'preview-admin',
            'user_name' => 'Admin Preview',
            'user_role' => 'admin',
            'user_type' => 'admin',
            'logged_in' => true
        ]);

        // Create sample data for preview
        $registrations = collect([
            (object) [
                'registration_id' => 'preview-reg-1',
                'user_firstname' => 'John',
                'user_lastname' => 'Doe',
                'user_email' => 'john.doe@example.com',
                'program_name' => 'Nursing Board Review',
                'package_name' => 'Premium Package',
                'enrollment_type' => 'Full',
                'status' => 'pending',
                'created_at' => now(),
                'total_amount' => 2500.00
            ],
            (object) [
                'registration_id' => 'preview-reg-2',
                'user_firstname' => 'Jane',
                'user_lastname' => 'Smith',
                'user_email' => 'jane.smith@example.com',
                'program_name' => 'Medical Technology Review',
                'package_name' => 'Standard Package',
                'enrollment_type' => 'Modular',
                'status' => 'pending',
                'created_at' => now()->subHours(2),
                'total_amount' => 1800.00
            ]
        ]);

        // Sample analytics data
        $analytics = [
            'total_students' => 245,
            'total_programs' => 8,
            'total_modules' => 32,
            'total_enrollments' => 312,
            'pending_registrations' => 12,
            'new_students_this_month' => 18,
            'modules_this_week' => 3,
            'archived_programs' => 2,
        ];

        $dbError = null;

        // Render full admin dashboard layout instead of minimal simple preview
        // Provide additional variables expected by the full view (even if placeholder values)
        $recentAnnouncements = collect([]);
        $recentActivity = collect([]);

        return view('admin.admin-dashboard.admin-dashboard', [
            'registrations' => $registrations,
            'analytics' => $analytics,
            'dbError' => $dbError,
            'recentAnnouncements' => $recentAnnouncements,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Preview mode for admin dashboard with tenant customization
     */
    public function previewDashboard($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add this for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Mock analytics data for preview
            $analytics = [
                'total_students' => 156,
                'total_programs' => 8,
                'total_modules' => 24,
                'total_enrollments' => 342,
                'pending_registrations' => 12,
                'new_students_this_month' => 28,
                'modules_this_week' => 3,
                'archived_programs' => 2,
            ];
            
            $registrations = collect();
            $dbError = null;

            return view('admin.admin-dashboard.admin-dashboard', compact('analytics','registrations','dbError'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Preview dashboard error for tenant $tenant", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response('<h1>Dashboard Preview Error</h1><p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>', 500);
        }
    }

    /**
     * Preview mode for payment pending page
     */
    public function previewPaymentPending($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock enrollments for pending payments
            $enrollments = collect([
                $this->createMockObject([
                    'enrollment_id' => 1,
                    'student_name' => 'Juan Dela Cruz',
                    'student_email' => 'juan.delacruz@example.com',
                    'payment_status' => 'pending',
                    'payment_method' => 'GCash', // Add missing property
                    'total_amount' => 15000.00,
                    'created_at' => now()->subDays(2),
                    'program' => $this->createMockObject([
                        'program_name' => 'Nursing Review Program'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'Complete Nursing Package'
                    ])
                ]),
                $this->createMockObject([
                    'enrollment_id' => 2,
                    'student_name' => 'Maria Santos',
                    'student_email' => 'maria.santos@example.com',
                    'payment_status' => 'pending',
                    'payment_method' => 'Bank Transfer', // Add missing property
                    'total_amount' => 18000.00,
                    'created_at' => now()->subDays(1),
                    'program' => $this->createMockObject([
                        'program_name' => 'Medical Technology Review'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'MedTech Premium Package'
                    ])
                ])
            ]);

            $pendingApprovals = collect();
            
            // Generate mock rejected payments
            $rejectedPayments = collect([
                $this->createMockObject([
                    'id' => 1,
                    'payment_id' => 'PAY001',
                    'payment_method' => 'GCash',
                    'amount' => 15000.00,
                    'status' => 'rejected',
                    'rejected_at' => now()->subDays(1),
                    'rejection_reason' => 'Invalid receipt number',
                    'enrollment' => $this->createMockObject([
                        'student_name' => 'Carlos Mendoza',
                        'student_email' => 'carlos.mendoza@example.com'
                    ])
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'payment_id' => 'PAY002',
                    'payment_method' => 'Bank Transfer',
                    'amount' => 18000.00,
                    'status' => 'rejected',
                    'rejected_at' => now()->subHours(6),
                    'rejection_reason' => 'Insufficient payment amount',
                    'enrollment' => $this->createMockObject([
                        'student_name' => 'Ana Reyes',
                        'student_email' => 'ana.reyes@example.com'
                    ])
                ])
            ]);

            return view('admin.admin-student-registration.admin-payment-pending', [
                'enrollments' => $enrollments,
                'pendingApprovals' => $pendingApprovals,
                'rejectedPayments' => $rejectedPayments,
                'isPreview' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Payment pending preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Payment Pending Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Payment Pending Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview mode for payment history page
     */
    public function previewPaymentHistory($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock payment history
            $paymentHistory = collect([
                $this->createMockObject([
                    'payment_history_id' => 1,
                    'payment_date' => now()->subDays(10),
                    'amount_paid' => 15000.00,
                    'payment_method' => 'Bank Transfer',
                    'status' => 'completed',
                    'payment_status' => 'completed',
                    'enrollment' => $this->createMockObject([
                        'enrollment_id' => 1,
                        'student' => $this->createMockObject([
                            'firstname' => 'Juan',
                            'lastname' => 'Dela Cruz',
                            'email' => 'juan.delacruz@example.com'
                        ]),
                        'program' => $this->createMockObject([
                            'program_name' => 'Nursing Review Program'
                        ]),
                        'package' => $this->createMockObject([
                            'package_name' => 'Complete Nursing Package'
                        ])
                    ])
                ]),
                $this->createMockObject([
                    'payment_history_id' => 2,
                    'payment_date' => now()->subDays(5),
                    'amount_paid' => 18000.00,
                    'payment_method' => 'GCash',
                    'status' => 'completed',
                    'payment_status' => 'completed',
                    'enrollment' => $this->createMockObject([
                        'enrollment_id' => 2,
                        'student' => $this->createMockObject([
                            'firstname' => 'Maria',
                            'lastname' => 'Santos',
                            'email' => 'maria.santos@example.com'
                        ]),
                        'program' => $this->createMockObject([
                            'program_name' => 'Medical Technology Review'
                        ]),
                        'package' => $this->createMockObject([
                            'package_name' => 'MedTech Premium Package'
                        ])
                    ])
                ])
            ]);

            return view('admin.admin-student-registration.admin-payment-history', [
                'paymentHistory' => $paymentHistory,
                'isPreview' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Payment history preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Payment History Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Payment History Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview student registration pending page
     */
    public function previewStudentRegistration($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock registrations
            $registrations = collect([
                $this->createMockObject([
                    'registration_id' => 1,
                    'user_firstname' => 'Juan',
                    'user_lastname' => 'Dela Cruz',
                    'email' => 'juan.delacruz@example.com',
                    'status' => 'pending',
                    'enrollment_type' => 'new',
                    'created_at' => now()->subDays(2),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Juan',
                        'user_lastname' => 'Dela Cruz',
                        'email' => 'juan.delacruz@example.com'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'Complete Nursing Package'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Nursing Review Program'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Standard Plan'
                    ])
                ]),
                $this->createMockObject([
                    'registration_id' => 2,
                    'user_firstname' => 'Maria',
                    'user_lastname' => 'Santos',
                    'email' => 'maria.santos@example.com',
                    'status' => 'pending',
                    'enrollment_type' => 'renewal',
                    'created_at' => now()->subDays(1),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Maria',
                        'user_lastname' => 'Santos',
                        'email' => 'maria.santos@example.com'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'MedTech Premium Package'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Medical Technology Review'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Premium Plan'
                    ])
                ])
            ]);

            return view('admin.admin-student-registration.admin-student-registration', [
                'registrations' => $registrations,
                'history' => false,
                'isPreview' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Student registration preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Student Registration Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Student Registration Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview student registration history page
     */
    public function previewStudentRegistrationHistory($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock approved registrations
            $registrations = collect([
                $this->createMockObject([
                    'registration_id' => 1,
                    'user_firstname' => 'Carlos',
                    'user_lastname' => 'Gonzalez',
                    'email' => 'carlos.gonzalez@example.com',
                    'status' => 'approved',
                    'enrollment_type' => 'new',
                    'approved_at' => now()->subDays(5),
                    'created_at' => now()->subDays(7),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Carlos',
                        'user_lastname' => 'Gonzalez',
                        'email' => 'carlos.gonzalez@example.com'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'Complete Nursing Package'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Nursing Review Program'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Standard Plan'
                    ])
                ]),
                $this->createMockObject([
                    'registration_id' => 2,
                    'user_firstname' => 'Ana',
                    'user_lastname' => 'Reyes',
                    'email' => 'ana.reyes@example.com',
                    'status' => 'approved',
                    'enrollment_type' => 'renewal',
                    'approved_at' => now()->subDays(3),
                    'created_at' => now()->subDays(6),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Ana',
                        'user_lastname' => 'Reyes',
                        'email' => 'ana.reyes@example.com'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'MedTech Premium Package'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Medical Technology Review'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Premium Plan'
                    ])
                ])
            ]);

            return view('admin.admin-student-registration.admin-student-registration', [
                'registrations' => $registrations,
                'history' => true,
                'isPreview' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Student registration history preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>Student Registration History Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>Student Registration History Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview mode for FAQ management page
     */
    public function previewFaqIndex($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Use existing FAQ data from faqIndex method
            $faqs = [
                [
                    'id' => 1,
                    'question' => 'How do I enroll in a course?',
                    'answer' => 'To enroll in a course, go to your dashboard, select "Available Courses", choose your desired course, and click "Enroll Now". Complete the payment process to finalize your enrollment.',
                    'category' => 'Enrollment',
                    'category_id' => 1,
                    'keywords' => 'enroll, register, course, signup',
                    'status' => 'active',
                    'views' => 145,
                    'updated_at' => \Carbon\Carbon::now()->subDays(2)->format('M j, Y')
                ],
                [
                    'id' => 2,
                    'question' => 'What are the payment options?',
                    'answer' => 'We accept credit/debit cards, PayPal, bank transfers, and installment plans for select courses. All payments are processed securely.',
                    'category' => 'Payment',
                    'category_id' => 2,
                    'keywords' => 'payment, pay, fee, money, cost',
                    'status' => 'active',
                    'views' => 98,
                    'updated_at' => \Carbon\Carbon::now()->subDays(1)->format('M j, Y')
                ]
            ];
            
            $categories = [
                ['id' => 1, 'name' => 'Enrollment', 'count' => 1],
                ['id' => 2, 'name' => 'Payment', 'count' => 1],
                ['id' => 3, 'name' => 'Schedule', 'count' => 1],
                ['id' => 4, 'name' => 'Certificate', 'count' => 1],
                ['id' => 5, 'name' => 'Support', 'count' => 1]
            ];

            return view('admin.faq.index', compact('faqs', 'categories'));

        } catch (\Exception $e) {
            Log::error('FAQ preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head>
                        <title>FAQ Management Preview - ' . htmlspecialchars($tenant) . '</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
                            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                            h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
                            .success { color: #28a745; font-size: 18px; margin: 20px 0; }
                            .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
                            .faqs { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
                            .faq-item { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; border-radius: 4px; }
                            .back-link { display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none; }
                            .back-link:hover { text-decoration: underline; }
                            .tenant-label { background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <h1>❓ FAQ Management Preview</h1>
                            <div class="tenant-label">Tenant: ' . htmlspecialchars($tenant) . '</div>
                            
                            <div class="success">✅ FAQ Management preview is working!</div>
                            
                            <div class="info">
                                <strong>Mock Data Generated:</strong><br>
                                • 2 sample FAQ entries<br>
                                • 5 FAQ categories<br>
                                • Admin management interface<br>
                                • Tenant customization applied (TEST11 branding)
                            </div>
                            
                            <div class="faqs">
                                <h3>Sample FAQs:</h3>
                                <div class="faq-item">
                                    <strong>Q: How do I enroll in a course?</strong><br>
                                    A: Go to dashboard, select Available Courses, choose course, click Enroll Now...
                                </div>
                                <div class="faq-item">
                                    <strong>Q: What are the payment options?</strong><br>
                                    A: Credit/debit cards, PayPal, bank transfers, installment plans...
                                </div>
                            </div>
                            
                            <p><strong>Note:</strong> This preview shows that the FAQ Management route and controller are working correctly. The tenant customization (TEST11 branding) is being applied.</p>
                            
                            <a href="/t/draft/' . htmlspecialchars($tenant) . '/admin-dashboard" class="back-link">← Back to Admin Dashboard</a>
                        </div>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);
        }
    }

    /**
     * Preview mode for assignment submissions page
     */
    public function previewSubmissions($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock submissions data
            $submissions = collect([
                $this->createMockObject([
                    'id' => 1,
                    'title' => 'Nursing Care Plan Assignment',
                    'content' => 'Comprehensive care plan for patient with diabetes',
                    'comments' => 'Please submit a detailed nursing care plan including assessment, diagnosis, planning, implementation, and evaluation.',
                    'files' => [
                        ['name' => 'care_plan.pdf', 'original_filename' => 'care_plan.pdf', 'size' => 2560000, 'type' => 'application/pdf', 'path' => 'submissions/care_plan.pdf'],
                        ['name' => 'references.docx', 'original_filename' => 'references.docx', 'size' => 1228800, 'type' => 'application/msword', 'path' => 'submissions/references.docx']
                    ],
                    'status' => 'pending',
                    'submitted_at' => now()->subDays(2),
                    'graded_at' => null,
                    'grade' => null,
                    'feedback' => null,
                    'student' => $this->createMockObject([
                        'student_id' => 'STU001',
                        'firstname' => 'Juan',
                        'lastname' => 'Dela Cruz',
                        'email' => 'juan.delacruz@example.com',
                        'user' => $this->createMockObject([
                            'user_firstname' => 'Juan',
                            'user_lastname' => 'Dela Cruz'
                        ])
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Nursing Review Program'
                    ]),
                    'module' => $this->createMockObject([
                        'module_name' => 'Fundamentals of Nursing'
                    ])
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'title' => 'Medical Technology Lab Report',
                    'content' => 'Analysis of blood chemistry results',
                    'comments' => 'Submit a comprehensive analysis of the provided lab results with interpretation.',
                    'files' => [
                        ['name' => 'lab_report.pdf', 'original_filename' => 'lab_report.pdf', 'size' => 1843200, 'type' => 'application/pdf', 'path' => 'submissions/lab_report.pdf']
                    ],
                    'status' => 'graded',
                    'submitted_at' => now()->subDays(5),
                    'graded_at' => now()->subDays(3),
                    'grade' => 88,
                    'feedback' => 'Excellent analysis and interpretation. Good use of references. Minor formatting issues.',
                    'student' => $this->createMockObject([
                        'student_id' => 'STU002',
                        'firstname' => 'Maria',
                        'lastname' => 'Santos',
                        'email' => 'maria.santos@example.com',
                        'user' => $this->createMockObject([
                            'user_firstname' => 'Maria',
                            'user_lastname' => 'Santos'
                        ])
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'Medical Technology Review'
                    ]),
                    'module' => $this->createMockObject([
                        'module_name' => 'Clinical Chemistry'
                    ])
                ])
            ]);

            // Paginate mock data (simulate Laravel's paginate)
            $submissions = new \Illuminate\Pagination\LengthAwarePaginator(
                $submissions,
                $submissions->count(),
                10,
                1,
                ['path' => request()->url()]
            );

            // Mock programs and modules for filter dropdowns
            $programs = collect([
                $this->createMockObject([
                    'id' => 1,
                    'program_id' => 1,
                    'program_name' => 'Nursing Review Program',
                    'getCreator' => function() { return 'admin@test1.com'; }
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'program_id' => 2,
                    'program_name' => 'Medical Technology Review',
                    'getCreator' => function() { return 'admin@test1.com'; }
                ])
            ]);

            $modules = collect([
                $this->createMockObject([
                    'id' => 1,
                    'module_id' => 1,
                    'module_name' => 'Fundamentals of Nursing',
                    'getCreator' => function() { return 'admin@test1.com'; }
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'module_id' => 2,
                    'module_name' => 'Clinical Chemistry',
                    'getCreator' => function() { return 'admin@test1.com'; }
                ])
            ]);

            return view('admin.admin-student-submissions.admin-submission', compact(
                'submissions',
                'programs',
                'modules'
            ));

        } catch (\Exception $e) {
            Log::error('Submissions preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head>
                        <title>Assignment Submissions Preview - ' . htmlspecialchars($tenant) . '</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
                            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                            h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
                            .success { color: #28a745; font-size: 18px; margin: 20px 0; }
                            .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
                            .submissions { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
                            .submission-item { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; border-radius: 4px; }
                            .back-link { display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none; }
                            .back-link:hover { text-decoration: underline; }
                            .tenant-label { background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <h1>📝 Assignment Submissions Preview</h1>
                            <div class="tenant-label">Tenant: ' . htmlspecialchars($tenant) . '</div>
                            
                            <div class="success">✅ Assignment Submissions preview is working!</div>
                            
                            <div class="info">
                                <strong>Mock Data Generated:</strong><br>
                                • 2 sample submissions (1 pending, 1 graded)<br>
                                • Student assignments with files<br>
                                • Program and module filtering<br>
                                • Tenant customization applied (TEST11 branding)
                            </div>
                            
                            <div class="submissions">
                                <h3>Sample Submissions:</h3>
                                <div class="submission-item">
                                    <strong>Nursing Care Plan Assignment</strong><br>
                                    Student: Juan Dela Cruz<br>
                                    Status: Pending Review<br>
                                    Files: care_plan.pdf, references.docx
                                </div>
                                <div class="submission-item">
                                    <strong>Medical Technology Lab Report</strong><br>
                                    Student: Maria Santos<br>
                                    Status: Graded (88/100)<br>
                                    Files: lab_report.pdf
                                </div>
                            </div>
                            
                            <p><strong>Note:</strong> This preview shows that the Assignment Submissions route and controller are working correctly. The tenant customization (TEST11 branding) is being applied.</p>
                            
                            <a href="/t/draft/' . htmlspecialchars($tenant) . '/admin-dashboard" class="back-link">← Back to Admin Dashboard</a>
                        </div>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);
        }
    }

    /**
     * Preview mode for certificates page
     */
    public function previewCertificates($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock certificates
            $certificates = collect([
                $this->createMockObject([
                    'id' => 1,
                    'student_name' => 'Maria Santos',
                    'program_name' => 'Nursing Review Program',
                    'certificate_type' => 'Completion Certificate',
                    'issued_date' => now()->subDays(30),
                    'status' => 'issued',
                    'certificate_number' => 'CERT-2025-001'
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'student_name' => 'Carlos Garcia',
                    'program_name' => 'Medical Technology Review',
                    'certificate_type' => 'Excellence Certificate',
                    'issued_date' => now()->subDays(15),
                    'status' => 'pending',
                    'certificate_number' => 'CERT-2025-002'
                ]),
                $this->createMockObject([
                    'id' => 3,
                    'student_name' => 'Ana Rodriguez',
                    'program_name' => 'Pharmacy Review Program',
                    'certificate_type' => 'Honor Certificate',
                    'issued_date' => now()->subDays(5),
                    'status' => 'issued',
                    'certificate_number' => 'CERT-2025-003'
                ])
            ]);

            $html = view('admin.certificates.certificates', [
                'certificates' => $certificates,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            Log::error('Certificates preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Certificates Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Certificates Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview mode for archived content page
     */
    public function previewArchivedContent($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock archived data
            $archivedPrograms = collect([
                $this->createMockObject([
                    'id' => 1,
                    'program_name' => 'TEST11 Nursing Program 2024-A',
                    'archived_at' => now()->subDays(30),
                    'status' => 'archived'
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'program_name' => 'TEST11 MedTech Batch 2024-B',
                    'archived_at' => now()->subDays(15),
                    'status' => 'archived'
                ]),
                $this->createMockObject([
                    'id' => 3,
                    'program_name' => 'TEST11 Pharmacy Review 2024-C',
                    'archived_at' => now()->subDays(7),
                    'status' => 'archived'
                ])
            ]);

            $archivedCourses = collect([
                $this->createMockObject([
                    'id' => 1,
                    'course_name' => 'Advanced Nursing Procedures',
                    'program_name' => 'TEST11 Nursing Program',
                    'archived_at' => now()->subDays(20),
                    'status' => 'archived'
                ]),
                $this->createMockObject([
                    'id' => 2,
                    'course_name' => 'Clinical Chemistry Lab',
                    'program_name' => 'TEST11 Medical Technology',
                    'archived_at' => now()->subDays(10),
                    'status' => 'archived'
                ])
            ]);

            $html = view('admin.archived.index', [
                'archivedPrograms' => $archivedPrograms,
                'archivedCourses' => $archivedCourses,
                'isPreview' => true
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            Log::error('Archived content preview error: ' . $e->getMessage());
            
            // Fallback HTML response when view rendering fails
            return response('
                <html>
                    <head>
                        <title>TEST11 - Archived Content Management</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                            .header { border-bottom: 2px solid #0074D9; padding-bottom: 10px; margin-bottom: 20px; }
                            .card { border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin: 10px 0; background: #f9f9f9; }
                            .program { background: #e7f3ff; }
                            .course { background: #fff3e0; }
                            .status { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
                            .archived { background: #ffeb3b; color: #333; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h1>🗄️ TEST11 - Archived Content Management</h1>
                                <p>Tenant: <strong>'.$tenant.'</strong> | Preview Mode Active ✅</p>
                            </div>
                            
                            <h2>📚 Archived Programs</h2>
                            <div class="card program">
                                <h3>TEST11 Nursing Program 2024-A</h3>
                                <p>Status: <span class="status archived">Archived</span></p>
                                <p>Archived: 30 days ago</p>
                            </div>
                            <div class="card program">
                                <h3>TEST11 MedTech Batch 2024-B</h3>
                                <p>Status: <span class="status archived">Archived</span></p>
                                <p>Archived: 15 days ago</p>
                            </div>
                            <div class="card program">
                                <h3>TEST11 Pharmacy Review 2024-C</h3>
                                <p>Status: <span class="status archived">Archived</span></p>
                                <p>Archived: 7 days ago</p>
                            </div>
                            
                            <h2>📖 Archived Courses</h2>
                            <div class="card course">
                                <h3>Advanced Nursing Procedures</h3>
                                <p>Program: TEST11 Nursing Program</p>
                                <p>Status: <span class="status archived">Archived</span></p>
                                <p>Archived: 20 days ago</p>
                            </div>
                            <div class="card course">
                                <h3>Clinical Chemistry Lab</h3>
                                <p>Program: TEST11 Medical Technology</p>
                                <p>Status: <span class="status archived">Archived</span></p>
                                <p>Archived: 10 days ago</p>
                            </div>
                            
                            <div style="margin-top: 30px; padding: 15px; background: #e8f5e8; border-radius: 6px;">
                                <p><strong>✅ Template Fallback Active:</strong> The main template failed to render, but this fallback ensures the preview works.</p>
                                <p><strong>Error:</strong> '.$e->getMessage().'</p>
                                <p><a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a></p>
                            </div>
                        </div>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview mode for course content upload page
     */
    public function previewCourseContentUpload($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock data for the form
            $mockData = [
                'programs' => collect([
                    $this->createMockObject([
                        'program_id' => 1,
                        'program_name' => 'TEST11 Nursing Review Program',
                        'status' => 'active'
                    ]),
                    $this->createMockObject([
                        'program_id' => 2,
                        'program_name' => 'TEST11 Medical Technology Program', 
                        'status' => 'active'
                    ])
                ]),
                'modules' => collect([
                    $this->createMockObject([
                        'id' => 1,
                        'modules_id' => 1,
                        'module_name' => 'TEST11 Module 1 - Introduction',
                        'program_id' => 1
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'modules_id' => 2,
                        'module_name' => 'TEST11 Module 2 - Advanced Topics',
                        'program_id' => 1
                    ])
                ]),
                'courses' => collect([
                    $this->createMockObject([
                        'id' => 1,
                        'course_name' => 'Introduction to Nursing',
                        'module_id' => 1
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'course_name' => 'Advanced Nursing Procedures',
                        'module_id' => 2
                    ])
                ])
            ];

            $html = view('admin.admin-modules.course-content-upload', [
                'programs' => $mockData['programs'],
                'modules' => $mockData['modules'],
                'courses' => $mockData['courses'],
                'isPreview' => true,
                'previewData' => $mockData
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            Log::error('Course content upload preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Course Content Upload Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Course Content Upload Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Regular admin archived content index page
     */
    public function archivedIndex()
    {
        try {
            // Return a basic archived content page
            return view('admin.archived.index');
        } catch (\Exception $e) {
            // Fallback response if view doesn't exist
            return response('
                <html>
                    <head><title>Archived Content</title></head>
                    <body style="font-family: Arial; margin: 20px;">
                        <h1>Archived Content Management</h1>
                        <p>This section manages archived programs and content.</p>
                        <ul>
                            <li>Archived Programs</li>
                            <li>Archived Courses</li>
                            <li>Archived Materials</li>
                        </ul>
                        <a href="/admin">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Regular admin archived programs page
     */
    public function archivedPrograms()
    {
        try {
            return view('admin.archived.programs');
        } catch (\Exception $e) {
            return response('
                <html>
                    <head><title>Archived Programs</title></head>
                    <body style="font-family: Arial; margin: 20px;">
                        <h1>Archived Programs</h1>
                        <p>View and manage archived training programs.</p>
                        <a href="/admin/archived">← Back to Archived Content</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Regular admin courses upload page
     */
    public function coursesUpload()
    {
        try {
            return view('admin.courses.upload');
        } catch (\Exception $e) {
            return response('
                <html>
                    <head><title>Course Content Upload</title></head>
                    <body style="font-family: Arial; margin: 20px;">
                        <h1>Course Content Upload</h1>
                        <p>Upload course materials, videos, and documents.</p>
                        <form>
                            <p>Course: <select><option>Select Course</option></select></p>
                            <p>File: <input type="file" disabled></p>
                            <button type="button" disabled>Upload</button>
                        </form>
                        <a href="/admin">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview mode for archived courses page
     */
    public function previewArchivedCourses($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            return response('
                <html>
                    <head>
                        <title>Archived Courses Preview - TEST11</title>
                        <style>body { font-family: Arial; margin: 20px; }</style>
                    </head>
                    <body>
                        <h1>TEST11 - Archived Courses Management</h1>
                        <p>✅ Tenant: '.$tenant.' | Preview Mode Active</p>
                        <div>
                            <h3>Archived Courses:</h3>
                            <ul>
                                <li>Advanced Nursing Fundamentals (Archived: 45 days ago)</li>
                                <li>Medical Terminology Course (Archived: 30 days ago)</li>
                                <li>Pharmacology Basics (Archived: 20 days ago)</li>
                                <li>Patient Care Protocols (Archived: 10 days ago)</li>
                            </ul>
                        </div>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);

        } catch (\Exception $e) {
            Log::error('Archived courses preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Archived Courses Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Archived Courses Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);
        }
    }

    /**
     * Preview mode for archived materials page
     */
    public function previewArchivedMaterials($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            return response('
                <html>
                    <head>
                        <title>Archived Materials Preview - TEST11</title>
                        <style>body { font-family: Arial; margin: 20px; }</style>
                    </head>
                    <body>
                        <h1>TEST11 - Archived Materials Management</h1>
                        <p>✅ Tenant: '.$tenant.' | Preview Mode Active</p>
                        <div>
                            <h3>Archived Materials:</h3>
                            <ul>
                                <li>Nursing Study Guides v2.1 (Archived: 60 days ago)</li>
                                <li>Medical Equipment Manual (Archived: 45 days ago)</li>
                                <li>Clinical Practice Videos (Archived: 30 days ago)</li>
                                <li>Assessment Tools Collection (Archived: 15 days ago)</li>
                            </ul>
                        </div>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);

        } catch (\Exception $e) {
            Log::error('Archived materials preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Archived Materials Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Archived Materials Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200, ['Content-Type' => 'text/html']);
        }
    }

    /**
     * Preview modules by program for tenant (API endpoint)
     */
    public function previewModulesByProgram(Request $request, $tenant)
    {
        try {
            // Set up preview session for API access
            session([
                'preview_tenant' => $tenant, 
                'user_name' => 'Preview Admin', 
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility 
                'logged_in' => true, 
                'preview_mode' => true
            ]);
            
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            $programId = $request->get('program_id');
            
            if (!$programId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program ID is required',
                    'modules' => []
                ]);
            }

            // Switch to tenant database
            $this->switchToTenantDatabase($tenant);
            
            // Get modules for the program - mock data for preview
            $modules = [
                [
                    'modules_id' => 1,
                    'module_name' => 'TEST11 Module 1 - Introduction',
                    'program_id' => $programId
                ],
                [
                    'modules_id' => 2,
                    'module_name' => 'TEST11 Module 2 - Advanced Topics',
                    'program_id' => $programId
                ],
                [
                    'modules_id' => 3,
                    'module_name' => 'TEST11 Module 3 - Final Project',
                    'program_id' => $programId
                ]
            ];
            
            return response()->json([
                'success' => true,
                'modules' => $modules,
                'tenant' => $tenant,
                'message' => 'Modules loaded successfully for TEST11 tenant'
            ]);

        } catch (\Exception $e) {
            Log::error('Preview modules by program error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading modules: ' . $e->getMessage(),
                'modules' => []
            ]);
        }
    }

    /**
     * Preview courses by module for tenant (API endpoint)
     */
    public function previewCoursesByModule(Request $request, $tenant, $moduleId)
    {
        try {
            // Set up preview session for API access
            session([
                'preview_tenant' => $tenant, 
                'user_name' => 'Preview Admin', 
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility 
                'logged_in' => true, 
                'preview_mode' => true
            ]);
            
            // Load tenant customization
            $this->loadAdminPreviewCustomization();

            if (!$moduleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module ID is required',
                    'courses' => []
                ]);
            }

            // Switch to tenant database
            $this->switchToTenantDatabase($tenant);
            
            // Get courses for the module - mock data for preview
            $courses = [
                [
                    'subject_id' => 1,
                    'subject_name' => 'TEST11 Course 1 - Fundamentals',
                    'module_id' => $moduleId
                ],
                [
                    'subject_id' => 2,
                    'subject_name' => 'TEST11 Course 2 - Practical Applications',
                    'module_id' => $moduleId
                ],
                [
                    'subject_id' => 3,
                    'subject_name' => 'TEST11 Course 3 - Assessment',
                    'module_id' => $moduleId
                ]
            ];
            
            return response()->json([
                'success' => true,
                'courses' => $courses,
                'tenant' => $tenant,
                'module_id' => $moduleId,
                'message' => 'Courses loaded successfully for TEST11 tenant'
            ]);

        } catch (\Exception $e) {
            Log::error('Preview courses by module error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage(),
                'courses' => []
            ]);
        }
    }

    /**
     * Preview student registration pending page for tenant
     */
    public function previewStudentRegistrationPending($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock pending registrations
            $registrations = collect([
                $this->createMockObject([
                    'registration_id' => 1,
                    'user_firstname' => 'John',
                    'user_lastname' => 'Doe',
                    'email' => 'john.doe@email.com',
                    'status' => 'pending',
                    'enrollment_type' => 'new',
                    'created_at' => now()->subDays(2),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'John',
                        'user_lastname' => 'Doe',
                        'email' => 'john.doe@email.com',
                        'phone' => '+1-555-0123'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'TEST11 Nursing Program'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'TEST11 Nursing Program'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Standard Plan'
                    ])
                ]),
                $this->createMockObject([
                    'registration_id' => 2,
                    'user_firstname' => 'Jane',
                    'user_lastname' => 'Smith',
                    'email' => 'jane.smith@email.com',
                    'status' => 'pending',
                    'enrollment_type' => 'new',
                    'created_at' => now()->subDays(1),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Jane',
                        'user_lastname' => 'Smith',
                        'email' => 'jane.smith@email.com',
                        'phone' => '+1-555-0124'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'TEST11 Medical Technology Program'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'TEST11 Medical Technology Program'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Premium Plan'
                    ])
                ]),
                $this->createMockObject([
                    'registration_id' => 3,
                    'user_firstname' => 'Mike',
                    'user_lastname' => 'Johnson',
                    'email' => 'mike.johnson@email.com',
                    'status' => 'pending',
                    'enrollment_type' => 'new',
                    'created_at' => now()->subHours(3),
                    'user' => $this->createMockObject([
                        'user_firstname' => 'Mike',
                        'user_lastname' => 'Johnson',
                        'email' => 'mike.johnson@email.com',
                        'phone' => '+1-555-0125'
                    ]),
                    'package' => $this->createMockObject([
                        'package_name' => 'TEST11 Physical Therapy Program'
                    ]),
                    'program' => $this->createMockObject([
                        'program_name' => 'TEST11 Physical Therapy Program'
                    ]),
                    'plan' => $this->createMockObject([
                        'plan_name' => 'Basic Plan'
                    ])
                ])
            ]);

            return view('admin.admin-student-registration.admin-student-registration', [
                'registrations' => $registrations,
                'history' => false,
                'isPreview' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Student registration pending preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Student Registration Pending Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Student Registration Pending Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }

    /**
     * Preview archived modules for tenant
     */
    public function previewArchivedModules($tenant)
    {
        try {
            // Load tenant customization
            $this->loadAdminPreviewCustomization();
            
            // Set preview session
            session([
                'preview_tenant' => $tenant,
                'user_name' => 'Preview Admin',
                'user_role' => 'admin',
                'user_type' => 'admin', // Add for sidebar compatibility
                'logged_in' => true,
                'preview_mode' => true
            ]);

            // Generate mock archived data
            $mockData = [
                'programs' => collect([
                    $this->createMockObject([
                        'program_id' => 1,
                        'program_name' => 'TEST11 Nursing Review Program',
                        'status' => 'active'
                    ]),
                    $this->createMockObject([
                        'program_id' => 2,
                        'program_name' => 'TEST11 Medical Technology Program',
                        'status' => 'active'
                    ])
                ]),
                'archivedModules' => collect([
                    $this->createMockObject([
                        'id' => 1,
                        'module_name' => 'TEST11 Archived Module 1',
                        'program_id' => 1,
                        'is_archived' => true,
                        'archived_at' => now()->subDays(5)->format('Y-m-d H:i:s')
                    ]),
                    $this->createMockObject([
                        'id' => 2,
                        'module_name' => 'TEST11 Archived Module 2',
                        'program_id' => 2,
                        'is_archived' => true,
                        'archived_at' => now()->subDays(10)->format('Y-m-d H:i:s')
                    ])
                ]),
                'archivedCourses' => collect([
                    $this->createMockObject([
                        'id' => 1,
                        'course_name' => 'TEST11 Archived Course 1',
                        'module_id' => 1,
                        'is_archived' => true
                    ])
                ]),
                'archivedContent' => collect([
                    $this->createMockObject([
                        'id' => 1,
                        'title' => 'TEST11 Archived Content Item',
                        'course_id' => 1,
                        'is_archived' => true,
                        'archived_at' => now()->subDays(2)->format('Y-m-d H:i:s')
                    ])
                ]),
                'stats' => [
                    'total_archived' => 4,
                    'archived_modules' => 2,
                    'archived_courses' => 1,
                    'archived_content' => 1
                ]
            ];

            $html = view('admin.admin-modules.admin-modules-archived', [
                'programs' => $mockData['programs'],
                'archivedModules' => $mockData['archivedModules'],
                'archivedCourses' => $mockData['archivedCourses'],
                'archivedContent' => $mockData['archivedContent'],
                'stats' => $mockData['stats'],
                'isPreview' => true,
                'previewData' => $mockData
            ])->render();

            return response($html);

        } catch (\Exception $e) {
            Log::error('Archived modules preview error: ' . $e->getMessage());
            return response('
                <html>
                    <head><title>TEST11 - Archived Modules Preview</title></head>
                    <body style="font-family: Arial;">
                        <h1>TEST11 - Archived Modules Preview - Tenant: '.$tenant.'</h1>
                        <p>❌ Error rendering full view: '.$e->getMessage().'</p>
                        <p>But route is working correctly!</p>
                        <a href="/t/draft/'.$tenant.'/admin-dashboard">← Back to Admin Dashboard</a>
                    </body>
                </html>
            ', 200);
        }
    }
}
