<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Package;
use App\Models\FormRequirement;
use App\Models\StudentBatch;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        Log::info('========== REGISTRATION ATTEMPT STARTED ==========');
        Log::info('Request method: ' . $request->method());
        Log::info('Request headers: ', $request->headers->all());
        Log::info('Request data: ', $request->all());
        
        // Check if reCAPTCHA is enabled
        $recaptchaEnabled = false; // Temporarily disable for testing
        
        // Validate reCAPTCHA first (make it optional for now)
        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        if ($recaptchaEnabled && !$recaptchaResponse) {
            Log::warning('reCAPTCHA response missing but required');
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please complete the CAPTCHA verification.');
        }

        // Verify reCAPTCHA with Google (only if enabled and response provided)
        if ($recaptchaEnabled && $recaptchaResponse) {
            $recaptchaSecret = '6Leb5IArAAAAAFqVkr7SWj9Zf5pmk7YPRvqvGArC';
            $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
            
            $recaptchaData = [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip()
            ];

            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, $recaptchaUrl);
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($recaptchaData));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            curl_close($verify);

            $responseData = json_decode($response, true);
            
            if (!$responseData['success']) {
                Log::warning('reCAPTCHA verification failed', ['response' => $responseData]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'CAPTCHA verification failed. Please try again.');
            }
        } else {
            Log::info('reCAPTCHA verification skipped - not enabled or no response provided');
        }
        
        // DEBUG: Check if batch_id is in the request
        if ($request->has('batch_id')) {
            Log::info('batch_id found in request:', ['batch_id' => $request->batch_id]);
        }
        
        try {
            // Get program type for dynamic validation (map to database values)
            $enrollmentType = $request->input('enrollment_type');
            $programType = $enrollmentType === 'Full' ? 'full' : 'modular';
            
            // Get active form requirements for the selected program type
            $formRequirements = FormRequirement::active()
                ->forProgram($programType)
                ->ordered()
                ->get();

            // Base validation rules for final registration
            $rules = [
                'learning_mode' => 'required|in:synchronous,asynchronous,Synchronous,Asynchronous',
                'package_id' => 'required|integer|exists:packages,package_id',
                'enrollment_type' => 'required|in:Full,Modular',
                'program_id' => 'required|integer|exists:programs,program_id',
                'registration_mode' => 'nullable|in:sync,async',
                'sync_async_mode' => 'nullable|in:sync,async',
                'education_level' => 'required|in:Undergraduate,Graduate',
                'start_date' => 'required|date',
                // batch_id is optional and will be stored in session, not in registrations table
                'batch_id' => 'nullable|integer'
            ];

            // Add selected modules validation for modular enrollment
            if ($request->enrollment_type === 'Modular') {
                $rules['selected_modules'] = 'required|json';
            }

            // Add start date validation only for asynchronous mode
            $learningMode = strtolower($request->input('learning_mode'));
            if ($learningMode === 'asynchronous') {
                $rules['Start_Date'] = 'required|date';
            }

            // Add account validation rules only for new users (not logged in)
            if (!session('user_id') && !auth()->check()) {
                $rules['user_firstname'] = 'required|string|max:255';
                $rules['user_lastname'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|confirmed|min:8';
            }

            // Add dynamic form field validation only for active fields
            foreach ($formRequirements as $field) {
                // Skip sections as they don't need validation
                if ($field->field_type === 'section') {
                    continue;
                }
                
                if ($field->is_required) {
                    $rules[$field->field_name] = 'required';
                    if ($field->field_type === 'file') {
                        $rules[$field->field_name] .= '|file|max:10240'; // 10MB max
                    }
                }
            }

            // Validate request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Final registration validation failed', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Registration validation failed');
            }

            DB::beginTransaction();

            // Create or get user
            $user = null;
            
            if (auth()->check()) {
                $user = auth()->user();
                Log::info('Using authenticated user', ['user_id' => $user->user_id]);
            } elseif (session('user_id')) {
                $user = User::find(session('user_id'));
                if ($user) {
                    Log::info('Using session user', ['user_id' => $user->user_id]);
                }
            }
            
            // Create new user if no existing user found
            if (!$user) {
                // Validate that we have user creation data
                if (!$request->has('user_firstname') || !$request->has('user_lastname') || 
                    !$request->has('email') || !$request->has('password')) {
                    throw new \Exception('User creation data missing. Please provide firstname, lastname, email, and password.');
                }
                
                Log::info('Creating new user with data:', [
                    'user_firstname' => $request->user_firstname,
                    'user_lastname' => $request->user_lastname,
                    'email' => $request->email
                ]);
                
                $user = new User();
                $user->user_firstname = $request->user_firstname;
                $user->user_lastname = $request->user_lastname;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = 'student';
                // Set default values for required fields
                $user->admin_id = 1; // Default admin_id
                $user->directors_id = 1; // Default directors_id
                
                if (!$user->save()) {
                    throw new \Exception('Failed to create user account');
                }
                
                // Refresh the user instance to ensure we have the ID
                $user = $user->fresh();
                
                if (!$user || !$user->user_id) {
                    throw new \Exception('User created but ID not available');
                }
                
                Log::info('Created new user', ['user_id' => $user->user_id]);
                
                // Set complete session for future requests
                session([
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_name,
                    'user_email' => $user->user_email,
                    'user_role' => 'student',
                    'logged_in' => true
                ]);
            }

            if (!$user || !$user->user_id) {
                throw new \Exception('Unable to create or find valid user');
            }

            // Create student registration with only base fields
            $registration = new Registration();
            $registration->user_id = $user->user_id;
            $registration->program_id = $request->program_id;
            $registration->package_id = $request->package_id;
            $registration->enrollment_type = $request->enrollment_type;
            $registration->learning_mode = strtolower($request->learning_mode);
            
            // Store sync/async mode (new field)
            if ($request->sync_async_mode) {
                $registration->sync_async_mode = $request->sync_async_mode;
            }
            
            // Store education level (new field)
            if ($request->education_level) {
                $registration->education_level = $request->education_level;
            }
            
            // Store selected modules for modular enrollment
            if ($request->enrollment_type === 'Modular' && $request->selected_modules) {
                $registration->selected_modules = $request->selected_modules;
            }
            
            // Handle start date based on learning mode
            if ($learningMode === 'synchronous') {
                // For synchronous mode, set start date to 2 weeks from registration
                $registration->start_date = now()->addDays(14)->format('Y-m-d');
            } else {
                // For asynchronous mode, use the user-provided start date
                $registration->start_date = $request->start_date ?? $request->Start_Date;
            }
            
            $registration->status = 'pending';
            
            // CRITICAL: Ensure batch_id is NEVER set on registration object
            // batch_id is not stored in registrations table - it belongs in student_batches
            // Store batch selection in session for later use during enrollment
            if ($request->batch_id) {
                session(['selected_batch_id' => $request->batch_id]);
                Log::info('Batch ID stored in session for later enrollment', ['batch_id' => $request->batch_id]);
            }

            // Only save dynamic form fields that have actual database columns
            foreach ($formRequirements as $field) {
                $fieldName = $field->field_name;
                
                // Skip sections as they don't need database columns
                if ($field->field_type === 'section') {
                    continue;
                }
                
                // EXPLICITLY SKIP batch_id - it should never be stored in registrations table
                if ($fieldName === 'batch_id') {
                    Log::info('Skipping batch_id field - will be handled separately during enrollment');
                    continue;
                }
                
                // Check if the column exists in the registrations table
                try {
                    if (FormRequirement::columnExists($fieldName) && $request->has($fieldName)) {
                        if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                            // Handle file uploads
                            $path = $request->file($fieldName)->store('uploads/registrations', 'public');
                            $registration->{$fieldName} = $path;
                        } elseif ($field->field_type === 'module_selection' && $request->has($fieldName)) {
                            // Handle module selection (array of module IDs)
                            $selectedModules = $request->input($fieldName, []);
                            if (is_array($selectedModules)) {
                                $registration->{$fieldName} = json_encode($selectedModules);
                            } else {
                                $registration->{$fieldName} = $selectedModules;
                            }
                        } elseif ($field->field_type === 'checkbox') {
                            // Handle checkbox fields (convert to boolean)
                            $registration->{$fieldName} = $request->has($fieldName) ? 1 : 0;
                        } else {
                            // Handle regular fields
                            $fieldValue = $request->input($fieldName);
                            if (!empty($fieldValue) || $fieldValue === '0') {
                                // EXTRA SAFETY: Never allow batch_id to be set via dynamic fields
                                if ($fieldName === 'batch_id') {
                                    Log::warning("Attempted to set batch_id via dynamic field - blocked!");
                                    continue;
                                }
                                $registration->{$fieldName} = $fieldValue;
                            }
                        }
                    } else {
                        // Log fields that are skipped because column doesn't exist
                        if ($request->has($fieldName)) {
                            Log::warning("Field {$fieldName} skipped - column doesn't exist in registrations table", [
                                'field_type' => $field->field_type,
                                'field_value' => $request->input($fieldName),
                                'suggestion' => 'Run: php artisan form-requirements:sync'
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error processing field {$fieldName}: " . $e->getMessage());
                    // Continue processing other fields
                }
            }

            // Map common user fields to registration columns if they exist
            try {
                if (FormRequirement::columnExists('firstname')) {
                    $registration->firstname = $user->user_firstname;
                }
                if (FormRequirement::columnExists('lastname')) {
                    $registration->lastname = $user->user_lastname;
                }
            } catch (\Exception $e) {
                Log::warning("Error mapping user fields to registration: " . $e->getMessage());
                // Continue with registration save
            }
            
            Log::info('Saving registration with data:', [
                'user_id' => $registration->user_id,
                'program_id' => $registration->program_id,
                'package_id' => $registration->package_id,
                'enrollment_type' => $registration->enrollment_type
            ]);
            
            // DEBUG: Check if batch_id is somehow set on the registration object
            if (isset($registration->batch_id)) {
                Log::error('ERROR: batch_id is set on registration object!', [
                    'batch_id' => $registration->batch_id,
                    'registration_attributes' => $registration->getAttributes()
                ]);
                // REMOVE batch_id from registration to prevent database error
                unset($registration->batch_id);
                Log::info('Removed batch_id from registration object');
            }
            
            // ADDITIONAL SAFETY: Ensure batch_id is not in the attributes array
            $attributes = $registration->getAttributes();
            if (array_key_exists('batch_id', $attributes)) {
                Log::warning('Found batch_id in registration attributes, removing it');
                $registration->unsetRelation('batch_id');
                $registration->offsetUnset('batch_id');
            }
            
            $registration->save();
            
            Log::info('Registration saved successfully', ['registration_id' => $registration->id]);
            
            // CREATE STUDENT RECORD - This was missing!
            // Create a student record with data from the registration
            
            // Generate unique student ID
            $studentId = $this->generateStudentId();
            
            $studentData = [
                'student_id' => $studentId,
                'user_id' => $user->user_id,
                'firstname' => $user->user_firstname,
                'lastname' => $user->user_lastname,
                'email' => $user->email,
                'education_level' => $request->education_level ?? '',
            ];
            
            // Add dynamic fields to student record if they have corresponding columns
            foreach ($formRequirements as $field) {
                $fieldName = $field->field_name;
                
                // Skip sections and batch_id
                if ($field->field_type === 'section' || $fieldName === 'batch_id') {
                    continue;
                }
                
                // Check if the column exists in the students table and if we have data for it
                try {
                    if (FormRequirement::columnExists($fieldName, 'students') && $request->has($fieldName)) {
                        if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                            // Handle file uploads - store same file path as registration
                            $studentData[$fieldName] = $registration->{$fieldName} ?? null;
                        } elseif ($field->field_type === 'module_selection' && $request->has($fieldName)) {
                            // Handle module selection
                            $selectedModules = $request->input($fieldName, []);
                            if (is_array($selectedModules)) {
                                $studentData[$fieldName] = json_encode($selectedModules);
                            } else {
                                $studentData[$fieldName] = $selectedModules;
                            }
                        } elseif ($field->field_type === 'checkbox') {
                            // Handle checkbox fields
                            $studentData[$fieldName] = $request->has($fieldName) ? 1 : 0;
                        } else {
                            // Handle regular fields
                            $fieldValue = $request->input($fieldName);
                            if (!empty($fieldValue) || $fieldValue === '0') {
                                $studentData[$fieldName] = $fieldValue;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error processing student field {$fieldName}: " . $e->getMessage());
                }
            }
            
            // Create or update student record
            $existingStudent = Student::where('user_id', $user->user_id)->first();
            
            if ($existingStudent) {
                // Update existing student record
                $existingStudent->update($studentData);
                $student = $existingStudent;
                Log::info('Updated existing student record', ['student_id' => $student->student_id]);
            } else {
                // Create new student record
                $student = Student::create($studentData);
                Log::info('Created new student record', ['student_id' => $student->student_id]);
            }
            
            // Also create an immediate enrollment record with the batch_id
            // This ensures batch_id is preserved even if the session is cleared
            $enrollmentData = [
                'registration_id' => $registration->registration_id,
                'user_id' => $user?->user_id, // Add user_id
                'program_id' => $request->program_id,
                'package_id' => $request->package_id,
                'enrollment_type' => $request->enrollment_type,
                'learning_mode' => strtolower($request->learning_mode),
                'enrollment_status' => 'pending', // Will be updated to 'approved' when admin approves
                'payment_status' => 'pending',
                'batch_access_granted' => false, // Default to false, admin will grant access
            ];
            
            // Include batch_id if it was selected during registration
            if ($request->batch_id) {
                $enrollmentData['batch_id'] = $request->batch_id;
                Log::info('Creating enrollment with batch_id during registration', [
                    'batch_id' => $request->batch_id,
                    'registration_id' => $registration->registration_id
                ]);
            }
            
            Enrollment::create($enrollmentData);
            
            Log::info('Initial enrollment created during registration', [
                'registration_id' => $registration->registration_id,
                'batch_id' => $request->batch_id ?? 'none'
            ]);

            DB::commit();

            Log::info('Registration completed successfully', [
                'registration_id' => $registration->registration_id,
                'user_id' => $user->user_id,
                'batch_id' => $request->batch_id ?? 'none'
            ]);

            // Debug AJAX detection
            Log::info('AJAX Detection Debug:', [
                'wantsJson' => $request->wantsJson(),
                'ajax' => $request->ajax(),
                'X-Requested-With' => $request->header('X-Requested-With'),
                'Accept' => $request->header('Accept'),
                'Content-Type' => $request->header('Content-Type')
            ]);

            // Check if this is an AJAX request (your form is sending via AJAX)
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                Log::info('Returning JSON response for AJAX request');
                return response()->json([
                    'success' => true,
                    'message' => 'Registration completed successfully!',
                    'redirect' => '/registration/success', // Redirect to registration success page
                    'data' => [
                        'registration_id' => $registration->registration_id,
                        'user_id' => $user->user_id,
                        'batch_id' => $request->batch_id ?? null
                    ]
                ]);
            }

            // Redirect to success page for regular form submissions
            Log::info('Returning redirect response for regular form submission');
            return redirect('/registration/success')->with('success', 'Registration submitted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if this is an AJAX request
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage(),
                    'errors' => ['general' => [$e->getMessage()]]
                ], 400);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred during registration: ' . $e->getMessage());
        }
    }

    public function showRegistrationForm(Request $request)
    {
        $enrollmentType = 'full'; // Set to full since this is the full enrollment route
        $packages = Package::all();
        
        // Get requirements for "full" program
        $formRequirements = FormRequirement::active()
            ->forProgram('full')
            ->ordered()
            ->get();

        // Get plan data with learning mode settings
        $fullPlan = \App\Models\Plan::where('plan_id', 1)->first(); // Full Plan
        $modularPlan = \App\Models\Plan::where('plan_id', 2)->first(); // Modular Plan
        
        // Get existing student data if user is logged in
        $student = null;
        $enrolledProgramIds = [];
        
        if (session('user_id')) {
            $student = Student::where('user_id', session('user_id'))->first();
            
            // Get all program IDs that the student is already enrolled in
            // Check enrollments directly by user_id (don't require student record)
            $enrolledProgramIds = \App\Models\Enrollment::where('user_id', session('user_id'))
                ->where(function($query) {
                    $query->whereIn('enrollment_status', ['pending', 'approved', 'completed'])
                          ->orWhere(function($subQuery) {
                              // Also include if payment is completed regardless of enrollment status
                              $subQuery->where('payment_status', 'paid');
                          });
                })
                ->pluck('program_id')
                ->unique() // Remove duplicates
                ->toArray();
                
            Log::info('User enrollment check', [
                'user_id' => session('user_id'),
                'enrolled_program_ids' => $enrolledProgramIds,
                'has_student_record' => !!$student
            ]);
        }
        
        // Filter out programs that the student is already enrolled in
        $programs = Program::where('is_archived', false)
            ->whereNotIn('program_id', $enrolledProgramIds)
            ->get();

        // Get education levels for the current plan type
        // Map enrollment types to education level plan types
        $planType = $enrollmentType === 'modular' ? 'general' : 'professional'; // Full enrollment -> professional, Modular -> general
        $educationLevels = \App\Models\EducationLevel::forPlan($planType)->get();

        return view('registration.Full_enrollment', compact('enrollmentType', 'programs', 'packages', 'student', 'formRequirements', 'fullPlan', 'modularPlan', 'educationLevels'));
    }


    public function showEnrollmentSelection()
    {
        return view('enrollment');
    }

    /**
     * Map dynamic field to existing registration column if it exists
     */
    private function mapDynamicFieldToRegistrationColumn($registration, $fieldName, $value)
    {
        // This method can be used for future dynamic field mapping if needed
        // For now, we handle dynamic fields directly in the store method
        return null;
    }

    /**
     * Get batches by program for public access (registration forms)
     */
    public function getBatchesByProgram(Request $request)
    {
        $programId = $request->get('program_id');
        
        if (!$programId) {
            return response()->json([]);
        }

        try {
            Log::info('Fetching batches for program: ' . $programId);

            // Update batch statuses first
            $this->updateBatchStatuses($programId);

            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->whereIn('batch_status', ['available', 'ongoing']) // Include ongoing batches
                ->where(function($query) {
                    // Allow registration if:
                    // 1. Registration deadline hasn't passed (for available batches) OR deadline is null
                    // 2. OR batch is ongoing (regardless of deadline, as people can still join)
                    $query->where(function($subQuery) {
                        $subQuery->where('batch_status', 'available')
                                 ->where(function($deadlineQuery) {
                                     $deadlineQuery->where('registration_deadline', '>=', now())
                                                   ->orWhereNull('registration_deadline');
                                 });
                    })->orWhere('batch_status', 'ongoing');
                })
                ->with('program')
                ->orderBy('start_date', 'asc')
                ->get()
                ->filter(function($batch) {
                    // Only show batches that have available slots
                    return $batch->current_capacity < $batch->max_capacity;
                })
                ->map(function ($batch) {
                    $isOngoing = $batch->batch_status === 'ongoing';
                    $daysStarted = $isOngoing ? now()->diffInDays($batch->start_date) : 0;
                    $availableSlots = $batch->max_capacity - $batch->current_capacity;
                    
                    return [
                        'batch_id' => $batch->batch_id,
                        'batch_name' => $batch->batch_name,
                        'program_name' => $batch->program->program_name ?? 'N/A',
                        'max_capacity' => $batch->max_capacity,
                        'current_capacity' => $batch->current_capacity,
                        'batch_status' => $batch->batch_status,
                        'registration_deadline' => $batch->registration_deadline ? $batch->registration_deadline->format('M d, Y') : 'Open',
                        'start_date' => $batch->start_date->format('M d, Y'),
                        'end_date' => $batch->end_date ? $batch->end_date->format('M d, Y') : null,
                        'description' => $batch->description,
                        'status' => $isOngoing ? 'ongoing' : 'active',
                        'schedule' => $isOngoing ? 'Started ' . $batch->start_date->format('M d, Y') : 'Starts ' . $batch->start_date->format('M d, Y'),
                        'is_ongoing' => $isOngoing,
                        'days_started' => $daysStarted,
                        'available_slots' => $availableSlots,
                        'duration' => 'TBD' // Add default duration
                    ];
                })
                ->values();

            Log::info('Found batches: ' . $batches->count());

            // Check if auto-create is enabled for this program
            $program = \App\Models\Program::find($programId);
            $autoCreate = $program ? $program->auto_create_batch : false;

            return response()->json([
                'success' => true,
                'batches' => $batches,
                'auto_create' => $autoCreate,
                'message' => $batches->count() > 0 ? 'Batches found' : 'No batches available'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching batches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'batches' => [],
                'auto_create' => false,
                'message' => 'Error loading batches'
            ]);
        }
    }

    /**
     * Update batch statuses based on current date
     */
    private function updateBatchStatuses($programId = null)
    {
        $query = \App\Models\StudentBatch::whereIn('batch_status', ['pending', 'available', 'ongoing']);
        
        if ($programId) {
            $query->where('program_id', $programId);
        }
        
        $batches = $query->get();
        
        foreach ($batches as $batch) {
            $batch->updateStatusBasedOnDates();
        }
    }

    /**
     * Check if email exists in users table
     */
    public function checkEmailExists(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json([
                    'error' => true,
                    'message' => 'Email is required'
                ], 400);
            }

            $exists = User::where('email', $email)->exists();
            
            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Email already exists' : 'Email is available'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking email: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error checking email availability'
            ], 500);
        }
    }

    /**
     * Generate a unique student ID in format YYYY-MM-NNNNN
     */
    private function generateStudentId()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $prefix = $currentYear . '-' . $currentMonth . '-';
        
        // Find the highest existing student ID for current year-month
        $lastStudent = Student::where('student_id', 'LIKE', $prefix . '%')
            ->orderBy('student_id', 'desc')
            ->first();
        
        if ($lastStudent) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastStudent->student_id, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no students exist for this month
            $nextNumber = 1;
        }
        
        // Format as 5-digit number with leading zeros
        $formattedNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        $studentId = $prefix . $formattedNumber;
        
        // Double-check uniqueness (in case of race condition)
        while (Student::where('student_id', $studentId)->exists()) {
            $nextNumber++;
            $formattedNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $studentId = $prefix . $formattedNumber;
        }
        
        Log::info('Generated student ID: ' . $studentId);
        return $studentId;
    }

}
