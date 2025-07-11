<?php

namespace App\Http\Controllers;

use App\Helpers\SessionManager;
use App\Models\User;
use App\Models\Student;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    protected $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function showForm(Request $request)
    {
        $data = $request->only(['course', 'package_id', 'enrollment_type']);
        
        // Add user data if logged in
        if (SessionManager::isLoggedIn()) {
            $userId = SessionManager::get('user_id');
            $user = User::with('student')->find($userId);
            if ($user) {
                $data['user'] = $user;
                $data['student'] = $user->student;
            }
        }
        
        return view('registration.form', $data);
    }

    public function loadUserData(Request $request)
    {
        if (!SessionManager::isLoggedIn()) {
            return response()->json(['error' => 'Not logged in'], 401);
        }

        $userId = SessionManager::get('user_id');
        $user = User::with('student')->find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'student' => $user->student
        ]);
    }

    public function validateDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'documentType' => 'required|string|in:course_cert,tor,cert_grad'
        ]);

        $file = $request->file('document');
        $path = $file->store('temp_documents');
        
        // Extract text from document
        $extractedText = $this->ocrService->extractText(storage_path('app/' . $path));
        
        // Validate name
        $nameValid = $this->ocrService->validateName(
            $request->firstName,
            $request->lastName,
            $extractedText
        );

        if (!$nameValid) {
            return response()->json([
                'error' => 'Name validation failed',
                'message' => 'The uploaded document does not contain your full name. Please check and upload a document with your complete name.'
            ], 400);
        }

        // Get program suggestions
        $suggestions = $this->ocrService->suggestPrograms($extractedText);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function getBatchesForProgram($programId)
    {
        try {
            $batches = \App\Models\StudentBatch::where('program_id', $programId)
                ->where(function($query) {
                    $query->where('batch_status', 'available')
                          ->orWhere(function($q) {
                              $q->where('batch_status', 'ongoing')
                                ->whereRaw('current_capacity < max_capacity');
                          });
                })
                ->where('registration_deadline', '>=', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();

            // If no batches available, return empty array with message
            if ($batches->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No active batches available. A new batch will be created for you.',
                    'batches' => [],
                    'auto_create' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'batches' => $batches,
                'auto_create' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching batches', [
                'program_id' => $programId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading batches: ' . $e->getMessage(),
                'batches' => [],
                'auto_create' => true
            ], 500);
        }
    }

    public function saveBatchEnrollment(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'batch_id' => 'required|exists:student_batches,batch_id',
        ]);

        $batch = \App\Models\StudentBatch::find($request->batch_id);

        if (!$batch) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        // Check if batch is available and has capacity
        if ($batch->batch_status === 'closed' || $batch->current_capacity >= $batch->max_capacity) {
            return response()->json(['error' => 'Batch is not available for enrollment'], 400);
        }

        // Increment student count
        $batch->increment('current_capacity');

        return response()->json(['success' => true]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'course' => 'required|string',
            'package_id' => 'required|exists:packages,package_id',
            'enrollment_type' => 'required|in:Modular,Full',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'documents.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        // Save user (account creation)
        $user = new User();
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->user_firstname = $validated['firstname'];
        $user->user_lastname = $validated['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($validated['enrollment_type'] === 'Modular') {
            $enrollment->Modular_enrollment = $validated['course'];
            $enrollment->Full_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Full_Program = $validated['course'];
        }
        $enrollment->package_id = $validated['package_id'];
        $enrollment->save();

        // Optionally link user and enrollment (if your schema supports it)
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Redirect to a success page or dashboard
        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }

    public function showAccountForm(Request $request)
    {
        // Store enrollment selection in session
        session([
            'enrollment.course' => $request->course,
            'enrollment.package_id' => $request->package_id,
            'enrollment.enrollment_type' => $request->enrollment_type,
        ]);
        return view('registration.account');
    }

    public function showDetailsForm(Request $request)
    {
        // Save account info in session
        session([
            'enrollment.firstname' => $request->firstname,
            'enrollment.lastname' => $request->lastname,
            'enrollment.email' => $request->email,
            'enrollment.password' => bcrypt($request->password),
        ]);
        return view('registration.details');
    }

    public function submit(Request $request)
    {
        // Save all registration info in session
        session(['enrollment' => array_merge((array)session('enrollment', []), $request->all())]);
        $data = session('enrollment');

        // Save user
        $user = new \App\Models\User();
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->user_firstname = $data['firstname'];
        $user->user_lastname = $data['lastname'];
        $user->role = 'student';
        $user->save();

        // Save enrollment
        $enrollment = new \App\Models\Enrollment();
        if ($data['enrollment_type'] === 'Modular') {
            $enrollment->Modular_enrollment = $data['course'];
            $enrollment->Full_Program = '';
        } else {
            $enrollment->Modular_enrollment = '';
            $enrollment->Full_Program = $data['course'];
        }
        $enrollment->package_id = $data['package_id'];
        $enrollment->save();

        // Link user and enrollment
        $user->enrollment_id = $enrollment->enrollment_id;
        $user->save();

        // Clear session
        session()->forget('enrollment');

        return redirect()->route('home')->with('success', 'Registration and enrollment successful!');
    }

    public function validateStep3(Request $request)
    {
        $rules = [
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'enrollment_type' => 'required|in:Full,Modular'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step 3 validation passed'
        ]);
    }

    public function validateStep4(Request $request)
    {
        $rules = [
            'First_Name' => 'required|string|max:255',
            'Last_Name' => 'required|string|max:255',
            'program_id' => 'nullable',  // Will be filled automatically
            'start_date' => 'nullable',  // Will be filled automatically
            'batch_id' => 'nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step 4 validation passed',
            'data' => $validator->validated()
        ]);
    }
    
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info('Registration process started', $request->except(['password', 'password_confirmation']));

            // Validate the request
            $validated = $request->validate([
                'user_firstname' => 'required|string|max:255',
                'user_lastname' => 'required|string|max:255',
                'user_email' => 'required|email|unique:users,email|unique:admins,email|unique:directors,email',
                'password' => 'required|min:8|confirmed',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,program_id',
                'package_id' => 'required|exists:packages,package_id',
                'learning_mode' => 'required|in:synchronous,asynchronous',
                'enrollment_type' => 'required|in:Full,Modular',
                'start_date' => 'nullable|date',
                'batch_id' => 'nullable|exists:student_batches,batch_id'
            ]);

            // Check for existing enrollment
            $existingEnrollment = DB::table('enrollments')
                ->join('users', 'enrollments.user_id', '=', 'users.user_id')
                ->where('users.email', $validated['user_email'])
                ->where('enrollments.program_id', $validated['program_id'])
                ->where('enrollments.learning_mode', $validated['learning_mode'])
                ->first();

            if ($existingEnrollment) {
                throw new \Exception('You are already enrolled in this program with the same learning mode.');
            }

            // Create user account
            $user = User::create([
                'user_firstname' => $validated['user_firstname'],
                'user_lastname' => $validated['user_lastname'],
                'email' => $validated['user_email'],
                'password' => bcrypt($validated['password']),
                'role' => 'unverified', // Don't auto-verify new users
                'enrollment_id' => 0 // Temporary, will be updated after enrollment creation
            ]);

            // Handle batch assignment for synchronous mode
            $batchId = null;
            $startDate = null;
            $endDate = null;

            if ($validated['learning_mode'] === 'synchronous') {
                if ($validated['batch_id']) {
                    // Use existing batch
                    $batch = \App\Models\StudentBatch::find($validated['batch_id']);
                    if ($batch && $batch->current_capacity < $batch->max_capacity) {
                        $batchId = $batch->batch_id;
                        $startDate = $batch->start_date;
                        $endDate = $batch->end_date;
                        $batch->increment('current_capacity');
                    } else {
                        // Create new batch if selected batch is full
                        $batch = $this->createPendingBatch($validated['program_id']);
                        $batchId = $batch->batch_id;
                        $startDate = $batch->start_date;
                        $endDate = $batch->end_date;
                    }
                } else {
                    // No batch selected, create new pending batch
                    $batch = $this->createPendingBatch($validated['program_id']);
                    $batchId = $batch->batch_id;
                    $startDate = $batch->start_date;
                    $endDate = $batch->end_date;
                }
            } else {
                // Asynchronous mode - set individual start and end dates
                $startDate = $validated['start_date'] ?? now()->addDays(1);
                $endDate = \Carbon\Carbon::parse($startDate)->addMonths(8);
            }

            // Create enrollment
            $enrollment = \App\Models\Enrollment::create([
                'user_id' => $user->user_id,
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id'],
                'enrollment_type' => $validated['enrollment_type'],
                'learning_mode' => $validated['learning_mode'],
                'batch_id' => $batchId,
                'enrollment_status' => 'pending',
                'payment_status' => 'pending',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'batch_access_granted' => false
            ]);

            // Update user's enrollment_id
            $user->update(['enrollment_id' => $enrollment->enrollment_id]);

            // Create registration record
            $registration = \App\Models\Registration::create([
                'user_id' => $user->user_id,
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'program_id' => $validated['program_id'],
                'package_id' => $validated['package_id'],
                'learning_mode' => $validated['learning_mode'],
                'batch_id' => $batchId,
                'Start_Date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending',
                'package_name' => \App\Models\Package::find($validated['package_id'])->package_name,
                'plan_name' => $validated['enrollment_type'],
                'program_name' => \App\Models\Program::find($validated['program_id'])->program_name,
                'plan_id' => $validated['enrollment_type'] === 'Full' ? 1 : 2,
                // Add other required fields with defaults
                'middlename' => $request->middlename ?? '',
                'student_school' => $request->student_school ?? '',
                'street_address' => $request->street_address ?? '',
                'state_province' => $request->state_province ?? '',
                'city' => $request->city ?? '',
                'zipcode' => $request->zipcode ?? '',
                'contact_number' => $request->contact_number ?? '',
                'emergency_contact_number' => $request->emergency_contact_number ?? '',
                'Undergraduate' => $request->Undergraduate ?? 'no',
                'Graduate' => $request->Graduate ?? 'no'
            ]);

            // Update enrollment with registration_id
            $enrollment->update(['registration_id' => $registration->registration_id]);

            DB::commit();

            Log::info('Registration completed successfully', [
                'user_id' => $user->user_id,
                'enrollment_id' => $enrollment->enrollment_id,
                'registration_id' => $registration->registration_id,
                'batch_id' => $batchId,
                'learning_mode' => $validated['learning_mode']
            ]);

            // DO NOT auto-login the user - redirect to success page instead
            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please wait for admin approval.',
                'redirect' => route('registration.success')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a pending batch for auto-enrollment
     */
    private function createPendingBatch($programId)
    {
        $program = \App\Models\Program::find($programId);
        $batchCount = \App\Models\StudentBatch::where('program_id', $programId)->count() + 1;
        
        return \App\Models\StudentBatch::create([
            'batch_name' => $program->program_name . ' Batch ' . $batchCount,
            'program_id' => $programId,
            'max_capacity' => 10,
            'current_capacity' => 1,
            'batch_status' => 'pending',
            'start_date' => now()->addDays(14), // 2 weeks from now
            'end_date' => now()->addDays(14)->addMonths(8), // 8 months from start
            'registration_deadline' => now()->addDays(10),
            'description' => 'Auto-created batch for new enrollments. Awaiting admin verification.',
            'created_by' => 1 // Default admin
        ]);
    }

    /**
     * Validate uploaded file using OCR
     */
    public function validateFileUpload(Request $request)
    {
        // ALWAYS return JSON response - prevent HTML error pages
        try {
            Log::info('File upload validation started', [
                'request_data' => $request->except(['file']),
                'has_file' => $request->hasFile('file')
            ]);

            // Basic validation first
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'field_name' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('File upload validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ], 400);
            }

            $file = $request->file('file');
            $fieldName = $request->input('field_name');
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');

            Log::info('Processing file upload', [
                'field_name' => $fieldName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'file_size' => $file->getSize(),
                'file_extension' => $file->getClientOriginalExtension()
            ]);

            // Ensure storage directory exists
            if (!file_exists(storage_path('app/public/documents'))) {
                mkdir(storage_path('app/public/documents'), 0755, true);
            }

            // Store file
            $permanentPath = $file->store('documents', 'public');

            if (!$permanentPath) {
                throw new \Exception('Failed to store file');
            }

            Log::info('File stored successfully', ['path' => $permanentPath]);

            // Perform OCR validation
            $fullPath = storage_path('app/public/' . $permanentPath);
            $extractedText = $this->ocrService->extractText($fullPath);
            
            Log::info('OCR extraction completed', [
                'extracted_text_length' => strlen($extractedText),
                'extracted_text_preview' => substr($extractedText, 0, 200)
            ]);

            // Validate name against document
            $nameValid = $this->ocrService->validateUserName($extractedText, $firstName, $lastName);
            
            if (!$nameValid) {
                Log::warning('Name validation failed', [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'extracted_text_preview' => substr($extractedText, 0, 200)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Name validation failed. The document does not appear to contain your full name. Please ensure the document is clear and contains your complete name.'
                ], 400);
            }

            // Get program suggestions
            $suggestions = $this->ocrService->suggestPrograms($extractedText);
            
            // Analyze certificate level
            $certificateLevel = $this->ocrService->analyzeCertificateLevel($extractedText);
            
            Log::info('OCR validation completed successfully', [
                'name_valid' => $nameValid,
                'suggestions_count' => count($suggestions),
                'certificate_level' => $certificateLevel
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document validated successfully.',
                'file_path' => $permanentPath,
                'suggestions' => $suggestions,
                'certificate_level' => $certificateLevel
            ]);

        } catch (\Throwable $e) {
            Log::error('File validation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // ALWAYS return JSON, even on fatal errors
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Get user data for pre-filling form
     */
    public function getUserPrefillData(Request $request)
    {
        if (!SessionManager::isLoggedIn()) {
            return response()->json(['error' => 'Not logged in'], 401);
        }

        try {
            $userId = SessionManager::get('user_id');
            
            // Get user data
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Get latest registration data
            $registration = DB::table('registrations')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

            // Get student data if exists
            $student = DB::table('students')
                ->where('user_id', $userId)
                ->first();

            // Prepare prefill data with multiple fallback options
            $prefillData = [
                'firstname' => $user->user_firstname ?? '',
                'lastname' => $user->user_lastname ?? '',
                'email' => $user->email ?? '',
            ];

            Log::info('User data for prefill', [
                'user_firstname' => $user->user_firstname ?? 'null',
                'user_lastname' => $user->user_lastname ?? 'null', 
                'prefill_firstname' => $prefillData['firstname'],
                'prefill_lastname' => $prefillData['lastname']
            ]);

            // Add registration data if available
            if ($registration) {
                $registrationData = (array) $registration;
                foreach ($registrationData as $key => $value) {
                    if (!in_array($key, ['registration_id', 'user_id', 'created_at', 'updated_at']) && $value !== null) {
                        $prefillData[$key] = $value;
                    }
                }

                // Handle dynamic fields JSON
                if ($registration->dynamic_fields) {
                    $dynamicFields = json_decode($registration->dynamic_fields, true);
                    if (is_array($dynamicFields)) {
                        $prefillData = array_merge($prefillData, $dynamicFields);
                    }
                }
            }

            // Add student data if available
            if ($student) {
                $studentData = (array) $student;
                foreach ($studentData as $key => $value) {
                    if (!in_array($key, ['student_id', 'user_id', 'created_at', 'updated_at']) && $value !== null && !isset($prefillData[$key])) {
                        $prefillData[$key] = $value;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $prefillData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting prefill data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user data'
            ], 500);
        }
    }
}
