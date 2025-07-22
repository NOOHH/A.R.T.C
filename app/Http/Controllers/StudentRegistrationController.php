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
use App\Models\EnrollmentCourse;
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
                'Start_Date' => 'required|date',
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
                            // Enhanced file upload handling with validation
                            $uploadedFile = $request->file($fieldName);
                            
                            // Validate file type (only allow pdf, png, jpeg, jpg, images)
                            $allowedMimes = ['pdf', 'png', 'jpeg', 'jpg'];
                            $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
                            
                            if (!in_array($fileExtension, $allowedMimes)) {
                                throw new \Exception("Invalid file type for {$fieldName}. Only PDF, PNG, JPEG files are allowed.");
                            }
                            
                            // Validate file size (max 10MB)
                            if ($uploadedFile->getSize() > 10485760) { // 10MB in bytes
                                throw new \Exception("File size for {$fieldName} exceeds 10MB limit.");
                            }
                            
                            // Store the file with a unique name to prevent conflicts
                            $fileName = time() . '_' . uniqid() . '_' . $uploadedFile->getClientOriginalName();
                            $path = $uploadedFile->storeAs('uploads/registrations', $fileName, 'public');
                            
                            // Save the file path to the database
                            $registration->{$fieldName} = $path;
                            
                            Log::info("File uploaded successfully for field {$fieldName}", [
                                'field_name' => $fieldName,
                                'file_path' => $path,
                                'file_size' => $uploadedFile->getSize(),
                                'file_type' => $fileExtension
                            ]);
                            
                        } elseif ($field->field_type === 'file' && !$request->hasFile($fieldName)) {
                            // Check if we have a validated file path from OCR validation
                            $filePathField = $fieldName . '_path';
                            if ($request->has($filePathField)) {
                                $validatedFilePath = $request->input($filePathField);
                                if ($validatedFilePath) {
                                    $registration->{$fieldName} = $validatedFilePath;
                                    Log::info("Using validated file path for field {$fieldName}", [
                                        'field_name' => $fieldName,
                                        'file_path' => $validatedFilePath
                                    ]);
                                }
                            }
                            
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

    /**
     * Submit modular enrollment
     */
    public function submitModularEnrollment(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info('=== MODULAR ENROLLMENT DETAILED DEBUG ===', [
                'request_method' => $request->method(),
                'all_input_data' => $request->except(['password', 'password_confirmation']),
                'selected_modules_value' => $request->input('selected_modules'),
                'form_files' => array_keys($request->allFiles()),
                'has_files' => !empty($request->allFiles()),
                'file_count' => count($request->allFiles()),
                'form_data_count' => count($request->except(['password', 'password_confirmation'])),
                'program_id' => $request->input('program_id'),
                'package_id' => $request->input('package_id'), 
                'education_level' => $request->input('education_level'),
                'start_date' => $request->input('Start_Date'),
                'learning_mode' => $request->input('learning_mode'),
                'enrollment_type' => $request->input('enrollment_type'),
                'user_logged_in' => (bool) (session('user_id') || auth()->check()),
                'session_user_id' => session('user_id'),
                'auth_check' => auth()->check()
            ]);

            // Base validation rules
            $rules = [
                'program_id' => 'required|exists:programs,program_id',
                'package_id' => 'required|exists:packages,package_id',
                'learning_mode' => 'required|in:synchronous,asynchronous',
                'batch_id' => 'nullable|exists:student_batches,batch_id',
                'selected_modules' => 'required|string', // Can be JSON string
                'education_level' => 'required|string|in:Undergraduate,Graduate',
                'Start_Date' => 'required|date',
                'enrollment_type' => 'required|in:Modular',
                'plan_id' => 'nullable|integer',
                'referral_code' => 'nullable|string'
            ];

            // Add account validation rules only for new users (not logged in)
            if (!session('user_id') && !auth()->check()) {
                $rules['user_firstname'] = 'required|string|max:255';
                $rules['user_lastname'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|min:8|confirmed';
            }

            // Add file validation rules for education level requirements - ONLY for selected education level
            $selectedEducationLevel = $request->input('education_level');
            Log::info('Selected education level for file validation', ['education_level' => $selectedEducationLevel]);
            
            if ($selectedEducationLevel) {
                // Find the education level in database
                $educationLevel = \App\Models\EducationLevel::where('level_name', $selectedEducationLevel)->first();
                
                if (!$educationLevel) {
                    Log::warning('Education level not found', ['education_level' => $selectedEducationLevel]);
                } else {
                    Log::info('Education level found', [
                        'id' => $educationLevel->id,
                        'name' => $educationLevel->level_name,
                        'has_file_requirements' => !empty($educationLevel->file_requirements)
                    ]);
                    
                    if ($educationLevel->file_requirements) {
                        $fileRequirements = is_string($educationLevel->file_requirements) 
                            ? json_decode($educationLevel->file_requirements, true) 
                            : $educationLevel->file_requirements;
                        
                        if (is_array($fileRequirements)) {
                            Log::info('Processing file requirements', ['count' => count($fileRequirements)]);
                            
                            foreach ($fileRequirements as $requirement) {
                                if (isset($requirement['available_modular_plan']) && $requirement['available_modular_plan']) {
                                    $fieldName = $requirement['field_name'] ?? $requirement['document_type'];
                                    
                                    if ($fieldName) {
                                        // Normalize field name to match form field names
                                        $normalizedFieldName = strtolower($fieldName);
                                        
                                        // Check if the file is uploaded
                                        $hasFile = $request->hasFile($normalizedFieldName);
                                        $isRequired = isset($requirement['is_required']) && $requirement['is_required'];
                                        
                                        if ($isRequired) {
                                            // If file is required, always add validation rule
                                            $rules[$normalizedFieldName] = 'required|file|max:10240'; // 10MB max
                                            Log::info('Added required file rule', [
                                                'field' => $normalizedFieldName,
                                                'original_field' => $fieldName,
                                                'has_file' => $hasFile,
                                                'education_level' => $selectedEducationLevel
                                            ]);
                                        } elseif ($hasFile) {
                                            // If file is optional but uploaded, validate format
                                            $rules[$normalizedFieldName] = 'file|max:10240'; // 10MB max
                                            Log::info('Added optional file rule', [
                                                'field' => $normalizedFieldName,
                                                'original_field' => $fieldName,
                                                'education_level' => $selectedEducationLevel
                                            ]);
                                        }
                                    }
                                }
                            }
                        } else {
                            Log::warning('File requirements is not valid array', ['raw_data' => $educationLevel->file_requirements]);
                        }
                    } else {
                        Log::info('No file requirements for this education level', ['education_level' => $selectedEducationLevel]);
                    }
                }
            }

            // Validate the request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                DB::rollBack();
                
                // Enhanced error logging with better file error messages
                $errors = $validator->errors()->toArray();
                $enhancedErrors = [];
                
                foreach ($errors as $field => $messages) {
                    // Check if this is a file field error
                    if (in_array($field, array_keys($rules)) && strpos($rules[$field], 'file') !== false) {
                        $enhancedErrors[$field] = [
                            "The {$field} file is required for your selected education level. Please upload the required document."
                        ];
                    } else {
                        $enhancedErrors[$field] = $messages;
                    }
                }
                
                Log::error('Modular enrollment validation failed', [
                    'errors' => $errors,
                    'enhanced_errors' => $enhancedErrors,
                    'input_data' => $request->except(['password', 'password_confirmation', '_token']),
                    'has_selected_modules' => $request->has('selected_modules'),
                    'selected_modules_value' => $request->input('selected_modules'),
                    'form_files' => array_keys($request->allFiles()),
                    'education_level' => $selectedEducationLevel,
                    'validation_rules' => array_keys($rules)
                ]);
                
                return response()->json([
                    'success' => false,
                    'errors' => $enhancedErrors
                ], 422);
            }

            $validated = $validator->validated();

            // Process uploaded files
            $uploadedFiles = [];
            $allFiles = $request->allFiles();
            if (!empty($allFiles)) {
                foreach ($allFiles as $fieldName => $file) {
                    try {
                        if ($file && $file->isValid()) {
                            $fileName = time() . '_' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('uploads/education_requirements', $fileName, 'public');
                            $uploadedFiles[$fieldName] = $filePath;
                            Log::info('File uploaded successfully', [
                                'field' => $fieldName,
                                'original_name' => $file->getClientOriginalName(),
                                'stored_path' => $filePath
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('File upload failed', [
                            'field' => $fieldName,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Process dynamic form fields
            $dynamicFields = [];
            $formRequirements = \App\Models\FormRequirement::active()
                ->forProgram('modular')
                ->get();
                
            foreach ($formRequirements as $requirement) {
                $fieldName = $requirement->field_name;
                if ($request->has($fieldName)) {
                    $dynamicFields[$fieldName] = $request->input($fieldName);
                }
            }

            // Handle referral code to set directors_id
            $directorsId = null;
            if (!empty($validated['referral_code'])) {
                // Check if referral code is from a director
                $director = \App\Models\Director::where('referral_code', $validated['referral_code'])
                    ->where('directors_archived', false)
                    ->first();
                
                if ($director) {
                    $directorsId = $director->directors_id;
                    Log::info('Referral from director', ['director_id' => $directorsId, 'referral_code' => $validated['referral_code']]);
                } else {
                    // Check if referral code is from a professor
                    $professor = \App\Models\Professor::where('referral_code', $validated['referral_code'])
                        ->where('is_archived', false)
                        ->first();
                    
                    if ($professor) {
                        // If referral is from professor, you might want to handle this differently
                        // For now, we'll set directors_id to null since it's from a professor
                        Log::info('Referral from professor', ['professor_id' => $professor->professor_id, 'referral_code' => $validated['referral_code']]);
                    }
                }
            }

            // Create or get user
            $user = null;
            
            if (auth()->check()) {
                $user = auth()->user();
                Log::info('Using authenticated user for modular enrollment', ['user_id' => $user->user_id]);
            } elseif (session('user_id')) {
                $user = User::find(session('user_id'));
                if ($user) {
                    Log::info('Using session user for modular enrollment', ['user_id' => $user->user_id]);
                }
            }
            
            // Create new user if no existing user found
            if (!$user) {
                // Validate that we have user creation data
                if (!isset($validated['user_firstname']) || !isset($validated['user_lastname']) || 
                    !isset($validated['email']) || !isset($validated['password'])) {
                    throw new \Exception('User creation data missing for modular enrollment.');
                }
                
                $user = User::create([
                    'user_firstname' => $validated['user_firstname'],
                    'user_lastname' => $validated['user_lastname'],
                    'email' => $validated['email'],
                    'password' => bcrypt($validated['password']),
                    'role' => 'student',
                    'admin_id' => 1, // Default admin ID for student registrations
                    'directors_id' => $directorsId, // Set based on referral code or null
                    'enrollment_id' => 0 // Will be updated after enrollment creation
                ]);
                
                Log::info('User created successfully for modular enrollment', ['user_id' => $user->user_id]);
            }

            if (!$user || !$user->user_id) {
                throw new \Exception('Unable to create or find valid user for modular enrollment');
            }

            // NOTE: Student record will be created later by admin after approval
            // This is different from full enrollment which creates student immediately
            
            // Get package and program details
            $package = \App\Models\Package::find($validated['package_id']);
            $program = \App\Models\Program::find($validated['program_id']);

            // Parse selected modules (could be JSON string or array)
            $selectedModulesData = $validated['selected_modules'];
            if (is_string($selectedModulesData)) {
                $selectedModules = json_decode($selectedModulesData, true) ?? [];
            } else {
                $selectedModules = $selectedModulesData;
            }

            Log::info('Parsed selected modules', ['modules' => $selectedModules]);

            // Extract course selections from modules data
            $selectedCourses = [];
            if (is_array($selectedModules)) {
                foreach ($selectedModules as $moduleData) {
                    if (is_array($moduleData) && isset($moduleData['selected_courses'])) {
                        if (is_array($moduleData['selected_courses'])) {
                            $selectedCourses = array_merge($selectedCourses, $moduleData['selected_courses']);
                        }
                    }
                }
            }
            
            Log::info('Extracted course selections', ['courses' => $selectedCourses]);

            // Prepare registration data
            $registrationData = [
                'user_id' => $user->user_id,
                'firstname' => $validated['user_firstname'] ?? ($user->user_firstname ?? ''),
                'lastname' => $validated['user_lastname'] ?? ($user->user_lastname ?? ''),
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id'],
                'program_name' => $program->program_name ?? '',
                'package_name' => $package->package_name ?? '',
                'learning_mode' => $validated['learning_mode'],
                'enrollment_type' => $validated['enrollment_type'],
                'education_level' => $validated['education_level'],
                'selected_modules' => $validated['selected_modules'], // Store full module data
                'selected_courses' => json_encode($selectedCourses), // Store extracted course IDs
                'Start_Date' => $validated['Start_Date'], // Match the actual column name in database
                'status' => 'pending',
                'dynamic_fields' => json_encode(array_merge([
                    'referral_code' => $validated['referral_code'] ?? '',
                    'registration_mode' => $validated['learning_mode']
                ], $dynamicFields))
            ];

            // Add uploaded file paths to registration data
            foreach ($uploadedFiles as $fieldName => $filePath) {
                // Map common file field names to registration columns
                $columnName = $this->mapFileFieldToColumn($fieldName);
                if ($columnName) {
                    $registrationData[$columnName] = $filePath;
                }
            }

            // Create registration record (this is the main table for registrations)
            $registration = \App\Models\Registration::create($registrationData);

            Log::info('Registration created successfully', ['registration_id' => $registration->registration_id]);

            // Handle batch assignment
            $batchId = null;
            if ($validated['learning_mode'] === 'synchronous' && isset($validated['batch_id'])) {
                $batch = \App\Models\StudentBatch::find($validated['batch_id']);
                if ($batch && $batch->current_capacity < $batch->max_capacity) {
                    $batchId = $batch->batch_id;
                    $batch->increment('current_capacity');
                    Log::info('Batch assigned', ['batch_id' => $batchId]);
                }
            }

            // Create enrollment record (without student_id as student hasn't been created yet)
            $enrollment = \App\Models\Enrollment::create([
                'user_id' => $user->user_id,
                'registration_id' => $registration->registration_id,
                'student_id' => null, // Will be set when admin approves and creates student record
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id'],
                'learning_mode' => $validated['learning_mode'],
                'enrollment_type' => $validated['enrollment_type'],
                'batch_id' => $batchId,
                'enrollment_status' => 'pending',
                'payment_status' => 'pending',
                'education_level' => $validated['education_level'],
                'Start_Date' => $validated['Start_Date']
            ]);

            Log::info('Enrollment created successfully', ['enrollment_id' => $enrollment->enrollment_id]);

            // Update user with enrollment_id
            $user->update(['enrollment_id' => $enrollment->enrollment_id]);

            // Create module registrations if registration_modules table exists
            if (is_array($selectedModules) && count($selectedModules) > 0) {
                foreach ($selectedModules as $moduleData) {
                    $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                    
                    if ($moduleId) {
                        try {
                            // Check if RegistrationModule model exists, if not skip this part
                            if (class_exists('\App\Models\RegistrationModule')) {
                                \App\Models\RegistrationModule::create([
                                    'registration_id' => $registration->registration_id,
                                    'module_id' => $moduleId
                                ]);
                            }
                            Log::info('Module registered', ['module_id' => $moduleId, 'registration_id' => $registration->registration_id]);
                            
                            // Handle course-level enrollments if specified
                            if (is_array($moduleData) && isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                                foreach ($moduleData['selected_courses'] as $courseData) {
                                    $courseId = is_array($courseData) ? ($courseData['id'] ?? $courseData['course_id'] ?? null) : $courseData;
                                    
                                    if ($courseId) {
                                        try {
                                            EnrollmentCourse::create([
                                                'enrollment_id' => $enrollment->enrollment_id,
                                                'course_id' => $courseId,
                                                'module_id' => $moduleId,
                                                'enrollment_type' => 'course',
                                                'course_price' => 0, // Price will be calculated based on package
                                                'is_active' => true
                                            ]);
                                            Log::info('Course enrolled', [
                                                'course_id' => $courseId, 
                                                'module_id' => $moduleId,
                                                'enrollment_id' => $enrollment->enrollment_id
                                            ]);
                                        } catch (\Exception $e) {
                                            Log::warning('Failed to create course enrollment', [
                                                'course_id' => $courseId,
                                                'module_id' => $moduleId,
                                                'enrollment_id' => $enrollment->enrollment_id,
                                                'error' => $e->getMessage()
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                // If no specific courses selected, enroll in all courses of the module
                                $module = \App\Models\Module::with('courses')->find($moduleId);
                                if ($module && $module->courses) {
                                    foreach ($module->courses as $course) {
                                        try {
                                            EnrollmentCourse::create([
                                                'enrollment_id' => $enrollment->enrollment_id,
                                                'course_id' => $course->subject_id,
                                                'module_id' => $moduleId,
                                                'enrollment_type' => 'module',
                                                'course_price' => 0,
                                                'is_active' => true
                                            ]);
                                            Log::info('Full module course enrolled', [
                                                'course_id' => $course->subject_id, 
                                                'module_id' => $moduleId,
                                                'enrollment_id' => $enrollment->enrollment_id
                                            ]);
                                        } catch (\Exception $e) {
                                            Log::warning('Failed to create full module course enrollment', [
                                                'course_id' => $course->subject_id,
                                                'module_id' => $moduleId,
                                                'enrollment_id' => $enrollment->enrollment_id,
                                                'error' => $e->getMessage()
                                            ]);
                                        }
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning('Failed to create module registration', [
                                'module_id' => $moduleId,
                                'registration_id' => $registration->registration_id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            Log::info('Modular enrollment completed successfully', [
                'user_id' => $user->user_id,
                'enrollment_id' => $enrollment->enrollment_id,
                'registration_id' => $registration->registration_id,
                'uploaded_files_count' => count($uploadedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration and enrollment successful!',
                'data' => [
                    'user_id' => $user->user_id,
                    'enrollment_id' => $enrollment->enrollment_id,
                    'registration_id' => $registration->registration_id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Modular enrollment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Store modular enrollment from 6-step wizard
     */
    public function storeModular(Request $request)
    {
        Log::info('ModEnroll payload', $request->all());
        
        try {
            DB::beginTransaction();

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'package_id' => 'required|exists:packages,package_id',
                'module_ids' => 'required|array',
                'module_ids.*' => 'exists:modules,modules_id',
                'learning_mode' => 'required|in:Face-to-Face,Online,Hybrid',
                'account_data' => 'required|array',
                'account_data.firstName' => 'required|string|max:255',
                'account_data.lastName' => 'required|string|max:255',
                'account_data.email' => 'required|email|unique:users,email',
                'account_data.password' => 'required|min:8',
                'profile_data' => 'required|array',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                Log::error('Modular enrollment validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $accountData = $validated['account_data'];
            $profileData = $validated['profile_data'];

            // Get package and determine program
            $package = Package::find($validated['package_id']);
            if (!$package) {
                throw new \Exception('Package not found');
            }

            // Create user account
            $user = User::create([
                'user_firstname' => $accountData['firstName'],
                'user_lastname' => $accountData['lastName'],
                'email' => $accountData['email'],
                'password' => Hash::make($accountData['password']),
                'role' => 'student',
                'admin_id' => 1, // Default admin ID for student registrations
                'directors_id' => null, // Default to null since no referral handling in this method
                'enrollment_id' => 0 // Will be updated after enrollment creation
            ]);

            Log::info('User created successfully', ['user_id' => $user->user_id]);

            // NOTE: For modular enrollment, student record will be created by admin after approval
            // This follows the correct flow: users table → registrations table → admin approval → students table

            // Create registration record with dynamic fields from admin settings
            $registrationData = [
                'user_id' => $user->user_id,
                'firstname' => $accountData['firstName'],
                'lastname' => $accountData['lastName'],
                'program_id' => $package->program_id,
                'package_id' => $validated['package_id'],
                'program_name' => $package->program->program_name ?? '',
                'package_name' => $package->package_name ?? '',
                'learning_mode' => $validated['learning_mode'],
                'enrollment_type' => 'Modular',
                'selected_modules' => json_encode($validated['module_ids']),
                'status' => 'pending',
                'dynamic_fields' => $profileData
            ];

            $registration = Registration::create($registrationData);
            
            if (!$registration) {
                Log::error('Failed registration save', ['data' => $registrationData]);
                throw new \Exception('Failed to create registration record');
            }

            Log::info('Registration created successfully', ['registration_id' => $registration->registration_id]);

            // Map learning mode values for enrollment table
            $enrollmentLearningMode = 'Asynchronous'; // Default to Asynchronous for modular
            if ($validated['learning_mode'] === 'Face-to-Face') {
                $enrollmentLearningMode = 'Synchronous';
            }

            // Create enrollment record (without student_id since student record will be created later by admin)
            $enrollment = Enrollment::create([
                'user_id' => $user->user_id,
                'student_id' => null, // Will be set when admin creates student record after approval
                'program_id' => $package->program_id,
                'package_id' => $validated['package_id'],
                'learning_mode' => $enrollmentLearningMode, // Use mapped value
                'enrollment_type' => 'Modular',
                'enrollment_status' => 'pending',
                'payment_status' => 'pending',
                'Modular_enrollment' => json_encode($validated['module_ids'])
            ]);

            Log::info('Enrollment created successfully', ['enrollment_id' => $enrollment->enrollment_id]);

            // Update user with enrollment_id
            $user->enrollment_id = $enrollment->enrollment_id;
            $user->save();

            // Create module registrations
            foreach ($validated['module_ids'] as $moduleId) {
                \App\Models\RegistrationModule::create([
                    'registration_id' => $registration->registration_id,
                    'module_id' => $moduleId
                ]);
                Log::info('Module registered', ['module_id' => $moduleId, 'registration_id' => $registration->registration_id]);
            }

            DB::commit();

            Log::info('Modular enrollment completed successfully', [
                'user_id' => $user->user_id,
                'enrollment_id' => $enrollment->enrollment_id,
                'registration_id' => $registration->registration_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully!',
                'redirect' => '/student/dashboard'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Modular enrollment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['account_data.password'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modules for a program
     */
    public function getProgramModules(Request $request)
    {
        try {
            $programId = $request->get('program_id');
            $includeAll = $request->get('all', false);

            if (!$programId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program ID is required'
                ], 400);
            }

            $query = \App\Models\Module::where('program_id', $programId)
                ->where('is_archived', false)
                ->orderBy('module_order');

            // Get courses count for each module
            $modules = $query->withCount('courses')->get();

            // Add pricing information if available
            $modules = $modules->map(function ($module) {
                return [
                    'id' => $module->modules_id,
                    'name' => $module->module_name,
                    'description' => $module->module_description,
                    'price' => $module->price ?? 0,
                    'duration' => $module->duration ?? 'Flexible',
                    'level' => $module->level ?? 'All Levels',
                    'courses_count' => $module->courses_count,
                    'course_count' => $module->courses_count // Alternative naming
                ];
            });

            return response()->json([
                'success' => true,
                'modules' => $modules
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading program modules', [
                'error' => $e->getMessage(),
                'program_id' => $request->get('program_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load modules'
            ], 500);
        }
    }

    /**
     * Get courses for a module
     */
    public function getModuleCourses(Request $request)
    {
        try {
            $moduleId = $request->get('module_id');

            if (!$moduleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module ID is required'
                ], 400);
            }

            $courses = \App\Models\Course::where('module_id', $moduleId)
                ->where('is_active', true)
                ->orderBy('subject_order')
                ->get();

            // Format courses for response
            $formattedCourses = $courses->map(function ($course) {
                return [
                    'course_id' => $course->subject_id,
                    'subject_id' => $course->subject_id, // Alternative naming
                    'course_name' => $course->subject_name,
                    'subject_name' => $course->subject_name, // Alternative naming
                    'course_description' => $course->subject_description,
                    'subject_description' => $course->subject_description, // Alternative naming
                    'course_price' => $course->subject_price,
                    'subject_price' => $course->subject_price, // Alternative naming
                    'course_order' => $course->subject_order,
                    'subject_order' => $course->subject_order, // Alternative naming
                    'is_required' => $course->is_required,
                    'duration' => $course->duration ?? 'Flexible',
                    'level' => $course->level ?? 'All Levels'
                ];
            });

            return response()->json([
                'success' => true,
                'courses' => $formattedCourses
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading module courses', [
                'error' => $e->getMessage(),
                'module_id' => $request->get('module_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load courses'
            ], 500);
        }
    }

    /**
     * Get batches for a program
     */
    public function getProgramBatches(Request $request)
    {
        try {
            $programId = $request->get('program_id');

            if (!$programId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program ID is required'
                ], 400);
            }

            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->where('is_active', true)
                ->where('start_date', '>', now())
                ->orderBy('start_date')
                ->get();

            return response()->json([
                'success' => true,
                'batches' => $batches
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading program batches', [
                'error' => $e->getMessage(),
                'program_id' => $request->get('program_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load batches'
            ], 500);
        }
    }

    /**
     * Check email availability for registration
     */
    public function checkEmailAvailability(Request $request)
    {
        try {
            $email = $request->input('email');

            if (!$email) {
                return response()->json([
                    'available' => false,
                    'message' => 'Email is required'
                ], 400);
            }

            // Check if email exists in users table
            $userExists = User::where('email', $email)->exists();

            return response()->json([
                'available' => !$userExists,
                'message' => $userExists ? 'Email is already registered' : 'Email is available'
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking email availability', [
                'error' => $e->getMessage(),
                'email' => $request->input('email')
            ]);

            return response()->json([
                'available' => false,
                'message' => 'Failed to check email availability'
            ], 500);
        }
    }

    /**
     * Send OTP for enrollment
     */
    public function sendEnrollmentOTP(Request $request)
    {
        try {
            $email = $request->input('email');

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is required'
                ], 400);
            }

            // Generate 6-digit OTP
            $otpCode = sprintf('%06d', mt_rand(0, 999999));

            // Store OTP in session (you might want to use cache or database for production)
            session(['enrollment_otp_' . $email => [
                'code' => $otpCode,
                'expires_at' => now()->addMinutes(10),
                'email' => $email
            ]]);

            // Send OTP via email (implement your email sending logic here)
            // For now, just log it for testing
            Log::info('OTP sent for enrollment', [
                'email' => $email,
                'otp' => $otpCode,
                'expires_at' => now()->addMinutes(10)
            ]);

            // In a real application, send email here
            // Mail::to($email)->send(new EnrollmentOTPMail($otpCode));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'debug_otp' => $otpCode // Remove this in production
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending enrollment OTP', [
                'error' => $e->getMessage(),
                'email' => $request->input('email')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP'
            ], 500);
        }
    }

    /**
     * Verify OTP for enrollment
     */
    public function verifyEnrollmentOTP(Request $request)
    {
        try {
            $email = $request->input('email');
            $otpCode = $request->input('otp_code');

            if (!$email || !$otpCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email and OTP code are required'
                ], 400);
            }

            // Get OTP from session
            $sessionKey = 'enrollment_otp_' . $email;
            $otpData = session($sessionKey);

            if (!$otpData) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP not found or expired'
                ], 400);
            }

            // Check if OTP has expired
            if (now()->gt($otpData['expires_at'])) {
                session()->forget($sessionKey);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired'
                ], 400);
            }

            // Verify OTP code
            if ($otpData['code'] !== $otpCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code'
                ], 400);
            }

            // OTP verified successfully
            session()->forget($sessionKey);
            session(['enrollment_email_verified_' . $email => true]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying enrollment OTP', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
                'otp_code' => $request->input('otp_code')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP'
            ], 500);
        }
    }

    /**
     * Validate referral code for enrollment
     */
    public function validateEnrollmentReferral(Request $request)
    {
        try {
            $referralCode = $request->input('referral_code');

            if (!$referralCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referral code is required'
                ], 400);
            }

            // Check if referral code exists in professors table
            $professor = \App\Models\Professor::where('referral_code', $referralCode)
                ->where('is_active', true)
                ->first();

            if ($professor) {
                return response()->json([
                    'success' => true,
                    'message' => 'Valid referral code',
                    'referrer_name' => $professor->professor_firstname . ' ' . $professor->professor_lastname,
                    'referrer_type' => 'professor'
                ]);
            }

            // Check if referral code exists in directors table
            $director = \App\Models\Director::where('referral_code', $referralCode)
                ->where('is_active', true)
                ->first();

            if ($director) {
                return response()->json([
                    'success' => true,
                    'message' => 'Valid referral code',
                    'referrer_name' => $director->director_firstname . ' ' . $director->director_lastname,
                    'referrer_type' => 'director'
                ]);
            }

            // No valid referral code found
            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error validating enrollment referral', [
                'error' => $e->getMessage(),
                'referral_code' => $request->input('referral_code')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate referral code'
            ], 500);
        }
    }

    /**
     * Process OCR document for Tesseract text extraction
     */
    public function processOcrDocument(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileType = strtolower($file->getClientOriginalExtension());

            // Store the file temporarily
            $tempPath = $file->storeAs('temp/ocr', uniqid() . '.' . $fileType, 'public');
            $fullPath = storage_path('app/public/' . $tempPath);

            // Process with OCR
            $ocrService = new \App\Services\OcrService();
            $extractedText = $ocrService->extractText($fullPath, $fileType);
            
            // Get program suggestions based on extracted text
            $suggestions = $ocrService->suggestPrograms($extractedText);

            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            Log::info('OCR processing completed', [
                'file_name' => $originalName,
                'file_type' => $fileType,
                'text_length' => strlen($extractedText),
                'suggestions_count' => count($suggestions)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document processed successfully',
                'data' => [
                    'extracted_text' => $extractedText,
                    'program_suggestions' => $suggestions,
                    'file_name' => $originalName
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OCR processing error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get package details for course selection
     */
    /**
     * Map file upload field names to registration table columns
     */
    private function mapFileFieldToColumn($fieldName)
    {
        $mapping = [
            'good_moral' => 'good_moral',
            'psa' => 'PSA',
            'PSA' => 'PSA',
            'birth_certificate' => 'PSA', // Birth certificate often maps to PSA
            'course_cert' => 'Course_Cert',
            'course_certificate' => 'Course_Cert',
            'tor' => 'TOR',
            'transcript_of_records' => 'TOR',
            'cert_of_grad' => 'Cert_of_Grad',
            'certificate_of_graduation' => 'Cert_of_Grad',
            'diploma' => 'Cert_of_Grad',
            'diploma_certificate' => 'diploma_certificate',
            'valid_id' => 'valid_id',
            'school_id' => 'valid_id',
            'photo_2x2' => 'photo_2x2',
            'passport_photo' => 'passport_photo',
            'medical_certificate' => 'medical_certificate',
            'ama_namin' => 'ama_namin', // Custom field from education levels
        ];

        $normalizedFieldName = strtolower($fieldName);
        
        // Check exact matches first
        if (isset($mapping[$normalizedFieldName])) {
            return $mapping[$normalizedFieldName];
        }

        // Check for partial matches
        foreach ($mapping as $pattern => $column) {
            if (strpos($normalizedFieldName, $pattern) !== false) {
                return $column;
            }
        }

        // If no mapping found, use the field name as column name (if it exists in the table)
        $registrationColumns = [
            'good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 
            'valid_id', 'birth_certificate', 'diploma_certificate', 
            'medical_certificate', 'passport_photo', 'photo_2x2', 'ama_namin'
        ];

        if (in_array($normalizedFieldName, $registrationColumns)) {
            return $normalizedFieldName;
        }

        // Default: try to store as a custom field name in lowercase
        return $normalizedFieldName;
    }

    public function getPackageDetails(Request $request)
    {
        try {
            $packageId = $request->get('package_id');

            if (!$packageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Package ID is required'
                ], 400);
            }

            $package = \App\Models\Package::find($packageId);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Package not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'package' => [
                    'package_id' => $package->package_id,
                    'package_name' => $package->package_name,
                    'allowed_modules' => $package->allowed_modules ?? 2,
                    'extra_module_price' => $package->extra_module_price ?? 0,
                    'amount' => $package->amount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading package details', [
                'error' => $e->getMessage(),
                'package_id' => $request->get('package_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load package details'
            ], 500);
        }
    }

}
