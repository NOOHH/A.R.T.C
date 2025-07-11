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
        Log::info('Registration attempt started', $request->all());
        
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
                // batch_id is optional and will be stored in session, not in registrations table
                'batch_id' => 'nullable|integer'
            ];

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
            
            // Handle start date based on learning mode
            if ($learningMode === 'synchronous') {
                // For synchronous mode, set start date to 2 weeks from registration
                $registration->start_date = now()->addDays(14)->format('Y-m-d');
            } else {
                // For asynchronous mode, use the user-provided start date
                $registration->start_date = $request->Start_Date;
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
                            Log::warning("Field {$fieldName} skipped - column doesn't exist in registrations table");
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

            // Redirect to success page 
            return redirect()->route('registration.success')->with('success', 'Registration submitted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
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

        return view('registration.Full_enrollment', compact('enrollmentType', 'programs', 'packages', 'student', 'formRequirements', 'fullPlan', 'modularPlan'));
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

            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->where('batch_status', '=', 'available')
                // Temporarily remove deadline filter for testing
                // ->where('registration_deadline', '>=', now())
                ->with('program')
                ->orderBy('start_date', 'asc')
                ->get()
                ->map(function ($batch) {
                    $deadlinePassed = $batch->registration_deadline < now();
                    return [
                        'batch_id' => $batch->batch_id,
                        'batch_name' => $batch->batch_name,
                        'program_name' => $batch->program->program_name ?? 'N/A',
                        'max_capacity' => $batch->max_capacity,
                        'current_capacity' => $batch->current_capacity,
                        'batch_status' => $batch->batch_status,
                        'registration_deadline' => $batch->registration_deadline->format('M d, Y'),
                        'start_date' => $batch->start_date->format('M d, Y'),
                        'description' => $batch->description,
                        'status' => $deadlinePassed ? 'deadline_passed' : 'active',
                        'schedule' => 'Live Classes - ' . $batch->start_date->format('M d, Y'),
                        'duration' => 'TBD' // Add default duration
                    ];
                });

            Log::info('Found batches: ' . $batches->count());

            return response()->json($batches);
        } catch (\Exception $e) {
            Log::error('Error fetching batches: ' . $e->getMessage());
            return response()->json([]);
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

}
