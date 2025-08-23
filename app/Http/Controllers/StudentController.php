<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\OcrService;
use App\Models\FormRequirement;
use App\Models\Student;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        // Validate form fields and files
        $validated = $request->validate([
            'first_name'         => 'required|string|max:255',
            'middle_name'        => 'nullable|string|max:255',
            'last_name'          => 'required|string|max:255',
            'school'             => 'required|string|max:255',
            'street_address'     => 'required|string|max:255',
            'state'              => 'required|string|max:255',
            'city'               => 'required|string|max:255',
            'zip'                => 'required|string|max:10',
            'email'              => 'required|email|max:255',
            'contact_number'     => 'required|string|max:20',
            'emergency_contact'  => 'required|string|max:20',
            'education'          => 'required|string',
            'course_1'           => 'required|string',
            'course_2'           => 'nullable|string',
            'start_date'         => 'nullable|date',
            'terms'              => 'accepted',

            // Optional: File validation rules
            'good_moral'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'birth_cert'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'course_cert'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tor'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'grad_cert'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo'              => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        // Handle file uploads with enhanced validation
        $uploadedFiles = [];
        $ocrText = null;
        $ocrSuggestions = [];
        
        // Define allowed file types for each field
        $fileFields = [
            'good_moral' => ['pdf', 'jpg', 'jpeg', 'png'],
            'birth_cert' => ['pdf', 'jpg', 'jpeg', 'png'], 
            'course_cert' => ['pdf', 'jpg', 'jpeg', 'png'],
            'tor' => ['pdf', 'jpg', 'jpeg', 'png'],
            'grad_cert' => ['pdf', 'jpg', 'jpeg', 'png'],
            'photo' => ['jpg', 'jpeg', 'png']
        ];
        
        foreach ($fileFields as $fieldName => $allowedTypes) {
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $fileExtension = strtolower($file->getClientOriginalExtension());
                
                // Validate file type
                if (!in_array($fileExtension, $allowedTypes)) {
                    return back()->withErrors([
                        $fieldName => "Invalid file type for {$fieldName}. Only " . implode(', ', $allowedTypes) . " files are allowed."
                    ])->withInput();
                }
                
                // Validate file size (max 10MB)
                if ($file->getSize() > 10485760) {
                    return back()->withErrors([
                        $fieldName => "File size for {$fieldName} exceeds 10MB limit."
                    ])->withInput();
                }
                
                // Store file with unique name
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('documents/' . $fieldName, $fileName, 'public');
                $uploadedFiles[$fieldName] = $path;
                
                // Special handling for course certificate with OCR
                if ($fieldName === 'course_cert') {
                    $ocrService = new OcrService();
                    $fullPath = storage_path('app/public/' . $path);
                    $ocrText = $ocrService->extractText($fullPath);
                    $ocrSuggestions = $ocrService->suggestPrograms($ocrText);
                }
            }
        }

        // Example: Save to database (uncomment after creating a Student model and migration)
        /*
        Student::create([
            ...$validated,
            ...$uploadedFiles,
        ]);
        */

        return back()->with([
            'success' => 'Registration submitted successfully!',
            'ocr_text' => $ocrText,
            'ocr_suggestions' => $ocrSuggestions,
        ]);
    }

    public function settings()
    {
        // Check if user is logged in via session
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user is a student
        if (session('user_role') !== 'student') {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
        }

        // Get user data from database
        $user = \App\Models\User::find(session('user_id'));
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get form requirements for student settings
        $formRequirements = FormRequirement::active()
            ->forProgram('both') // Show fields that apply to both full and modular
            ->ordered()
            ->get();

        // Try to get actual student data from database
        $student = Student::where('user_id', session('user_id'))->first();

        // If no student record exists, try to get registration data
        if (!$student) {
            $registration = \App\Models\Registration::where('user_id', session('user_id'))->first();
            if ($registration) {
                // Create student object from registration data
                $student = (object) [
                    'user_id' => $user->user_id,
                    'firstname' => $user->user_firstname,
                    'middlename' => $registration->middlename ?? '',
                    'lastname' => $user->user_lastname,
                    'email' => $user->email,
                    'student_school' => $registration->student_school ?? '',
                    'street_address' => $registration->street_address ?? '',
                    'state_province' => $registration->state_province ?? '',
                    'city' => $registration->city ?? '',
                    'zipcode' => $registration->zipcode ?? '',
                    'contact_number' => $registration->contact_number ?? '',
                    'emergency_contact_number' => $registration->emergency_contact_number ?? '',
                    'student_id' => $registration->registration_id ?? 'N/A',
                    'program_name' => 'Pending Registration',
                ];

                // Add dynamic fields from registration
                foreach ($formRequirements as $requirement) {
                    if (isset($registration->{$requirement->field_name})) {
                        $student->{$requirement->field_name} = $registration->{$requirement->field_name};
                    }
                }
            } else {
                // Create minimal student object from user data
                $student = (object) [
                    'user_id' => $user->user_id,
                    'firstname' => $user->user_firstname,
                    'middlename' => '',
                    'lastname' => $user->user_lastname,
                    'email' => $user->email,
                    'student_school' => '',
                    'street_address' => '',
                    'state_province' => '',
                    'city' => '',
                    'zipcode' => '',
                    'contact_number' => '',
                    'emergency_contact_number' => '',
                    'student_id' => 'N/A',
                    'program_name' => 'Not enrolled yet',
                ];
            }
        } else {
            // Update student with current user data
            $student->email = $user->email;
            $student->firstname = $user->user_firstname;
            $student->lastname = $user->user_lastname;
        }

        // Get enrollment information if available
        $enrollment = \App\Models\Enrollment::where('user_id', session('user_id'))->first();
        if ($enrollment && $enrollment->program) {
            $student->program_name = $enrollment->program->program_name;
        } elseif (!isset($student->program_name)) {
            $student->program_name = 'Not enrolled yet';
        }

        return view('student.student-settings.settings', compact('user', 'student', 'formRequirements'));
    }

    public function updateSettings(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Get form requirements for validation
        $formRequirements = FormRequirement::active()->get();
        
        Log::info('Student settings update started', [
            'user_id' => session('user_id'),
            'form_requirements_count' => $formRequirements->count(),
            'request_data_keys' => array_keys($request->all())
        ]);
        $validationRules = [
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'state_province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        // Add dynamic validation rules from form requirements
        foreach ($formRequirements as $requirement) {
            if ($requirement->field_type === 'section') continue;
            
            if ($requirement->is_required) {
                if ($requirement->field_type === 'file') {
                    $validationRules[$requirement->field_name] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
                } else {
                    $validationRules[$requirement->field_name] = 'required|string|max:255';
                }
            } else {
                if ($requirement->field_type === 'file') {
                    $validationRules[$requirement->field_name] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
                } else {
                    $validationRules[$requirement->field_name] = 'nullable|string|max:255';
                }
            }
        }

        // Validate the input
        $validated = $request->validate($validationRules);

        try {
            // Update user table
            $user = \App\Models\User::find(session('user_id'));
            if ($user) {
                $user->user_firstname = $validated['user_firstname'];
                $user->user_lastname = $validated['user_lastname'];
                $user->save();
                
                Log::info('Updated user table', ['user_id' => $user->user_id]);
            }

            // Handle file uploads
            $fileUploads = [];
            
            // Handle profile photo upload separately
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $profilePhotoName = 'profile_' . session('user_id') . '_' . time() . '.' . $profilePhoto->getClientOriginalExtension();
                $profilePhotoPath = $profilePhoto->storeAs('profile-photos', $profilePhotoName, 'public');
                $fileUploads['profile_photo'] = $profilePhotoName;
                
                Log::info('Profile photo uploaded', [
                    'user_id' => session('user_id'),
                    'filename' => $profilePhotoName,
                    'path' => $profilePhotoPath
                ]);
            }
            
            // Handle other dynamic form requirement file uploads
            foreach ($formRequirements as $requirement) {
                if ($requirement->field_type === 'file' && $request->hasFile($requirement->field_name)) {
                    $file = $request->file($requirement->field_name);
                    $filename = time() . '_' . $requirement->field_name . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('student-documents', $filename, 'public');
                    $fileUploads[$requirement->field_name] = $filename;
                }
            }

            // Prepare base student data
            $studentData = [
                'user_id' => session('user_id'),
                'firstname' => $validated['user_firstname'],
                'lastname' => $validated['user_lastname'],
                'middlename' => $validated['middlename'] ?? '',
                'email' => $user->email,
                'street_address' => $validated['street_address'] ?? '',
                'state_province' => $validated['state_province'] ?? '',
                'city' => $validated['city'] ?? '',
                'zipcode' => $validated['zipcode'] ?? '',
                'contact_number' => $validated['contact_number'] ?? '',
            ];
            
            // Add profile photo if uploaded
            if (isset($fileUploads['profile_photo'])) {
                $studentData['profile_photo'] = $fileUploads['profile_photo'];
                Log::info('Added profile photo to student data', ['filename' => $fileUploads['profile_photo']]);
            }

            // Add dynamic fields and file uploads
            foreach ($formRequirements as $requirement) {
                if ($requirement->field_type === 'section') continue;
                
                $fieldName = $requirement->field_name;
                
                // Ensure column exists in students table, create if it doesn't
                if (!FormRequirement::columnExists($fieldName, 'students')) {
                    Log::warning("Column '{$fieldName}' doesn't exist in students table, creating it", [
                        'field_name' => $fieldName,
                        'field_type' => $requirement->field_type
                    ]);
                    
                    // Try to create the column
                    FormRequirement::createDatabaseColumn(
                        $fieldName,
                        $requirement->field_type
                    );
                }
                
                // Add field data regardless of column existence check
                // Let Laravel handle any database errors during save
                if (isset($fileUploads[$fieldName])) {
                    $studentData[$fieldName] = $fileUploads[$fieldName];
                    Log::info("Added file upload for field: {$fieldName}", ['filename' => $fileUploads[$fieldName]]);
                } elseif (isset($validated[$fieldName])) {
                    $studentData[$fieldName] = $validated[$fieldName];
                    Log::info("Added validated data for field: {$fieldName}", ['value' => $validated[$fieldName]]);
                }
            }

            // Debug: Log the data being prepared for student update
            Log::info('Student data prepared for update/create', [
                'user_id' => session('user_id'),
                'student_data' => $studentData,
                'form_requirements_count' => $formRequirements->count()
            ]);

            // Update or create student record
            $student = Student::where('user_id', session('user_id'))->first();
            if ($student) {
                // Update existing student record
                $student->update($studentData);
                Log::info('Updated existing student record', ['student_id' => $student->student_id]);
            } else {
                // Create new student record
                // Generate a unique student_id if not provided
                if (!isset($studentData['student_id'])) {
                    $studentData['student_id'] = 'STU' . str_pad(Student::count() + 1, 6, '0', STR_PAD_LEFT);
                }
                
                $student = Student::create($studentData);
                Log::info('Created new student record', ['student_id' => $student->student_id]);
            }

            // Update registrations table ONLY if record exists
            $registration = \App\Models\Registration::where('user_id', session('user_id'))->first();
            if ($registration) {
                $registrationData = [
                    'firstname' => $validated['user_firstname'],
                    'lastname' => $validated['user_lastname'],
                    'middlename' => $validated['middlename'] ?? '',
                    'street_address' => $validated['street_address'] ?? '',
                    'state_province' => $validated['state_province'] ?? '',
                    'city' => $validated['city'] ?? '',
                    'zipcode' => $validated['zipcode'] ?? '',
                    'contact_number' => $validated['contact_number'] ?? '',
                ];

                // Add dynamic fields and file uploads
                foreach ($formRequirements as $requirement) {
                    if ($requirement->field_type === 'section') continue;
                    
                    $fieldName = $requirement->field_name;
                    
                    // Ensure column exists in registrations table
                    if (!FormRequirement::columnExists($fieldName, 'registrations')) {
                        Log::warning("Column '{$fieldName}' doesn't exist in registrations table, creating it", [
                            'field_name' => $fieldName,
                            'field_type' => $requirement->field_type
                        ]);
                        
                        FormRequirement::createDatabaseColumn($fieldName, $requirement->field_type);
                    }
                    
                    // Add field data
                    if (isset($fileUploads[$fieldName])) {
                        $registrationData[$fieldName] = $fileUploads[$fieldName];
                    } elseif (isset($validated[$fieldName])) {
                        $registrationData[$fieldName] = $validated[$fieldName];
                    }
                }

                $registration->update($registrationData);
                Log::info('Updated registrations table', ['registration_id' => $registration->registration_id]);
            }

            // Update session data
            session([
                'user_firstname' => $validated['user_firstname'],
                'user_lastname' => $validated['user_lastname'],
                'user_name' => $validated['user_firstname'] . ' ' . $validated['user_lastname'],
                'user_middlename' => $validated['middlename'] ?? '',
                'user_address' => $validated['street_address'] ?? '',
                'user_state' => $validated['state_province'] ?? '',
                'user_city' => $validated['city'] ?? '',
                'user_zip' => $validated['zipcode'] ?? '',
                'user_contact' => $validated['contact_number'] ?? '',
            ]);

            return redirect()->back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating settings', [
                'user_id' => session('user_id'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()->with('error', 'Error updating settings. Please try again.');
        }
    }

    public function changePassword(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

        // Validate reCAPTCHA if enabled
        if (env('RECAPTCHA_SECRET_KEY')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$this->validateRecaptcha($recaptchaResponse)) {
                return redirect()->back()->with('error', 'Please complete the reCAPTCHA verification.');
            }
        }

        // Validate input
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // In a real application, you would verify the current password against the database
            // For now, we'll assume the verification passes
            
            // Generate a hash for the new password (for database storage)
            $hashedPassword = Hash::make($request->new_password);
            
            // Here you would update the password in the database
            // $student = Student::find(session('user_id'));
            // $student->password = $hashedPassword;
            // $student->save();

            return redirect()->back()->with('success', 'Password changed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error changing password. Please try again.');
        }
    }

    public function resetPassword(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

        // Validate reCAPTCHA if enabled
        if (env('RECAPTCHA_SECRET_KEY')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$this->validateRecaptcha($recaptchaResponse)) {
                return redirect()->back()->with('error', 'Please complete the reCAPTCHA verification.');
            }
        }

        try {
            // Generate a new random password
            $newPassword = Str::random(12);
            $hashedPassword = Hash::make($newPassword);
            
            // Here you would update the password in the database
            // $student = Student::find(session('user_id'));
            // $student->password = $hashedPassword;
            // $student->save();

            // Send email with new password
            $this->sendPasswordResetEmail(session('user_email'), $newPassword);

            return redirect()->back()->with('success', 'New password sent to your email!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error resetting password. Please try again.');
        }
    }

    public function sendOTP(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        $newEmail = $request->email;
        $currentEmail = session('user_email');

        try {
            // Check if it's the same email as current
            if ($newEmail === $currentEmail) {
                return response()->json(['success' => false, 'message' => 'This is your current email address.']);
            }

            // Check if email exists in any table
            $emailExists = false;
            
            // Check users table
            if (\App\Models\User::where('email', $newEmail)->exists()) {
                $emailExists = true;
            }
            
            // Check students table if email column exists
            if (!$emailExists && Schema::hasColumn('students', 'email')) {
                if (\App\Models\Student::where('email', $newEmail)->exists()) {
                    $emailExists = true;
                }
            }
            
            // Check registrations table if email column exists
            if (!$emailExists && Schema::hasColumn('registrations', 'email')) {
                if (\App\Models\Registration::where('email', $newEmail)->exists()) {
                    $emailExists = true;
                }
            }

            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'This email address is already in use.']);
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in session with expiration
            session([
                'email_change_otp' => $otp,
                'email_change_new_email' => $newEmail,
                'email_change_otp_expires' => now()->addMinutes(10)
            ]);

            // Send OTP email
            $this->sendOTPEmail($newEmail, $otp);

            Log::info('OTP sent for email change', [
                'user_id' => session('user_id'),
                'old_email' => $currentEmail,
                'new_email' => $newEmail
            ]);

            return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending OTP for email change', [
                'user_id' => session('user_id'),
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Error sending OTP']);
        }
    }

    public function verifyEmailOTP(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'new_email' => 'required|email'
        ]);

        try {
            // Check if OTP is valid and not expired
            $storedOTP = session('email_change_otp');
            $storedEmail = session('email_change_new_email');
            $expiry = session('email_change_otp_expires');

            if (!$storedOTP || !$expiry || now()->greaterThan($expiry)) {
                return response()->json(['success' => false, 'message' => 'OTP has expired']);
            }

            if ($storedOTP != $request->otp || $storedEmail != $request->new_email) {
                return response()->json(['success' => false, 'message' => 'Invalid OTP']);
            }

            // Update user email in database
            $user = \App\Models\User::find(session('user_id'));
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }

            $user->email = $request->new_email;
            $user->save();

            // Update session
            session(['user_email' => $request->new_email]);
            
            // Clear OTP data
            session()->forget(['email_change_otp', 'email_change_new_email', 'email_change_otp_expires']);

            return response()->json(['success' => true, 'message' => 'Email updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error verifying OTP']);
        }
    }

    public function enrollmentPage(Request $request)
    {
        // Determine if user is logged in for blade template
        $isUserLoggedIn = session('user_id') || (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']));

        // Initialize required variables for the view
        $enrollmentType = $request->get('type', 'full');
        $programs = \App\Models\Program::where('is_active', true)->get();
        $packages = \App\Models\Package::where('package_type', 'full')->get();
        
        // Auto-generate default package if none exist
        if ($packages->isEmpty()) {
            $defaultPackage = \App\Models\Package::create([
                'package_name' => 'Standard Full Program',
                'description' => 'Complete full program package with all courses included',
                'amount' => 0.00,
                'package_type' => 'full',
                'created_by_admin_id' => 1
            ]);
            $packages = collect([$defaultPackage]);
            \Log::info('Auto-generated default full package', ['package_id' => $defaultPackage->package_id]);
        }
        $formRequirements = \App\Models\FormRequirement::active()->get();
        $educationLevels = ['High School', 'College', 'Graduate', 'Postgraduate'];
        
        // Get student data if user is logged in
        $student = null;
        if ($isUserLoggedIn) {
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        }
        
        // Create default objects for plans
        $fullPlan = (object) ['name' => 'Full Program Plan', 'description' => 'Complete program enrollment'];
        $modularPlan = (object) ['name' => 'Modular Plan', 'description' => 'Subject-by-subject enrollment'];

        return view('registration.Full_enrollment', compact('enrollmentType', 'programs', 'packages', 'student', 'formRequirements', 'fullPlan', 'modularPlan', 'educationLevels', 'isUserLoggedIn'));
    }

    private function validateRecaptcha($response)
    {
        $secretKey = env('RECAPTCHA_SECRET_KEY');
        if (!$secretKey) {
            return true; // Skip validation if not configured
        }

        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
        $postData = [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => request()->ip()
        ];

        $ch = curl_init($verifyURL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response);
        return isset($responseData->success) && $responseData->success === true;
    }

    private function sendPasswordResetEmail($email, $newPassword)
    {
        // In a real application, you would use Laravel's Mail facade
        // Mail::to($email)->send(new PasswordResetMail($newPassword));
        
        // For now, we'll just log it or use a simple mail function
        error_log("New password for {$email}: {$newPassword}");
    }

    private function sendOTPEmail($email, $otp)
    {
        // In a real application, you would use Laravel's Mail facade
        // Mail::to($email)->send(new OTPMail($otp));
        
        // For now, we'll just log it
        error_log("OTP for {$email}: {$otp}");
    }

    /**
     * Get rejection details for a specific enrollment
     */
    public function getRejectionDetails($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // First, check if the enrollment exists with basic fields
            $enrollment = \App\Models\Enrollment::where('enrollment_id', $id)
                ->where('user_id', $userId)
                ->first();

            // If enrollment not found, check Registration table
            if (!$enrollment) {
                $registration = \App\Models\Registration::where('registration_id', $id)
                    ->where('user_id', $userId)
                    ->where('status', 'rejected')
                    ->first();
                if ($registration) {
                    $data = [
                        'registration_id' => $registration->getAttribute('registration_id'),
                        'program_name' => $registration->getAttribute('program_name') ?? 'Unknown Program',
                        'package_name' => $registration->getAttribute('package_name') ?? 'Unknown Package',
                        'learning_mode' => $registration->getAttribute('learning_mode') ?? 'synchronous',
                        'rejected_fields' => $registration->getAttribute('rejected_fields') ?? [],
                        'rejection_reason' => $registration->getAttribute('rejection_reason') ?? 'No reason provided',
                        'rejected_by_name' => 'Administrator',
                        'rejected_at' => $registration->getAttribute('rejected_at') ?? $registration->getAttribute('updated_at'),
                        'status' => $registration->getAttribute('status')
                    ];
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ]);
                }
            }

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment or registration not found'
                ], 404);
            }

            // Check if enrollment is actually rejected using either field name
            $enrollmentStatus = $enrollment->getAttribute('enrollment_status') ?? $enrollment->getAttribute('status') ?? 'pending';
            
            // For debugging purposes, also check if this is a recent enrollment that might be rejected but not properly marked
            if (!in_array($enrollmentStatus, ['rejected', 'rejected_registration'])) {
                // Check if there's a related registration that's rejected
                $relatedRegistration = null;
                $registrationId = $enrollment->getAttribute('registration_id');
                if ($registrationId) {
                    $relatedRegistration = \App\Models\Registration::where('registration_id', $registrationId)
                        ->where('status', 'rejected')
                        ->first();
                }
                
                if ($relatedRegistration) {
                    // Return registration data if the related registration is rejected
                    $data = [
                        'enrollment_id' => $enrollment->getAttribute('enrollment_id'),
                        'program_name' => $relatedRegistration->getAttribute('program_name') ?? 'Unknown Program',
                        'package_name' => $relatedRegistration->getAttribute('package_name') ?? 'Unknown Package',
                        'learning_mode' => $relatedRegistration->getAttribute('learning_mode') ?? 'synchronous',
                        'rejected_fields' => $relatedRegistration->getAttribute('rejected_fields') ?? [],
                        'rejection_reason' => $relatedRegistration->getAttribute('rejection_reason') ?? 'No reason provided',
                        'rejected_by_name' => 'Administrator',
                        'rejected_at' => $relatedRegistration->getAttribute('rejected_at') ?? $relatedRegistration->getAttribute('updated_at'),
                        'status' => 'rejected',
                        'source' => 'related_registration'
                    ];
                    return response()->json([
                        'success' => true,
                        'data' => $data
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'This enrollment is not rejected (status: ' . $enrollmentStatus . ')',
                    'debug' => [
                        'enrollment_status' => $enrollmentStatus,
                        'has_related_registration' => !is_null($registrationId),
                        'related_registration_id' => $registrationId
                    ]
                ], 404);
            }

            // Get additional details
            $program = \App\Models\Program::find($enrollment->getAttribute('program_id'));
            $package = \App\Models\Package::find($enrollment->getAttribute('package_id'));
            $rejectedBy = null;
            
            $rejectedById = $enrollment->getAttribute('rejected_by');
            if ($rejectedById) {
                $rejectedBy = \App\Models\Admin::find($rejectedById);
            }

            $data = [
                'enrollment_id' => $enrollment->getAttribute('enrollment_id'),
                'program_name' => $program ? $program->getAttribute('program_name') : 'Unknown Program',
                'package_name' => $package ? $package->getAttribute('package_name') : 'Unknown Package',
                'learning_mode' => $enrollment->getAttribute('learning_mode') ?? 'synchronous',
                'rejected_fields' => $enrollment->getAttribute('rejected_fields') ?? [],
                'rejection_reason' => $enrollment->getAttribute('rejection_reason') ?? 'No reason provided',
                'rejected_by_name' => $rejectedBy ? ($rejectedBy->getAttribute('admin_firstname') . ' ' . $rejectedBy->getAttribute('admin_lastname')) : 'Administrator',
                'rejected_at' => $enrollment->getAttribute('rejected_at') ?? $enrollment->getAttribute('updated_at'),
                'status' => $enrollmentStatus,
                'source' => 'enrollment'
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting rejection details: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Get edit form for rejected registration
     */
    public function getEditForm($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Find the enrollment
            $enrollment = \App\Models\Enrollment::where('enrollment_id', $id)
                ->where('user_id', $userId)
                ->where('status', 'rejected')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejected enrollment not found'
                ], 404);
            }

            // Get user details
            $user = \App\Models\User::find($userId);
            
            // Get form requirements based on education level
            $formRequirements = \App\Models\FormRequirement::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            // Get rejected fields
            $rejectedFields = [];
            $rejectedFieldsData = $enrollment->getAttribute('rejected_fields');
            if ($rejectedFieldsData) {
                try {
                    // If it's already an array (from model casting), use it directly
                    if (is_array($rejectedFieldsData)) {
                        $rejectedFields = $rejectedFieldsData;
                    } else {
                        // If it's a string, decode it
                        $rejectedFields = json_decode($rejectedFieldsData, true) ?: [];
                    }
                } catch (\Exception $e) {
                    $rejectedFields = [];
                }
            }

            // Generate the form HTML
            $html = view('student.components.edit-registration-form', [
                'enrollment' => $enrollment,
                'user' => $user,
                'formRequirements' => $formRequirements,
                'rejectedFields' => $rejectedFields
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting edit form: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get rejected registration data for resubmission
     */
    public function getRejectedRegistration($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Find the enrollment
            $enrollment = \App\Models\Enrollment::where('enrollment_id', $id)
                ->where('user_id', $userId)
                ->where('enrollment_status', 'rejected')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejected enrollment not found'
                ], 404);
            }

            // Get rejected fields
            $rejectedFields = [];
            $rejectedFieldsData = $enrollment->getAttribute('rejected_fields');
            if ($rejectedFieldsData) {
                try {
                    // If it's already an array (from model casting), use it directly
                    if (is_array($rejectedFieldsData)) {
                        $rejectedFields = $rejectedFieldsData;
                    } else {
                        // If it's a string, decode it
                        $rejectedFields = json_decode($rejectedFieldsData, true) ?: [];
                    }
                } catch (\Exception $e) {
                    $rejectedFields = [];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'enrollment' => $enrollment,
                    'rejected_fields' => $rejectedFields
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting rejected registration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Resubmit a rejected registration
     */
    public function resubmitRegistration(Request $request, $id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Find the enrollment
            $enrollment = \App\Models\Enrollment::where('enrollment_id', $id)
                ->where('user_id', $userId)
                ->where('status', 'rejected')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejected enrollment not found'
                ], 404);
            }

            // Validate the request based on form requirements
            $rules = [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'contact_number' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'education_level' => 'required|string'
            ];

            // Add file validation rules for uploads
            $fileFields = $request->allFiles();
            foreach ($fileFields as $fieldName => $file) {
                $rules[$fieldName] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'; // 5MB max
            }

            $validated = $request->validate($rules);

            // Update the enrollment data
            $updateData = [
                'status' => 'resubmitted',
                'rejected_fields' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'resubmitted_at' => now(),
                'updated_at' => now()
            ];

            // Update basic fields
            foreach (['firstname', 'lastname', 'middlename', 'email', 'contact_number', 'address', 'education_level'] as $field) {
                if (isset($validated[$field])) {
                    $updateData[$field] = $validated[$field];
                }
            }

            // Handle file uploads
            $uploadedFiles = [];
            foreach ($fileFields as $fieldName => $file) {
                if ($file && $file->isValid()) {
                    // Delete old file if exists
                    $oldFilePath = $enrollment->{$fieldName};
                    if ($oldFilePath && Storage::exists($oldFilePath)) {
                        Storage::delete($oldFilePath);
                    }

                    // Store new file
                    $path = $file->store('enrollments/' . $id, 'public');
                    $updateData[$fieldName] = $path;
                    $uploadedFiles[$fieldName] = $path;
                }
            }

            // Update the enrollment
            $enrollment->update($updateData);

            Log::info('Registration resubmitted', [
                'enrollment_id' => $id,
                'user_id' => $userId,
                'uploaded_files' => $uploadedFiles
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration resubmitted successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error resubmitting registration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a rejected registration
     */
    public function deleteRegistration($id)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Find the enrollment
            $enrollment = \App\Models\Enrollment::where('enrollment_id', $id)
                ->where('user_id', $userId)
                ->where('status', 'rejected')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rejected enrollment not found'
                ], 404);
            }

            // Delete associated files
            $fileFields = [
                'tor', 'psa_birth_certificate', 'good_moral_certificate', 
                'certificate', 'transcript', 'diploma', 'photo', 
                'marriage_certificate', 'profile_photo'
            ];

            foreach ($fileFields as $field) {
                $filePath = $enrollment->{$field};
                if ($filePath && Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }

            // Delete the enrollment
            $enrollment->delete();

            Log::info('Rejected registration deleted', [
                'enrollment_id' => $id,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting registration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get current sidebar customization settings for the student
     */
    public function getSidebarSettings()
    {
        try {
            // Check authentication
            if (!session('logged_in') || !session('user_id') || session('user_role') !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $userId = session('user_id');
            
            // Get user-specific sidebar settings
            $settings = \App\Models\UiSetting::where('section', 'student_sidebar_' . $userId)
                ->pluck('setting_value', 'setting_key');
            
            // If no user-specific settings, get default settings
            if ($settings->isEmpty()) {
                $settings = \App\Models\UiSetting::where('section', 'student_sidebar')
                    ->pluck('setting_value', 'setting_key');
            }
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting sidebar settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading sidebar settings'
            ], 500);
        }
    }

    /**
     * Save sidebar customization settings for the student
     */
    public function saveSidebarSettings(Request $request)
    {
        try {
            // Check authentication
            if (!session('logged_in') || !session('user_id') || session('user_role') !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $userId = session('user_id');
            $section = 'student_sidebar_' . $userId;
            
            // Validate color inputs
            $validated = $request->validate([
                'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'text_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'hover_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            // Save each setting
            foreach ($validated as $key => $value) {
                \App\Models\UiSetting::updateOrCreate(
                    ['section' => $section, 'setting_key' => $key],
                    ['setting_value' => $value, 'setting_type' => 'color']
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Sidebar settings saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid color format. Please use valid hex colors.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saving sidebar settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving sidebar settings'
            ], 500);
        }
    }

    /**
     * Reset sidebar customization settings to default for the student
     */
    public function resetSidebarSettings()
    {
        try {
            // Check authentication
            if (!session('logged_in') || !session('user_id') || session('user_role') !== 'student') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $userId = session('user_id');
            $section = 'student_sidebar_' . $userId;
            
            // Delete user-specific settings (will fall back to defaults)
            \App\Models\UiSetting::where('section', $section)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sidebar settings reset to default'
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting sidebar settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error resetting sidebar settings'
            ], 500);
        }
    }
    
    // ======================== PREVIEW METHODS FOR TENANT SYSTEM ========================
    
    /**
     * Preview settings page for tenant customization
     */
    public function previewSettings($tenant)
    {
        // Set up tenant context
        $tenantModel = \App\Models\Tenant::where('slug', $tenant)->firstOrFail();
        $tenantService = app(\App\Services\TenantService::class);
        
        try {
            $tenantService->switchToTenant($tenantModel);
            
            // Load tenant settings
            $this->loadTenantSettings($tenantModel);
            
            // Set preview mode session temporarily
            session(['preview_mode' => true, 'user_id' => 'preview-user', 'user_role' => 'student', 'user_name' => 'Preview Student', 'logged_in' => true]);
            
            // Create mock student data
            $user = (object) [
                'user_id' => 'preview-user',
                'user_firstname' => 'Preview',
                'user_lastname' => 'Student',
                'role' => 'student',
                'email' => 'preview@example.com'
            ];
            
            // Create mock student model
            $student = (object) [
                'student_id' => 'PREVIEW-001',
                'user_id' => 'preview-user',
                'student_firstname' => 'Preview',
                'student_lastname' => 'Student',
                'school' => 'Sample University',
                'contact_number' => '123-456-7890',
                'emergency_contact' => '098-765-4321'
            ];
            
            // Get form requirements for display
            $formRequirements = \App\Models\FormRequirement::where('is_active', true)->get();
            
            return view('student.student-settings.settings', compact('user', 'student', 'formRequirements'));
            
        } finally {
            $tenantService->switchToMain();
        }
    }
    
    /**
     * Load tenant-specific settings for preview
     */
    private function loadTenantSettings($tenant)
    {
        try {
            $settings = [
                'navbar' => \App\Models\Setting::getGroup('navbar')->toArray(),
                'student_sidebar' => \App\Models\Setting::getGroup('student_sidebar')->toArray(),
            ];
            
            // Share settings with views
            view()->share('settings', $settings);
            view()->share('navbar', $settings['navbar'] ?? []);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to load tenant settings in preview', [
                'tenant' => $tenant->slug,
                'error' => $e->getMessage()
            ]);
        }
    }
}
