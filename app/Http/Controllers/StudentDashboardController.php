<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Support\Facades\Log;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated students can access these methods
        $this->middleware('student.auth');
    }

    public function dashboard()
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];
        
        // Fetch the student's enrolled programs from database
        $userId = session('user_id');
        $courses = [];
        
        if ($userId) {
            // Get student record to find their program
            $student = Student::where('user_id', $userId)->first();
            
            if ($student && $student->program_id) {
                // Get the program details
                $program = Program::find($student->program_id);
                
                if ($program) {
                    $courses[] = [
                        'id' => $program->program_id,
                        'name' => $program->program_name,
                        'description' => $program->description ?? 'Program description coming soon.',
                        'progress' => 0, // You can calculate actual progress later
                        'status' => 'in_progress',
                        'package_name' => $student->package_name ?? 'N/A',
                        'start_date' => $student->Start_Date ? \Carbon\Carbon::parse($student->Start_Date)->format('M d, Y') : 'N/A'
                    ];
                }
            }
        }
        
        // If no programs found, show a message
        if (empty($courses)) {
            $courses = [
                [
                    'id' => 0,
                    'name' => 'No Programs Enrolled',
                    'description' => 'You haven\'t enrolled in any programs yet. Please complete your registration.',
                    'progress' => 0,
                    'status' => 'not_enrolled',
                    'package_name' => 'N/A',
                    'start_date' => 'N/A'
                ]
            ];
        }

        return view('student.student-dashboard', compact('user', 'courses'));
    }

    public function calendar()
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        return view('student.student-calendar', compact('user'));
    }

    public function course($courseId)
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        // For now, return a course view with dummy data
        // Later you can fetch real course data from database
        $course = [
            'id' => $courseId,
            'name' => 'Calculus 1',
            'description' => 'Introduction to differential and integral calculus',
            'progress' => 15,
            'modules' => [
                [
                    'id' => 1,
                    'name' => 'Introduction to Limits',
                    'status' => 'completed',
                    'progress' => 100
                ],
                [
                    'id' => 2,
                    'name' => 'Derivatives',
                    'status' => 'in_progress',
                    'progress' => 60
                ],
                [
                    'id' => 3,
                    'name' => 'Integration',
                    'status' => 'locked',
                    'progress' => 0
                ]
            ]
        ];

        return view('student.student-course', compact('user', 'course'));
    }

    public function settings()
    {
        // Get user data from session
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to access settings.');
        }

        // Fetch student data from the database
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // Create a default student record if it doesn't exist
            $student = new Student();
            $student->user_id = $userId;
            $student->student_id = 'TEMP_' . $userId;
            $student->firstname = '';
            $student->lastname = '';
            $student->email = session('user_email', '');
        }

        return view('student.settings', compact('student'));
    }

    public function updateSettings(Request $request)
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please log in to update settings.');
        }

        // Validate the request
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'student_school' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:500',
            'state_province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'contact_number' => 'nullable|string|max:20',
            'emergency_contact_number' => 'nullable|string|max:20',
            'Start_Date' => 'nullable|date',
            'education_level' => 'nullable|in:undergraduate,graduate',
            // File uploads
            'good_moral' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'PSA' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'Course_Cert' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'TOR' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'Cert_of_Grad' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo_2x2' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        try {
            // Find or create the student record
            $student = Student::where('user_id', $userId)->first();
            
            if (!$student) {
                $student = new Student();
                $student->user_id = $userId;
                $student->student_id = 'TEMP_' . $userId . '_' . time();
                $student->email = session('user_email', '');
            }

            // Update the student information
            $student->fill($validated);
            
            // Handle education level
            if ($request->has('education_level')) {
                $student->Undergraduate = $request->education_level === 'undergraduate' ? 1 : 0;
                $student->Graduate = $request->education_level === 'graduate' ? 1 : 0;
            }
            
            // Handle file uploads
            $fileFields = ['good_moral', 'PSA', 'Course_Cert', 'TOR', 'Cert_of_Grad', 'photo_2x2'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('student_documents', $fileName, 'public');
                    $student->$field = $filePath;
                }
            }
            
            $student->save();

            return redirect()->route('student.settings')->with('success', 'Your information has been updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Student settings update failed: ' . $e->getMessage());
            return redirect()->route('student.settings')
                ->withInput()
                ->with('error', 'Failed to update your information. Please try again.');
        }
    }
}
