<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\Deadline;
use App\Models\Announcement;

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
                    // Get module count for this program
                    $moduleCount = \App\Models\Module::where('program_id', $enrollment->program->program_id)->count();
                    
                    // Get completed modules count (you can implement this based on your completion tracking)
                    $completedCount = 0; // Implement based on your module completion logic
                    
                    // Determine button text and action based on status
                    $buttonText = 'Continue Learning';
                    $buttonClass = 'resume-btn';
                    $buttonAction = route('student.course', ['courseId' => $enrollment->program->program_id]);
                    
                    if ($enrollment->enrollment_status === 'pending') {
                        $buttonText = 'Pending Verification';
                        $buttonClass = 'resume-btn pending';
                        $buttonAction = '#';
                    } elseif ($enrollment->enrollment_status === 'approved' && $enrollment->payment_status !== 'paid') {
                        $buttonText = 'Payment Required';
                        $buttonClass = 'resume-btn payment-required';
                        $buttonAction = route('student.course', ['courseId' => $enrollment->program->program_id]);
                    } elseif ($enrollment->enrollment_status === 'rejected') {
                        $buttonText = 'Enrollment Rejected';
                        $buttonClass = 'resume-btn rejected';
                        $buttonAction = '#';
                    }
                    
                    $courses[] = [
                        'id' => $enrollment->program->program_id,
                        'name' => $enrollment->program->program_name,
                        'description' => $enrollment->program->program_description ?? 'No description available.',
                        'progress' => $moduleCount > 0 ? round(($completedCount / $moduleCount) * 100) : 0,
                        'status' => 'in_progress',
                        'learning_mode' => $enrollment->learning_mode ?? 'Synchronous',
                        'enrollment_type' => $enrollment->enrollment_type,
                        'package_name' => $enrollment->package->package_name ?? 'Unknown Package',
                        'total_modules' => $moduleCount,
                        'completed_modules' => $completedCount,
                        'enrollment_status' => $enrollment->enrollment_status,
                        'payment_status' => $enrollment->payment_status,
                        'button_text' => $buttonText,
                        'button_class' => $buttonClass,
                        'button_action' => $buttonAction,
                    ];
                }
            }
        }
        
        // If no enrollments found, show empty state
        if (empty($courses)) {
            $courses = [];
        }

        // Get student deadlines
        $deadlines = [];
        $announcements = [];
        
        if ($student) {
            // Get deadlines for this student from all enrolled programs
            $enrolledProgramIds = $enrollments->pluck('program_id')->toArray();
            
            $deadlines = \App\Models\Deadline::where('student_id', $student->student_id)
                ->orWhereIn('program_id', $enrolledProgramIds)
                ->where('due_date', '>=', now())
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get();
            
            // Get announcements for this student from all enrolled programs
            $announcements = \App\Models\Announcement::whereIn('program_id', $enrolledProgramIds)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        return view('student.student-dashboard.student-dashboard', compact('user', 'courses', 'deadlines', 'announcements'));
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
        $enrollment = null;
        $isEnrolled = false;
        if ($student) {
            // Check if student has any enrollment for this program
            $enrollment = $student->enrollments()->where('program_id', $courseId)->first();
            $isEnrolled = (bool) $enrollment;
        }
        
        if ($student && !$isEnrolled) {
            return redirect()->route('student.dashboard')->with('error', 'You are not enrolled in this course.');
        }

        // Check payment status
        $paymentStatus = $enrollment ? $enrollment->payment_status : 'unpaid';
        $enrollmentStatus = $enrollment ? $enrollment->enrollment_status : 'pending';
        
        // If not paid or not approved, show paywall
        if ($paymentStatus !== 'paid' || $enrollmentStatus !== 'approved') {
            return view('student.paywall', compact(
                'user', 
                'program', 
                'enrollment', 
                'paymentStatus', 
                'enrollmentStatus',
                'courseId'
            ));
        }
        
        // Continue with normal course view if paid and approved
        // Get all modules for this program, ordered by creation date
        $modules = Module::where('program_id', $courseId)
                        ->orderBy('created_at', 'asc')
                        ->get();
        
        // Format modules for the view
        $formattedModules = [];
        foreach ($modules as $index => $module) {
            $isLocked = $index > 0; // First module is available, others locked for now
            $isCompleted = false; // You can implement completion tracking later
            
            $formattedModules[] = [
                'id' => $module->modules_id,
                'name' => $module->module_name,
                'title' => $module->module_name, // Add title key that template expects
                'description' => $module->module_description ?? 'No description available',
                'status' => $index === 0 ? 'available' : 'locked',
                'progress' => $index === 0 ? 0 : 0,
                'attachment' => $module->attachment,
                'attachment_url' => $module->attachment ? asset('storage/' . $module->attachment) : null,
                'order' => $index + 1,
                'type' => 'module', // Add type key that template expects
                'is_locked' => $isLocked,
                'is_completed' => $isCompleted,
                'content_data' => [] // Add empty content_data for template compatibility
            ];
        }
        
        // Calculate overall progress (you can implement this based on your requirements)
        $totalModules = count($formattedModules);
        $completedModules = 0; // Implement completion tracking later
        $progressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
        
        // Group modules by type for the view (if needed)
        $modulesByType = [
            'module' => [],
            'assignment' => [],
            'quiz' => [],
            'test' => [],
            'link' => []
        ];
        
        // You can categorize modules here if you have a module type field
        foreach ($formattedModules as $module) {
            $modulesByType['module'][] = $module; // Default to 'module' type
        }
        
        $course = [
            'id' => $courseId,
            'name' => $program->program_name,
            'description' => $program->program_description ?? 'Program description',
            'progress' => $progressPercentage,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'modules' => $formattedModules
        ];

        // Variables for the view
        $progress = $progressPercentage;

        return view('student.student-courses.student-course', compact('user', 'course', 'program', 'progress', 'totalModules', 'completedModules', 'modulesByType'));
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
            'education' => 'nullable|in:Undergraduate,Graduate',
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
    
    public function module($moduleId)
    {
        // Get user data from session
        $user = (object) [
            'user_id' => session('user_id'),
            'user_firstname' => explode(' ', session('user_name'))[0] ?? '',
            'user_lastname' => explode(' ', session('user_name'))[1] ?? '',
            'role' => session('user_role')
        ];

        // Get the module data
        $module = Module::find($moduleId);
        
        if (!$module) {
            return redirect()->route('student.dashboard')->with('error', 'Module not found.');
        }
        
        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        if ($student) {
            // Check if student is enrolled in the program that contains this module
            $enrollment = $student->enrollments()->where('program_id', $module->program_id)->first();
            
            if (!$enrollment) {
                return redirect()->route('student.dashboard')->with('error', 'You are not enrolled in this course.');
            }
        }
        
        // Get the program for context
        $program = Program::find($module->program_id);
        
        // For now, just return a simple view with module details
        // You can expand this to show module content, videos, assignments, etc.
        return view('student.student-modules.student-module', compact('user', 'module', 'program'));
    }
}
