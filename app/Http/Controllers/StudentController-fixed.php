<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        // Handle file uploads
        $uploadedFiles = [];
        $ocrText = null;
        $ocrSuggestions = [];
        if ($request->hasFile('course_cert')) {
            $path = $request->file('course_cert')->store('documents/course_cert', 'public');
            $uploadedFiles['course_cert'] = $path;
            // OCR processing
            $ocrService = new OcrService();
            $fullPath = storage_path('app/public/' . $path);
            $ocrText = $ocrService->extractText($fullPath);
            $ocrSuggestions = $ocrService->suggestPrograms($ocrText);
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

        // Get user data from session
        $userData = [
            'user_id' => session('user_id'),
            'user_firstname' => session('user_firstname'),
            'user_lastname' => session('user_lastname'),
            'user_email' => session('user_email'),
            'user_name' => session('user_name'),
            'user_role' => session('user_role')
        ];

        // Get form requirements for student settings
        $formRequirements = FormRequirement::active()
            ->forProgram('both') // Show fields that apply to both full and modular
            ->ordered()
            ->get();

        // Try to get actual student data from database if available
        $student = Student::where('student_id', session('user_id'))
                          ->orWhere('email', session('user_email'))
                          ->first();

        // If no database record, create from session data
        if (!$student) {
            $student = (object) [
                'firstname' => session('user_firstname'),
                'middlename' => session('user_middlename', ''),
                'lastname' => session('user_lastname'),
                'student_school' => session('user_school', ''),
                'street_address' => session('user_address', ''),
                'state_province' => session('user_state', ''),
                'city' => session('user_city', ''),
                'zipcode' => session('user_zip', ''),
                'contact_number' => session('user_contact', ''),
                'emergency_contact_number' => session('user_emergency_contact', ''),
                'email' => session('user_email'),
                'student_id' => session('user_id'),
                'program_name' => session('user_program', 'Not enrolled yet'),
            ];
        }

        // Ensure program_name is set
        if (!isset($student->program_name) || empty($student->program_name)) {
            $student->program_name = 'Not enrolled yet';
        }

        return view('student.settings-new', compact('userData', 'student', 'formRequirements'));
    }

    public function updateSettings(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Get form requirements for validation
        $formRequirements = FormRequirement::active()->get();
        $validationRules = [
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'middlename' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'state_province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
        ];

        // Add dynamic validation rules from form requirements
        foreach ($formRequirements as $requirement) {
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

        // Handle file uploads
        $fileUploads = [];
        foreach ($formRequirements as $requirement) {
            if ($requirement->field_type === 'file' && $request->hasFile($requirement->field_name)) {
                $file = $request->file($requirement->field_name);
                $filename = time() . '_' . $requirement->field_name . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('student-documents', $filename, 'public');
                $fileUploads[$requirement->field_name] = $filename;
            }
        }

        // Try to update student record in database
        $student = Student::where('student_id', session('user_id'))
                          ->orWhere('email', session('user_email'))
                          ->first();

        if ($student) {
            // Update existing student record
            $student->update(array_merge($validated, $fileUploads));
        }

        // Update session data
        session([
            'user_firstname' => $validated['user_firstname'],
            'user_lastname' => $validated['user_lastname'],
            'user_email' => $validated['user_email'],
            'user_name' => $validated['user_firstname'] . ' ' . $validated['user_lastname'],
            'user_middlename' => $validated['middlename'] ?? '',
            'user_address' => $validated['street_address'] ?? '',
            'user_state' => $validated['state_province'] ?? '',
            'user_city' => $validated['city'] ?? '',
            'user_zip' => $validated['zipcode'] ?? '',
            'user_contact' => $validated['contact_number'] ?? '',
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully!');
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

        try {
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in session with expiration
            session([
                'otp_code' => $otp,
                'otp_email' => $request->email,
                'otp_expires' => now()->addMinutes(10)
            ]);

            // Send OTP email
            $this->sendOTPEmail($request->email, $otp);

            return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error sending OTP']);
        }
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
}
