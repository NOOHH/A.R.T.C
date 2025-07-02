<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Module;
use App\Models\Program;

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
        
        // Get student's enrolled programs/courses
        $student = Student::where('user_id', session('user_id'))->first();
        $courses = [];
        
        if ($student && $student->program_id) {
            $program = Program::find($student->program_id);
            if ($program) {
                $moduleCount = Module::where('program_id', $program->program_id)->count();
                $courses[] = [
                    'id' => $program->program_id,
                    'name' => $program->program_name,
                    'description' => $program->program_description ?? 'Program description',
                    'progress' => 0, // You can calculate this based on completed modules
                    'status' => 'enrolled',
                    'module_count' => $moduleCount
                ];
            }
        }
        
        // If no courses found, use dummy data
        if (empty($courses)) {
            $courses = [
                [
                    'id' => 1,
                    'name' => 'No Programs Enrolled',
                    'description' => 'Please contact administration for enrollment.',
                    'progress' => 0,
                    'status' => 'not_enrolled',
                    'module_count' => 0
                ]
            ];
        }

        return view('student.student-dasboard.student-dashboard', compact('user', 'courses'));
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
            // If program not found, redirect back to dashboard
            return redirect()->route('student.dashboard')->with('error', 'Course not found.');
        }
        
        // Check if student is enrolled in this program
        if ($student && $student->program_id != $courseId) {
            return redirect()->route('student.dashboard')->with('error', 'You are not enrolled in this course.');
        }
        
        // Get all modules for this program, ordered by creation date
        $modules = Module::where('program_id', $courseId)
                        ->orderBy('created_at', 'asc')
                        ->get();
        
        // Format modules for the view
        $formattedModules = [];
        foreach ($modules as $index => $module) {
            $formattedModules[] = [
                'id' => $module->modules_id,
                'name' => $module->module_name,
                'description' => $module->module_description ?? 'No description available',
                'status' => $index === 0 ? 'available' : 'locked', // First module is available, others locked for now
                'progress' => $index === 0 ? 0 : 0, // You can implement progress tracking later
                'attachment' => $module->attachment,
                'attachment_url' => $module->attachment ? asset('storage/' . $module->attachment) : null,
                'order' => $index + 1
            ];
        }
        
        // Calculate overall progress (you can implement this based on your requirements)
        $totalModules = count($formattedModules);
        $completedModules = 0; // Implement completion tracking later
        $progressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
        
        $course = [
            'id' => $courseId,
            'name' => $program->program_name,
            'description' => $program->program_description ?? 'Program description',
            'progress' => $progressPercentage,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'modules' => $formattedModules
        ];

        return view('student.student-courses.student-course', compact('user', 'course'));
    }
}
