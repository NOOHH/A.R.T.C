<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\OcrService;
// use App\Models\Student; // Uncomment if you have a Student model

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

        // Create a student object-like array to match the view expectations
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
            'good_moral' => null,
            'PSA' => null,
            'Course_Cert' => null,
            'TOR' => null,
            'Cert_of_Grad' => null,
            'photo_2x2' => null,
            'Undergraduate' => false,
            'Graduate' => false,
            'program_name' => session('user_program', '')
        ];

        return view('student.settings', compact('userData', 'student'));
    }

    public function updateSettings(Request $request)
    {
        // Check authentication
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Validate the input
        $validated = $request->validate([
            'user_firstname' => 'required|string|max:255',
            'user_lastname' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
        ]);

        // Here you would typically update the database
        // For now, just update the session
        session([
            'user_firstname' => $validated['user_firstname'],
            'user_lastname' => $validated['user_lastname'],
            'user_email' => $validated['user_email'],
            'user_name' => $validated['user_firstname'] . ' ' . $validated['user_lastname']
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}