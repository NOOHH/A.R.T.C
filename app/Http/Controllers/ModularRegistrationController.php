<?php

namespace App\Http\Controllers;

use App\Helpers\SessionManager;
use App\Models\User;
use App\Models\Student;
use App\Models\Registration;
use App\Models\Enrollment;
use App\Models\EnrollmentCourse;
use App\Models\Program;
use App\Models\Package;
use App\Models\Module;
use App\Models\EducationLevel;
use App\Models\FormRequirement;
use App\Models\Director;
use App\Models\Professor;
use App\Models\Plan;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ModularRegistrationController extends Controller
{
    protected $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Show modular enrollment form
     */
    public function showForm(Request $request)
    {
        try {
            // Enhanced debug logging to diagnose navigation issues
            Log::info('=== MODULAR ENROLLMENT PAGE REQUESTED ===', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->header('User-Agent'),
                'is_ajax' => $request->ajax(),
                'ip' => $request->ip(),
                'referer' => $request->header('referer'),
                'time' => now()->toDateTimeString()
            ]);
            
            // Get all programs with modular packages
            $allPrograms = Program::with(['modules.courses', 'packages' => function($q) { 
                $q->where('package_type', 'modular'); 
            }])
            // Check if archived column exists, otherwise don't filter by it
            ->when(Schema::hasColumn('programs', 'archived'), function($q) {
                return $q->where('archived', false);
            })
            ->whereHas('packages', function($q) { $q->where('package_type', 'modular'); })
            ->get();

            // Get only modular packages
            $packages = Package::when(Schema::hasColumn('packages', 'archived'), function($q) {
                    return $q->where('archived', false);
                })
                ->where('package_type', 'modular')
                ->get();

            // Auto-generate default modular package if none exist
            if ($packages->isEmpty()) {
                $defaultPackage = Package::create([
                    'package_name' => 'Standard Modular Package',
                    'description' => 'Flexible modular package allowing course-by-course enrollment',
                    'package_price' => 0,
                    'package_type' => 'modular',
                    'archived' => false
                ]);
                $packages = collect([$defaultPackage]);
                Log::info('Auto-generated default modular package', ['package_id' => $defaultPackage->package_id]);
            }

            // Get form requirements for modular enrollment
            $formRequirements = FormRequirement::active()
                ->forProgram('modular')
                ->get();

            // Get education levels for modular plan
            $educationLevels = EducationLevel::where('is_active', true)
                ->where('available_modular_plan', true)
                ->orderBy('level_order', 'asc')
                ->get();

            $modularPlan = Plan::where('plan_id', 2)->first(); // Modular Plan

            // Get student data if logged in
            $student = null;
            if (SessionManager::isLoggedIn()) {
                $userId = SessionManager::get('user_id');
                $user = User::with('student')->find($userId);
                if ($user && $user->student) {
                    $student = $user->student;
                }
            }

            // Get program structure for display
            $programs = $allPrograms->map(function($program) {
                return [
                    'program_id' => $program->program_id,
                    'program_name' => $program->program_name,
                    'program_description' => $program->program_description,
                    'modules' => $program->modules->map(function($module) {
                        return [
                            'module_id' => $module->module_id,
                            'module_name' => $module->module_name,
                            'module_description' => $module->module_description,
                            'courses' => $module->courses->map(function($course) {
                                return [
                                    'subject_id' => $course->subject_id,
                                    'subject_name' => $course->subject_name,
                                    'subject_description' => $course->subject_description,
                                ];
                            })->toArray()
                        ];
                    })->toArray()
                ];
            })->toArray();

            $programId = $request->query('program_id');

            // Add debug logging to confirm the view is being rendered
            Log::info('Rendering Modular_enrollment.blade.php view', [
                'timestamp' => now()->toDateTimeString(),
                'url' => $request->fullUrl(),
                'program_count' => count($programs),
                'package_count' => count($packages),
                'program_id' => $programId,
                'user_agent' => $request->header('User-Agent')
            ]);

            return view('registration.Modular_enrollment', compact(
                'programs', 
                'packages', 
                'programId', 
                'formRequirements', 
                'educationLevels', 
                'student', 
                'modularPlan'
            ));

        } catch (\Exception $e) {
            Log::error('Modular enrollment form error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Always use the Modular_enrollment view in case of errors
            try {
                Log::info('Loading Modular_enrollment view due to error in main view');
                return view('registration.Modular_enrollment')->with([
                    'error' => $e->getMessage(),
                    'programs' => \App\Models\Program::when(Schema::hasColumn('programs', 'archived'), function($q) {
                        return $q->where('archived', false);
                    })->get()->toArray(),
                    'packages' => \App\Models\Package::when(Schema::hasColumn('packages', 'archived'), function($q) {
                        return $q->where('archived', false);
                    })->where('package_type', 'modular')->get()
                ]);
            } catch (\Exception $viewError) {
                Log::error('Failed to load simplified view: ' . $viewError->getMessage());
                
                // As a last resort, return a basic HTML response
                return response()->view('layouts.error', [
                    'title' => 'Enrollment Error',
                    'message' => 'Unable to load the modular enrollment form. Please try again later.',
                    'details' => $e->getMessage(),
                    'returnUrl' => url('/enrollment')  // Using url() helper instead of route() helper
                ], 500);
            }
        }
    }

    /**
     * Submit modular enrollment
     */
    public function submitEnrollment(Request $request)
    {
        try {
            DB::beginTransaction();

            // DUPLICATE PREVENTION: Check for recent registration with same data
            $userEmail = $request->email ?? $request->user_email;
            
            // Join with users table to check email since registrations table doesn't have email column
            $duplicateCheck = Registration::join('users', 'registrations.user_id', '=', 'users.user_id')
                ->where('users.email', $userEmail)
                ->where('registrations.program_id', $request->program_id)
                ->where('registrations.package_id', $request->package_id)
                ->where('registrations.enrollment_type', 'Modular')
                ->where('registrations.created_at', '>=', now()->subMinutes(5))
                ->first();
                
            if ($duplicateCheck) {
                DB::rollBack();
                Log::warning('Duplicate modular registration attempt prevented', [
                    'email' => $userEmail,
                    'program_id' => $request->program_id,
                    'package_id' => $request->package_id,
                    'recent_registration_id' => $duplicateCheck->registration_id
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration already completed successfully!',
                    'redirect' => '/registration/success',
                    'data' => [
                        'registration_id' => $duplicateCheck->registration_id,
                        'user_id' => $duplicateCheck->user_id
                    ]
                ]);
            }

            Log::info('=== MODULAR ENROLLMENT STARTED ===', [
                'request_method' => $request->method(),
                'all_input_data' => $request->except(['password', 'password_confirmation']),
                'selected_modules_value' => $request->input('selected_modules'),
                'form_files' => array_keys($request->allFiles()),
                'has_files' => !empty($request->allFiles()),
                'file_count' => count($request->allFiles()),
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

            // Add file validation rules for education level requirements
            $this->addFileValidationRules($rules, $request);

            // Validate the request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                DB::rollBack();
                
                $errors = $validator->errors()->toArray();
                $enhancedErrors = $this->enhanceFileErrors($errors, $rules);
                
                Log::error('Modular enrollment validation failed', [
                    'errors' => $errors,
                    'enhanced_errors' => $enhancedErrors,
                    'input_data' => $request->except(['password', 'password_confirmation', '_token']),
                    'has_selected_modules' => $request->has('selected_modules'),
                    'selected_modules_value' => $request->input('selected_modules'),
                    'form_files' => array_keys($request->allFiles()),
                    'education_level' => $request->input('education_level'),
                    'validation_rules' => array_keys($rules)
                ]);
                
                return response()->json([
                    'success' => false,
                    'errors' => $enhancedErrors
                ], 422);
            }

            $validated = $validator->validated();

            // Process uploaded files
            $uploadedFiles = $this->processUploadedFiles($request);

            // Process dynamic form fields
            $dynamicFields = $this->processDynamicFields($request);

            // Handle referral code to set directors_id
            $directorsId = $this->handleReferralCode($validated['referral_code'] ?? null);

            // Create or get user
            $user = $this->createOrGetUser($validated, $directorsId);

            // Get package and program details
            $package = Package::find($validated['package_id']);
            $program = Program::find($validated['program_id']);

            // Parse selected modules
            $selectedModules = $this->parseSelectedModules($validated['selected_modules']);
            $selectedCourses = $this->extractSelectedCourses($selectedModules);

            // Create registration record
            $registration = $this->createRegistration($validated, $user, $package, $program, $selectedModules, $selectedCourses, $uploadedFiles, $dynamicFields);

            // Handle batch assignment
            $batchId = $this->handleBatchAssignment($validated);

            // Create enrollment record
            $enrollment = $this->createEnrollment($validated, $user, $registration, $batchId);

            // Update user with enrollment_id
            $user->update(['enrollment_id' => $enrollment->enrollment_id]);

            // Create module and course registrations
            $this->createModuleRegistrations($selectedModules, $registration, $enrollment, $user);

            DB::commit();

            Log::info('Modular enrollment completed successfully', [
                'user_id' => $user->user_id,
                'enrollment_id' => $enrollment->enrollment_id,
                'registration_id' => $registration->registration_id,
                'uploaded_files_count' => count($uploadedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Modular enrollment submitted successfully! Please wait for admin approval.',
                'redirect' => route('registration.success'),
                'data' => [
                    'registration_id' => $registration->registration_id,
                    'user_id' => $user->user_id,
                    'enrollment_id' => $enrollment->enrollment_id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Modular enrollment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Modular enrollment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate modular enrollment step
     */
    public function validateStep(Request $request)
    {
        $rules = [
            'selected_modules' => 'required|string',
            'program_id' => 'required|exists:programs,program_id',
            'package_id' => 'required|exists:packages,package_id',
            'learning_mode' => 'required|in:synchronous,asynchronous',
            'education_level' => 'required|string|in:Undergraduate,Graduate',
            'Start_Date' => 'required|date'
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
            'message' => 'Modular enrollment validation passed',
            'data' => $validator->validated()
        ]);
    }

    /**
     * Get batches for modular program
     */
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
            Log::error('Error fetching modular batches', [
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

    /**
     * Validate uploaded file using OCR for modular enrollment
     */
    public function validateFileUpload(Request $request)
    {
        try {
            Log::info('Modular file upload validation started', [
                'request_data' => $request->except(['file']),
                'has_file' => $request->hasFile('file')
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf,gif,bmp,tiff,webp|max:10240',
                'field_name' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Modular file upload validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ], 400);
            }

            $file = $request->file('file');
            $fieldName = $request->input('field_name');
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');

            // Ensure storage directory exists
            if (!file_exists(storage_path('app/public/documents'))) {
                mkdir(storage_path('app/public/documents'), 0755, true);
            }

            // Store file
            $permanentPath = $file->store('documents', 'public');

            if (!$permanentPath) {
                throw new \Exception('Failed to store file');
            }

            Log::info('File stored successfully for modular enrollment', ['path' => $permanentPath]);

            // Perform OCR validation
            $fullPath = storage_path('app/public/' . $permanentPath);
            
            try {
                $extractedText = $this->ocrService->extractText($fullPath);
            } catch (\Exception $ocrException) {
                Log::error('OCR extraction failed for modular enrollment', [
                    'file_path' => $fullPath,
                    'error' => $ocrException->getMessage()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully. OCR validation unavailable at the moment.',
                    'file_path' => $permanentPath,
                    'suggestions' => [],
                    'certificate_level' => null,
                    'ocr_note' => 'OCR processing failed but file was uploaded successfully'
                ]);
            }
            
            // Validate name against document
            $nameValid = $this->validateNameAgainstDocument($extractedText, $firstName, $lastName);
            
            if (!$nameValid) {
                Log::warning('Name validation failed for modular enrollment', [
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
            try {
                $suggestions = $this->ocrService->suggestPrograms($extractedText);
            } catch (\Exception $e) {
                Log::warning('Program suggestions failed for modular enrollment', ['error' => $e->getMessage()]);
                $suggestions = [];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Document validated successfully.',
                'file_path' => $permanentPath,
                'suggestions' => $suggestions,
                'ocr_metadata' => [
                    'text_length' => strlen($extractedText),
                    'name_validation' => $nameValid
                ]
            ]);

        } catch (\Throwable $e) {
            Log::error('Modular file validation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Get user data for pre-filling form (modular-specific)
     */
    public function userPrefill(Request $request)
    {
        try {
            $laravelUserId = session('user_id');
            $phpSessionUserId = SessionManager::get('user_id');
            $isLoggedIn = !empty($laravelUserId) || SessionManager::isLoggedIn();
            
            Log::info('Modular UserPrefill called', [
                'laravel_user_id' => $laravelUserId,
                'php_session_user_id' => $phpSessionUserId,
                'is_logged_in' => $isLoggedIn
            ]);
            
            if (!$isLoggedIn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not logged in',
                    'data' => []
                ], 200);
            }

            $userId = $laravelUserId ?: $phpSessionUserId;
            $user = User::findOrFail($userId);

            $prefill = [
                'firstname' => $user->user_firstname,
                'lastname' => $user->user_lastname,
                // 'email' field removed - doesn't exist in registrations table
            ];

            return response()->json([
                'success' => true,
                'data' => $prefill
            ]);
        } catch (\Throwable $e) {
            Log::error("Modular userPrefill error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not load your data, please try again.',
                'data' => []
            ], 500);
        }
    }

    // Private helper methods

    private function addFileValidationRules(&$rules, Request $request)
    {
        $selectedEducationLevel = $request->input('education_level');
        Log::info('Selected education level for modular file validation', ['education_level' => $selectedEducationLevel]);
        
        if ($selectedEducationLevel) {
            $educationLevel = EducationLevel::where('level_name', $selectedEducationLevel)->first();
            
            // Initialize empty requirements array
            $fileRequirements = [];
            
            // Only process if we have an education level
            if ($educationLevel) {
                try {
                    // Get file_requirements with safe null-coalescing
                    $rawRequirements = $educationLevel->file_requirements ?? null;
                    
                    if ($rawRequirements) {
                        if (is_string($rawRequirements)) {
                            // Safely parse JSON string
                            $decoded = json_decode($rawRequirements, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $fileRequirements = $decoded;
                            }
                        } elseif (is_array($rawRequirements)) {
                            // Already an array, use directly
                            $fileRequirements = $rawRequirements;
                        }
                    }
                } catch (\Exception $e) {
                    // Log any issues but continue with empty requirements
                    Log::error('Error processing file requirements: ' . $e->getMessage());
                }
            }
                
            if (is_array($fileRequirements)) {
                foreach ($fileRequirements as $requirement) {
                    if (isset($requirement['available_modular_plan']) && $requirement['available_modular_plan']) {
                        $fieldName = $requirement['field_name'] ?? $requirement['document_type'];
                        
                        if ($fieldName) {
                            $normalizedFieldName = strtolower($fieldName);
                            $hasFile = $request->hasFile($normalizedFieldName);
                            $isRequired = isset($requirement['is_required']) && $requirement['is_required'];

                            if ($isRequired) {
                                $rules[$normalizedFieldName] = 'required|file|max:10240';
                            } elseif ($hasFile) {
                                $rules[$normalizedFieldName] = 'file|max:10240';
                            }
                        }
                    }
                }
            }
        }
    }

    private function enhanceFileErrors($errors, $rules)
    {
        $enhancedErrors = [];
        
        foreach ($errors as $field => $messages) {
            if (in_array($field, array_keys($rules)) && strpos($rules[$field], 'file') !== false) {
                $enhancedErrors[$field] = [
                    "The {$field} file is required for your selected education level. Please upload the required document."
                ];
            } else {
                $enhancedErrors[$field] = $messages;
            }
        }
        
        return $enhancedErrors;
    }

    private function processUploadedFiles(Request $request)
    {
        $uploadedFiles = [];
        $allFiles = $request->allFiles();
        
        if (!empty($allFiles)) {
            foreach ($allFiles as $fieldName => $file) {
                try {
                    if ($file && $file->isValid()) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads/education_requirements', $fileName, 'public');
                        $uploadedFiles[$fieldName] = $filePath;
                        Log::info('Modular file uploaded successfully', [
                            'field' => $fieldName,
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $filePath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Modular file upload failed', [
                        'field' => $fieldName,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $uploadedFiles;
    }

    private function processDynamicFields(Request $request)
    {
        $dynamicFields = [];
        $formRequirements = FormRequirement::active()
            ->forProgram('modular')
            ->get();
            
        foreach ($formRequirements as $requirement) {
            $fieldName = $requirement->field_name;
            if ($request->has($fieldName)) {
                $dynamicFields[$fieldName] = $request->input($fieldName);
            }
        }
        
        return $dynamicFields;
    }

    private function handleReferralCode($referralCode)
    {
        $directorsId = null;
        
        if (!empty($referralCode)) {
            // Check if referral code is from a director
            $director = Director::where('referral_code', $referralCode)
                ->where('directors_archived', false)
                ->first();
            
            if ($director) {
                $directorsId = $director->directors_id;
                Log::info('Modular referral from director', ['director_id' => $directorsId, 'referral_code' => $referralCode]);
            } else {
                // Check if referral code is from a professor
                $professor = Professor::where('referral_code', $referralCode)
                    ->where('is_archived', false)
                    ->first();
                
                if ($professor) {
                    Log::info('Modular referral from professor', ['professor_id' => $professor->professor_id, 'referral_code' => $referralCode]);
                }
            }
        }
        
        return $directorsId;
    }

    private function createOrGetUser($validated, $directorsId)
    {
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
                'admin_id' => 1,
                'directors_id' => $directorsId,
                'enrollment_id' => 0
            ]);
            
            Log::info('User created successfully for modular enrollment', ['user_id' => $user->user_id]);
        }

        if (!$user || !$user->user_id) {
            throw new \Exception('Unable to create or find valid user for modular enrollment');
        }
        
        return $user;
    }

    private function parseSelectedModules($selectedModulesData)
    {
        if (is_string($selectedModulesData)) {
            return json_decode($selectedModulesData, true) ?? [];
        } else {
            return $selectedModulesData;
        }
    }

    private function extractSelectedCourses($selectedModules)
    {
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
        
        return $selectedCourses;
    }

    private function createRegistration($validated, $user, $package, $program, $selectedModules, $selectedCourses, $uploadedFiles, $dynamicFields)
    {
        $registrationData = [
            'user_id' => $user->user_id,
            'firstname' => $validated['user_firstname'] ?? ($user->user_firstname ?? ''),
            'lastname' => $validated['user_lastname'] ?? ($user->user_lastname ?? ''),
            // 'email' field removed as it doesn't exist in registrations table
            'program_id' => $validated['program_id'],
            'package_id' => $validated['package_id'],
            'program_name' => $program->program_name ?? '',
            'package_name' => $package->package_name ?? '',
            'learning_mode' => $validated['learning_mode'],
            'enrollment_type' => $validated['enrollment_type'],
            'education_level' => $validated['education_level'],
            'referral_code' => $validated['referral_code'] ?? '',
            'selected_modules' => json_encode($selectedModules),
            'selected_courses' => json_encode($selectedCourses),
            'Start_Date' => $validated['Start_Date'],
            'status' => 'pending',
            'dynamic_fields' => json_encode(array_merge([
                'referral_code' => $validated['referral_code'] ?? '',
                'registration_mode' => $validated['learning_mode']
            ], $dynamicFields))
        ];

        // Add uploaded file paths to registration data
        foreach ($uploadedFiles as $fieldName => $filePath) {
            $columnName = $this->mapFileFieldToColumn($fieldName);
            if ($columnName) {
                $registrationData[$columnName] = $filePath;
            }
        }

        return Registration::create($registrationData);
    }

    private function handleBatchAssignment($validated)
    {
        $batchId = null;
        
        if ($validated['learning_mode'] === 'synchronous' && isset($validated['batch_id'])) {
            $batch = \App\Models\StudentBatch::find($validated['batch_id']);
            if ($batch && $batch->current_capacity < $batch->max_capacity) {
                $batchId = $batch->batch_id;
                $batch->increment('current_capacity');
                Log::info('Modular batch assigned', ['batch_id' => $batchId]);
            }
        }
        
        return $batchId;
    }

    private function createEnrollment($validated, $user, $registration, $batchId)
    {
        return Enrollment::create([
            'user_id' => $user->user_id,
            'registration_id' => $registration->registration_id,
            'student_id' => null, // Will be set when admin approves
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
    }

    private function createModuleRegistrations($selectedModules, $registration, $enrollment, $user)
    {
        if (!is_array($selectedModules) || count($selectedModules) === 0) {
            return;
        }

        $enrolledCourseIds = [];
        
        // Get already enrolled course IDs for this user
        $existingEnrolledCourseIds = EnrollmentCourse::whereHas('enrollment', function($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->where('enrollment_status', '!=', 'rejected');
        })->where('is_active', true)
          ->pluck('course_id')
          ->toArray();
        
        Log::info('Existing enrolled courses for modular user', [
            'user_id' => $user->user_id,
            'existing_enrolled_course_ids' => $existingEnrolledCourseIds
        ]);
        
        foreach ($selectedModules as $moduleData) {
            $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
            
            if ($moduleId) {
                try {
                    // Create module registration if model exists
                    if (class_exists('\App\Models\RegistrationModule')) {
                        \App\Models\RegistrationModule::create([
                            'registration_id' => $registration->registration_id,
                            'module_id' => $moduleId
                        ]);
                    }
                    
                    Log::info('Modular module registered', ['module_id' => $moduleId, 'registration_id' => $registration->registration_id]);
                    
                    // Handle course-level enrollments
                    if (is_array($moduleData) && isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                        foreach ($moduleData['selected_courses'] as $courseData) {
                            $courseId = is_array($courseData) ? ($courseData['id'] ?? $courseData['course_id'] ?? null) : $courseData;
                            
                            if ($courseId && !in_array($courseId, $existingEnrolledCourseIds) && !in_array($courseId, $enrolledCourseIds)) {
                                try {
                                    EnrollmentCourse::create([
                                        'enrollment_id' => $enrollment->enrollment_id,
                                        'course_id' => $courseId,
                                        'module_id' => $moduleId,
                                        'enrollment_type' => 'course',
                                        'course_price' => 0,
                                        'is_active' => true
                                    ]);
                                    $enrolledCourseIds[] = $courseId;
                                    
                                    Log::info('Modular course enrolled', [
                                        'course_id' => $courseId, 
                                        'module_id' => $moduleId,
                                        'enrollment_id' => $enrollment->enrollment_id
                                    ]);
                                } catch (\Exception $e) {
                                    Log::warning('Failed to create modular course enrollment', [
                                        'course_id' => $courseId,
                                        'module_id' => $moduleId,
                                        'enrollment_id' => $enrollment->enrollment_id,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                        }
                    } else {
                        // Enroll in all courses of the module
                        $module = Module::with('courses')->find($moduleId);
                        if ($module && $module->courses) {
                            foreach ($module->courses as $course) {
                                if (!in_array($course->subject_id, $existingEnrolledCourseIds) && !in_array($course->subject_id, $enrolledCourseIds)) {
                                    try {
                                        EnrollmentCourse::create([
                                            'enrollment_id' => $enrollment->enrollment_id,
                                            'course_id' => $course->subject_id,
                                            'module_id' => $moduleId,
                                            'enrollment_type' => 'course',
                                            'course_price' => 0,
                                            'is_active' => true
                                        ]);
                                        $enrolledCourseIds[] = $course->subject_id;
                                        
                                        Log::info('Modular course enrolled (all module courses)', [
                                            'course_id' => $course->subject_id,
                                            'course_name' => $course->subject_name,
                                            'module_id' => $moduleId,
                                            'enrollment_id' => $enrollment->enrollment_id
                                        ]);
                                    } catch (\Exception $e) {
                                        Log::warning('Failed to create modular course enrollment (all module courses)', [
                                            'course_id' => $course->subject_id,
                                            'module_id' => $moduleId,
                                            'enrollment_id' => $enrollment->enrollment_id,
                                            'error' => $e->getMessage()
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to create modular module registration', [
                        'module_id' => $moduleId,
                        'registration_id' => $registration->registration_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        Log::info('Modular course enrollment summary', [
            'enrollment_id' => $enrollment->enrollment_id,
            'total_enrolled_courses' => count($enrolledCourseIds),
            'enrolled_course_ids' => $enrolledCourseIds
        ]);
    }

    private function validateNameAgainstDocument($extractedText, $firstName, $lastName)
    {
        try {
            $nameValid = $this->ocrService->validateUserName($extractedText, $firstName, $lastName);
            
            // If strict validation fails, try a more lenient approach
            if (!$nameValid) {
                $firstNameExists = stripos($extractedText, $firstName) !== false;
                $lastNameExists = stripos($extractedText, $lastName) !== false;
                
                if ($firstNameExists || $lastNameExists) {
                    $nameValid = true;
                    Log::info('Modular name validation passed with lenient check', [
                        'first_name_found' => $firstNameExists,
                        'last_name_found' => $lastNameExists
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Modular name validation failed with error', ['error' => $e->getMessage()]);
            $nameValid = true; // Skip validation if there's an error
        }
        
        return $nameValid;
    }

    private function mapFileFieldToColumn($fieldName)
    {
        $mapping = [
            'good_moral' => 'good_moral',
            'psa' => 'PSA',
            'course_cert' => 'Course_Cert',
            'tor' => 'TOR',
            'cert_of_grad' => 'Cert_of_Grad',
            'photo_2x2' => 'photo_2x2',
            'diploma_certificate' => 'diploma_certificate',
            'medical_certificate' => 'medical_certificate',
            'passport_photo' => 'passport_photo',
            'valid_id' => 'valid_id',
            'birth_certificate' => 'birth_certificate'
        ];

        return $mapping[strtolower($fieldName)] ?? $fieldName;
    }
}
