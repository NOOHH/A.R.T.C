<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Package;
use App\Models\FormRequirement;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Get program type for dynamic validation
        $programType = $request->input('enrollment_type') === 'full' ? 'complete' : 'modular';
        
        // Get active form requirements for the selected program type
        $formRequirements = FormRequirement::active()
            ->forProgram($programType)
            ->ordered()
            ->get();

        // Check if user is already logged in (multiple enrollment scenario)
        $isLoggedIn = session('user_id') !== null;

        // Base validation rules for core fields
        $rules = [
            'Start_Date' => 'required|date',
            'program_id' => 'required|integer|exists:programs,program_id',
            'package_id' => 'required|integer|exists:packages,package_id',
            'enrollment_type' => 'required|in:modular,full',
            'learning_mode' => 'required|in:synchronous,asynchronous,Synchronous,Asynchronous',
            'registration_mode' => 'nullable|in:sync,async',
            'selected_modules' => 'nullable|array',
            'selected_modules.*' => 'exists:modules,modules_id',
        ];

        // Add account validation rules only for new users (not logged in)
        if (!$isLoggedIn) {
            $rules['user_firstname'] = 'required|string|max:255';
            $rules['user_lastname'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|confirmed|min:6';
        }
        
        // For logged-in users, check if they're already enrolled in this program
        if ($isLoggedIn) {
            $user = User::find(session('user_id'));
            $student = Student::where('user_id', $user->user_id)->first();
            
            if ($student) {
                $existingEnrollment = $student->enrollments()
                    ->where('program_id', $request->input('program_id'))
                    ->first();
                    
                if ($existingEnrollment) {
                    return redirect()->back()
                        ->withErrors(['program_id' => 'You are already enrolled in this program.'])
                        ->withInput();
                }
            }
        }        // Add dynamic field validation rules based on form requirements
        foreach ($formRequirements as $requirement) {
            // Skip section type fields as they don't need validation
            if ($requirement->field_type === 'section') {
                continue;
            }
            
            $fieldName = $requirement->field_name;
            $fieldRules = [];

            // Only add validation if the field is actually being submitted or is required
            $fieldIsSubmitted = $request->has($fieldName) || $request->hasFile($fieldName);
            $fieldIsRequired = $requirement->is_required;
            
            // Skip validation for fields that are required but not submitted (prevents errors for new fields)
            if ($fieldIsRequired && !$fieldIsSubmitted) {
                // Log this for debugging
                Log::warning("Required field '$fieldName' not found in form submission", [
                    'field_type' => $requirement->field_type,
                    'program_type' => $programType,
                    'submitted_fields' => array_keys($request->all())
                ]);
                continue;
            }

            // Add required rule if field is required and submitted
            if ($fieldIsRequired && $fieldIsSubmitted) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add specific validation rules based on field type
            switch ($requirement->field_type) {
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string|max:255';
                    break;
                case 'email':
                    $fieldRules[] = 'email|max:255';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'tel':
                    $fieldRules[] = 'string|max:20';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    if ($fieldIsSubmitted) {
                        $fieldRules[] = 'file|mimes:pdf,jpg,jpeg,png|max:2048';
                    }
                    break;
                case 'select':
                case 'radio':
                    if ($requirement->field_options && is_array($requirement->field_options)) {
                        $options = implode(',', $requirement->field_options);
                        $fieldRules[] = "in:$options";
                    } else {
                        $fieldRules[] = 'string|max:255';
                    }
                    break;
                case 'checkbox':
                    $fieldRules[] = 'nullable|in:0,1';
                    break;
                case 'module_selection':
                    $fieldRules[] = 'array';
                    if ($fieldIsRequired) {
                        $fieldRules[] = 'min:1';
                    }
                    break;
            }

            // Add custom validation rules if specified
            if ($requirement->validation_rules) {
                $customRules = explode('|', $requirement->validation_rules);
                $fieldRules = array_merge($fieldRules, $customRules);
            }

            // Only add rule if field is submitted or if it's a file field that could be optional
            if ($fieldIsSubmitted || (!$fieldIsRequired && in_array($requirement->field_type, ['file']))) {
                $rules[$fieldName] = implode('|', $fieldRules);
            }
        }

        // Handle module selection validation separately
        if (isset($rules['selected_modules']) && $request->has('selected_modules')) {
            $moduleIds = $request->input('selected_modules');
            if (is_array($moduleIds)) {
                foreach ($moduleIds as $moduleId) {
                    if (!is_numeric($moduleId)) {
                        throw new \InvalidArgumentException("Invalid module ID: $moduleId");
                    }
                }
            }
        }

        $validated = $request->validate($rules);

        $enrollmentType = $validated['enrollment_type'] === 'full' ? 'Complete' : 'Modular';
        
        // Normalize learning mode to proper case
        $learningMode = ucfirst(strtolower($validated['learning_mode']));

        // Check if user is already logged in (multiple enrollment scenario)
        $user = null;
        $student = null;
        
        if (session('user_id')) {
            // User is already logged in - this is a multiple enrollment
            $user = User::find(session('user_id'));
            $student = Student::where('user_id', $user->user_id)->first();
            
            Log::info('Multiple enrollment detected', [
                'user_id' => $user->user_id,
                'student_id' => $student ? $student->student_id : 'none',
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id']
            ]);
        } else {
            // New user registration
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_firstname' => $validated['user_firstname'],
                'user_lastname' => $validated['user_lastname'],
                'role' => 'unverified',
            ]);
            
            Log::info('New user created', [
                'user_id' => $user->user_id,
                'email' => $user->email
            ]);
        }

        // Create registration record first
        $registration = new Registration();
        $registration->user_id = $user->user_id;
        $registration->Start_Date = $validated['Start_Date'];
        $registration->status = 'pending';
        
        // Save package, program, and plan information
        $registration->package_id = $validated['package_id'];
        $registration->program_id = $validated['program_id'];
        $registration->plan_id = $validated['plan_id'] ?? null;
        $registration->enrollment_type = $enrollmentType;
        $registration->learning_mode = $learningMode;
        
        // Get and save the names for easy display
        if ($validated['package_id']) {
            $package = \App\Models\Package::find($validated['package_id']);
            $registration->package_name = $package ? $package->package_name : 'N/A';
        }
        
        if ($validated['program_id']) {
            $program = \App\Models\Program::find($validated['program_id']);
            $registration->program_name = $program ? $program->program_name : 'N/A';
        }
        
        if (isset($validated['plan_id']) && $validated['plan_id']) {
            $plan = \App\Models\Plan::find($validated['plan_id']);
            $registration->plan_name = $plan ? $plan->plan_name : 'N/A';
        } else {
            $registration->plan_name = $enrollmentType === 'full' ? 'Full Program' : 'Modular';
        }

        // Handle dynamic fields from form requirements
        $dynamicFields = [];
        
        // Add registration mode to dynamic fields if provided
        if (isset($validated['registration_mode'])) {
            $dynamicFields['registration_mode'] = $validated['registration_mode'];
        }
        
        foreach ($formRequirements as $requirement) {
            if ($requirement->field_type === 'section') {
                continue;
            }
            
            $fieldName = $requirement->field_name;
            if (isset($validated[$fieldName])) {
                $value = $validated[$fieldName];
                
                // Handle different field types
                switch ($requirement->field_type) {
                    case 'checkbox':
                        $value = $value ? 1 : 0;
                        break;
                    case 'module_selection':
                        $value = is_array($value) ? $value : [$value];
                        break;
                    case 'file':
                        // Handle file uploads
                        if ($request->hasFile($fieldName)) {
                            $file = $request->file($fieldName);
                            $filename = time() . '_' . $fieldName . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('uploads/registrations', $filename, 'public');
                            $value = $path;
                        }
                        break;
                }
                
                // Try to save directly to database column if it exists
                if (FormRequirement::columnExists($fieldName)) {
                    $registration->$fieldName = $value;
                } else {
                    // Fallback to dynamic_fields for backward compatibility
                    $dynamicFields[$fieldName] = $value;
                }
                
                // Also try to map to existing registration columns if they exist (legacy support)
                $this->mapDynamicFieldToRegistrationColumn($registration, $fieldName, $value);
            }
        }
        
        // Store any remaining dynamic fields as JSON (only if there are any)
        if (!empty($dynamicFields)) {
            $registration->dynamic_fields = $dynamicFields;
        }
        
        // Handle legacy file uploads (for backward compatibility)
        $this->handleLegacyFileUploads($request, $registration);
        
        $registration->save();

        // Create the enrollment record with registration_id for tracking
        $enrollment = Enrollment::create([
            'student_id' => $student ? $student->student_id : null, // Will be updated after student creation or during approval
            'program_id' => $validated['program_id'],
            'package_id' => $validated['package_id'],
            'enrollment_type' => $enrollmentType,
            'learning_mode' => $learningMode,
            'registration_id' => $registration->registration_id, // Link to the registration for admin approval process
            'enrollment_status' => 'pending',
        ]);

        // Create or update Student record
        if (!$student) {
            // Create new student record with year-month-increment format
            $currentYear = date('Y');
            $currentMonth = date('m');
            
            // Get the latest student ID for the current month to generate the next increment
            $latestStudent = Student::where('student_id', 'LIKE', $currentYear . '-' . $currentMonth . '-%')
                ->orderBy('student_id', 'desc')
                ->first();
            
            $increment = 1;
            if ($latestStudent) {
                // Extract the increment from the latest student ID
                $lastIncrement = (int) substr($latestStudent->student_id, -5);
                $increment = $lastIncrement + 1;
            }
            
            $studentId = $currentYear . '-' . $currentMonth . '-' . str_pad($increment, 5, '0', STR_PAD_LEFT);
            
            $student = Student::create([
                'student_id' => $studentId,
                'user_id' => $user->user_id,
                'firstname' => $registration->firstname ?? $validated['user_firstname'] ?? '',
                'middlename' => $registration->middlename ?? '',
                'lastname' => $registration->lastname ?? $validated['user_lastname'] ?? '',
                'student_school' => $registration->student_school ?? $registration->school_name ?? '',
                'street_address' => $registration->street_address ?? '',
                'state_province' => $registration->state_province ?? '',
                'city' => $registration->city ?? '',
                'zipcode' => $registration->zipcode ?? '',
                'contact_number' => $registration->contact_number ?? $registration->phone_number ?? '',
                'emergency_contact_number' => $registration->emergency_contact_number ?? '',
                'good_moral' => $registration->good_moral ?? '',
                'PSA' => $registration->PSA ?? '',
                'Course_Cert' => $registration->Course_Cert ?? '',
                'TOR' => $registration->TOR ?? '',
                'Cert_of_Grad' => $registration->Cert_of_Grad ?? '',
                'Undergraduate' => $registration->Undergraduate ?? '',
                'Graduate' => $registration->Graduate ?? '',
                'photo_2x2' => $registration->photo_2x2 ?? '',
                'Start_Date' => $validated['Start_Date'],
                'email' => $user->email,
                'is_archived' => false,
            ]);
            
            Log::info('New student record created', [
                'student_id' => $student->student_id,
                'user_id' => $user->user_id
            ]);
        } else {
            // For existing students, update the student record with new information if provided
            $updateData = [];
            if (isset($registration->firstname) && $registration->firstname) {
                $updateData['firstname'] = $registration->firstname;
            }
            if (isset($registration->lastname) && $registration->lastname) {
                $updateData['lastname'] = $registration->lastname;
            }
            if (isset($registration->middlename) && $registration->middlename) {
                $updateData['middlename'] = $registration->middlename;
            }
            // Add other fields as needed
            
            if (!empty($updateData)) {
                $student->update($updateData);
                Log::info('Existing student record updated', [
                    'student_id' => $student->student_id,
                    'updated_fields' => array_keys($updateData)
                ]);
            }
        }

        // Update enrollment with student_id if student was created
        if (!$student && isset($student)) {
            $enrollment->update([
                'student_id' => $student->student_id
            ]);
        }

        // Handle module selection for modular enrollment
        if ($validated['enrollment_type'] === 'modular' && !empty($validated['selected_modules'])) {
            $moduleIds = is_array($validated['selected_modules']) 
                ? $validated['selected_modules'] 
                : [$validated['selected_modules']];
            
            $registration->modules()->sync($moduleIds);
        }

        // Set success message based on whether this is a new user or multiple enrollment
        $successMessage = $isLoggedIn 
            ? 'Successfully enrolled in additional program! You can view all your enrollments in your dashboard.'
            : 'Registration submitted successfully!';

        return redirect()->route('registration.success')
            ->with('success', $successMessage);
    }

    public function showRegistrationForm(Request $request)
    {
        $enrollmentType = 'full'; // Set to full since this is the full enrollment route
        $packages = Package::all();
        
        // Get requirements for "complete" (full) program
        $formRequirements = FormRequirement::active()
            ->forProgram('complete')
            ->ordered()
            ->get();

        // Get existing student data if user is logged in
        $student = null;
        $enrolledProgramIds = [];
        
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
            
            // Get all program IDs that the student is already enrolled in
            if ($student) {
                $enrolledProgramIds = $student->enrollments()
                    ->pluck('program_id')
                    ->toArray();
            }
        }
        
        // Filter out programs that the student is already enrolled in
        $programs = Program::where('is_archived', false)
            ->whereNotIn('program_id', $enrolledProgramIds)
            ->get();

        return view('registration.Full_enrollment', compact('enrollmentType', 'programs', 'packages', 'student', 'formRequirements'));
    }


    public function showEnrollmentSelection()
    {
        return view('enrollment');
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        
        // Check if email exists in users table
        $exists = User::where('email', $email)->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email already exists' : 'Email is available'
        ]);
    }

    /**
     * Map dynamic field to existing registration column if it exists
     */
    private function mapDynamicFieldToRegistrationColumn($registration, $fieldName, $value)
    {
        // Mapping between dynamic field names and registration table columns
        $fieldMapping = [
            'firstname' => 'firstname',
            'First_Name' => 'firstname',  // Added mapping for new field name
            'middlename' => 'middlename',
            'Middle_Name' => 'middlename',  // Added mapping for new field name
            'lastname' => 'lastname',
            'Last_Name' => 'lastname',  // Added mapping for new field name
            'school_name' => 'student_school',
            'phone_number' => 'contact_number',
            'emergency_contact' => 'emergency_contact_number',
            'street_address' => 'street_address',
            'state_province' => 'state_province',
            'city' => 'city',
            'zipcode' => 'zipcode',
        ];
        
        if (isset($fieldMapping[$fieldName])) {
            $columnName = $fieldMapping[$fieldName];
            $registration->$columnName = $value;
        }
    }
    
    /**
     * Handle legacy file uploads for backward compatibility
     */
    private function handleLegacyFileUploads($request, $registration)
    {
        $legacyFileFields = [
            'good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad',
            'Undergraduate', 'Graduate', 'photo_2x2'
        ];
        
        foreach ($legacyFileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/registrations', $filename, 'public');
                $registration->$field = $path;
            }
        }
    }
}
