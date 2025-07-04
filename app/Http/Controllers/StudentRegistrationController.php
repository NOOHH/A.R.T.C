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

        // Base validation rules for core fields
        $rules = [
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'Start_Date' => 'required|date',
            'program_id' => 'required|integer|exists:programs,program_id',
            'package_id' => 'required|integer|exists:packages,package_id',
            'enrollment_type' => 'required|in:modular,full',
            'registration_mode' => 'nullable|in:sync,async',
            'selected_modules' => 'nullable|array',
            'selected_modules.*' => 'exists:modules,modules_id',
        ];        // Add dynamic field validation rules based on form requirements
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

        $enrollment = Enrollment::create([
            'program_id' => $validated['program_id'],
            'package_id' => $validated['package_id'],
            'enrollment_type' => $enrollmentType,
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_firstname' => $validated['user_firstname'],
            'user_lastname' => $validated['user_lastname'],
            'role' => 'unverified',
            'enrollment_id' => $enrollment->enrollment_id,
        ]);

        $package = Package::find($validated['package_id']);
        $program = Program::find($validated['program_id']);
        $planName = $enrollmentType;

        // Create registration record
        $registration = new Registration();
        $registration->user_id = $user->user_id;
        $registration->package_id = $validated['package_id'];
        $registration->program_id = $validated['program_id'];
        $registration->plan_id = $request->input('plan_id');
        $registration->package_name = $package ? $package->package_name : null;
        $registration->program_name = $program ? $program->program_name : null;
        $registration->plan_name = $planName;
        $registration->Start_Date = $validated['Start_Date'];
        $registration->status = 'pending';

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

        // Handle module selection for modular enrollment
        if ($validated['enrollment_type'] === 'modular' && !empty($validated['selected_modules'])) {
            $moduleIds = is_array($validated['selected_modules']) 
                ? $validated['selected_modules'] 
                : [$validated['selected_modules']];
            
            $registration->modules()->sync($moduleIds);
        }

        return redirect()->route('registration.success')
            ->with('success', 'Registration submitted successfully!');
    }

    public function showRegistrationForm(Request $request)
    {
        $enrollmentType = 'full'; // Set to full since this is the full enrollment route
        $programs = Program::all();
        $packages = Package::all();
        
        // Get requirements for "complete" (full) program
        $formRequirements = FormRequirement::active()
            ->forProgram('complete')
            ->ordered()
            ->get();

        // Get existing student data if user is logged in
        $student = null;
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
        }

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
            'middlename' => 'middlename', 
            'lastname' => 'lastname',
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
