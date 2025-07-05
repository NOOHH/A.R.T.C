<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated students can access these methods
        $this->middleware('student.auth');
    }

    /**
     * Display the student dashboard
     */
    public function index()
    {
        return $this->dashboard();
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
        
        // Get the student data
        $student = Student::where('user_id', session('user_id'))->first();
        
        $courses = [];
        
        if ($student) {
            // Get all enrollments for this student
            $enrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
                ->with(['program', 'package'])
                ->get();
                
            foreach ($enrollments as $enrollment) {
                if ($enrollment->program && !$enrollment->program->is_archived) {
                    $courses[] = [
                        'id' => $enrollment->program->program_id,
                        'name' => $enrollment->program->program_name,
                        'description' => $enrollment->program->program_description ?? 'No description available.',
                        'progress' => 0, // You can implement progress tracking later
                        'status' => 'in_progress',
                        'learning_mode' => $enrollment->learning_mode ?? 'Synchronous',
                        'enrollment_type' => $enrollment->enrollment_type,
                        'package_name' => $enrollment->package->package_name ?? 'Unknown Package'
                    ];
                }
            }
        }
        
        // If no enrollments found, show empty state
        if (empty($courses)) {
            $courses = [];
        }

        return view('student.student-dashboard.student-dashboard', compact('user', 'courses'));
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

        return view('student.student-calendar.student-calendar', compact('user'));
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

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        // Fetch real course data from database
        $program = Program::find($courseId);
        
        if (!$program) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Course not found.');
        }

        // Get modules for this program if it's a modular enrollment
        $modules = Module::where('program_id', $courseId)->get();

        $course = [
            'id' => $program->program_id,
            'name' => $program->program_name,
            'description' => $program->program_description ?? 'No description available.',
            'modules' => $modules,
            'progress' => 0 // You can implement progress tracking later
        ];

        return view('student.student-course.student-course', compact('user', 'course'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $userId = session('user_id');
            
            // Validate input
            $request->validate([
                'user_firstname' => 'required|string|max:255',
                'user_lastname' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:20',
                'street_address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state_province' => 'nullable|string|max:100',
                'zipcode' => 'nullable|string|max:10',
            ]);

            // Update User table
            $user = User::find($userId);
            if ($user) {
                $user->user_firstname = $request->user_firstname;
                $user->user_lastname = $request->user_lastname;
                $user->save();
                
                // Update session data
                session(['user_name' => $request->user_firstname . ' ' . $request->user_lastname]);
                session(['user_firstname' => $request->user_firstname]);
                session(['user_lastname' => $request->user_lastname]);
            }

            // Update Student table
            $student = Student::where('user_id', $userId)->first();
            if ($student) {
                $student->firstname = $request->user_firstname;
                $student->lastname = $request->user_lastname;
                $student->contact_number = $request->contact_number;
                $student->street_address = $request->street_address;
                $student->city = $request->city;
                $student->state_province = $request->state_province;
                $student->zipcode = $request->zipcode;
                $student->save();
            }

            return redirect()->route('student.dashboard')
                ->with('success', 'Your profile has been updated successfully.');

        } catch (\Exception $e) {
            Log::error('Student profile update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update your information. Please try again.');
        }
    }
}
