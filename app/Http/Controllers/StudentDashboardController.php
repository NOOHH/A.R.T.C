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
use App\Models\Package;

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
        
        // First, check for enrollments linked to this user_id (for pending registrations)
        $enrollments = collect();
        
        if (session('user_id')) {
            // Get enrollments by user_id (including pending ones)
            $userEnrollments = \App\Models\Enrollment::where('user_id', session('user_id'))
                ->with(['program', 'package', 'batch'])
                ->get();
            $enrollments = $enrollments->merge($userEnrollments);
        }
        
        if ($student) {
            // Also get enrollments by student_id (for approved ones)
            $studentEnrollments = \App\Models\Enrollment::where('student_id', $student->student_id)
                ->with(['program', 'package', 'batch'])
                ->get();
            $enrollments = $enrollments->merge($studentEnrollments);
        }
        
        // Remove duplicates based on enrollment_id
        $enrollments = $enrollments->unique('enrollment_id');
                
        foreach ($enrollments as $enrollment) {
            // Show all enrollments, not just those with active programs
            if ($enrollment->program) {
                // Get module count for this program
                $moduleCount = \App\Models\Module::where('program_id', $enrollment->program->program_id)->count();
                
                // Get completed modules count based on actual completion records
                $completedCount = 0;
                if ($student) {
                    $completedCount = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                        ->where('program_id', $enrollment->program->program_id)
                        ->count();
                }
                
                // Determine button text and action based on status
                $buttonText = 'Continue Learning';
                $buttonClass = 'resume-btn';
                $buttonAction = route('student.course', ['courseId' => $enrollment->program->program_id]);
                $showAccessModal = false;
                
                // Check if student has batch access (overrides normal status checks)
                if ($enrollment->batch_access_granted) {
                    $buttonText = 'Continue Learning';
                    $buttonClass = 'resume-btn batch-access';
                    $buttonAction = route('student.course', ['courseId' => $enrollment->program->program_id]);
                    
                    // Only show modal if status is still pending or payment is not completed
                    $showAccessModal = ($enrollment->enrollment_status !== 'approved' || $enrollment->payment_status !== 'paid');
                } else {
                    // Normal status checks
                    if ($enrollment->enrollment_status === 'pending') {
                        $buttonText = 'Pending Admin Approval';
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
                }
                
                // Get batch information
                $batchInfo = null;
                $batchDates = null;
                if ($enrollment->batch) {
                    $batchInfo = $enrollment->batch->batch_name;
                    $batchDates = [
                        'start' => $enrollment->batch->start_date ? \Carbon\Carbon::parse($enrollment->batch->start_date)->format('M d, Y') : 'TBA',
                        'end' => $enrollment->batch->end_date ? \Carbon\Carbon::parse($enrollment->batch->end_date)->format('M d, Y') : 'TBA'
                    ];
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
                    'batch_name' => $batchInfo,
                    'batch_dates' => $batchDates,
                    'enrollment_id' => $enrollment->enrollment_id,
                    'show_access_modal' => $showAccessModal,
                    'batch_access_granted' => $enrollment->batch_access_granted ?? false,
                ];
            }
        }
        
        // If no enrollments found, show empty state
        if (empty($courses)) {
            $courses = [];
        }
        
        // Log final courses array for debugging
        Log::info('Dashboard courses array', [
            'courses_count' => count($courses),
            'courses_data' => $courses
        ]);

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
        
        // Check if program is archived - if so, only allow access if student is enrolled
        if ($program->is_archived && !$isEnrolled) {
            return redirect()->route('student.dashboard')->with('error', 'This program is no longer available.');
        }

        // Check payment status and batch access
        $paymentStatus = $enrollment ? $enrollment->payment_status : 'unpaid';
        $enrollmentStatus = $enrollment ? $enrollment->enrollment_status : 'pending';
        $batchAccessGranted = $enrollment ? $enrollment->batch_access_granted : false;
        
        // If batch access is granted, allow access but show modal only if status is pending
        if ($batchAccessGranted) {
            // Only show modal if status is still pending or payment is not completed
            $showAccessModal = ($enrollmentStatus !== 'approved' || $paymentStatus !== 'paid');
        } else {
            // Normal access checks - if not paid AND not approved, show paywall
            // But allow access if payment is paid regardless of approval status
            if ($paymentStatus !== 'paid' && $enrollmentStatus !== 'approved') {
                // Get package information from database
                $package = null;
                $packageName = 'Selected Package';
                $enrollmentFee = 5000; // Default fallback
                
                if ($enrollment && $enrollment->package_id) {
                    $package = Package::find($enrollment->package_id);
                    if ($package) {
                        $packageName = $package->package_name;
                        $enrollmentFee = $package->amount;
                    }
                }
                
                return view('student.paywall', compact(
                    'user', 
                    'program', 
                    'enrollment', 
                    'paymentStatus', 
                    'enrollmentStatus',
                    'courseId',
                    'package',
                    'packageName',
                    'enrollmentFee'
                ));
            }
            $showAccessModal = false;
        }
        
        // Continue with normal course view if paid and approved
        // Get all modules for this program, ordered by order column first, then creation date
        $modules = Module::where('program_id', $courseId)
                        ->where('is_archived', false)
                        ->orderBy('order', 'asc')
                        ->orderBy('created_at', 'asc')
                        ->get();
        
        // Filter modules for modular enrollments based on selected modules
        if ($enrollment && isset($enrollment->enrollment_type) && $enrollment->enrollment_type === 'Modular') {
            // Get selected modules from registration
            $registration = \App\Models\Registration::where('user_id', session('user_id'))
                ->where('program_id', $courseId)
                ->where('enrollment_type', 'Modular')
                ->first();
                
            if ($registration && $registration->selected_modules) {
                $selectedModuleIds = json_decode($registration->selected_modules, true);
                if (is_array($selectedModuleIds) && !empty($selectedModuleIds)) {
                    // Filter modules to only show selected ones
                    $modules = $modules->filter(function($module) use ($selectedModuleIds) {
                        return in_array($module->modules_id, $selectedModuleIds);
                    });
                    Log::info('Filtered modules for modular enrollment', [
                        'original_count' => Module::where('program_id', $courseId)->count(),
                        'filtered_count' => $modules->count(),
                        'selected_modules' => $selectedModuleIds
                    ]);
                }
            }
        }
        
        // Get completed modules for this student
        $completedModuleIds = [];
        if ($student) {
            $completedModuleIds = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('program_id', $courseId)
                ->pluck('module_id')
                ->toArray();
        }
        
        // Format modules for the view
        $formattedModules = [];
        $completedCount = 0;
        $modulesByType = [];
        
        foreach ($modules as $index => $module) {
            $isCompleted = in_array($module->modules_id, $completedModuleIds);
            
            // Check if module has admin override - if so, it's always available
            $hasAdminOverride = $module->admin_override ?? false;
            
            // Original locking logic - only apply if no admin override
            $isLocked = false;
            if (!$hasAdminOverride) {
                $isLocked = $index > 0 && !$isCompleted && !in_array($modules[$index - 1]->modules_id, $completedModuleIds);
            }
            
            if ($isCompleted) {
                $completedCount++;
            }
            
            // Determine module type based on additional content
            $moduleType = 'module';
            $contentData = [];
            
            // Check if module has video content
            if ($module->video_path) {
                $moduleType = 'video';
                $contentData['video_url'] = asset('storage/' . $module->video_path);
            }
            
            // Check if module has additional content (links, etc.)
            if ($module->additional_content) {
                $additionalContent = json_decode($module->additional_content, true);
                if (is_array($additionalContent)) {
                    $contentData = array_merge($contentData, $additionalContent);
                    
                    // Set type based on additional content
                    if (!empty($additionalContent['external_url'])) {
                        $moduleType = 'link';
                    } elseif (!empty($additionalContent['assignment_title'])) {
                        $moduleType = 'assignment';
                    } elseif (!empty($additionalContent['quiz_title'])) {
                        $moduleType = 'quiz';
                    } elseif (!empty($additionalContent['test_title'])) {
                        $moduleType = 'test';
                    }
                }
            }
            
            $formattedModule = [
                'id' => $module->modules_id,
                'name' => $module->module_name,
                'title' => $module->module_name,
                'description' => $module->module_description ?? 'No description available',
                'status' => $isCompleted ? 'completed' : ($isLocked ? 'locked' : 'available'),
                'progress' => $isCompleted ? 100 : 0,
                'attachment' => $module->attachment,
                'attachment_url' => $module->attachment ? asset('storage/' . $module->attachment) : null,
                'order' => $index + 1,
                'type' => $moduleType,
                'is_locked' => $isLocked,
                'is_completed' => $isCompleted,
                'has_admin_override' => $hasAdminOverride,
                'content_data' => $contentData,
                'video_path' => $module->video_path,
                'additional_content' => $module->additional_content
            ];
            
            $formattedModules[] = $formattedModule;
            
            // Group modules by type for filtering
            if (!isset($modulesByType[$moduleType])) {
                $modulesByType[$moduleType] = [];
            }
            $modulesByType[$moduleType][] = $formattedModule;
        }
        
        // Calculate overall progress based on completed modules
        $totalModules = count($formattedModules);
        $completedModules = $completedCount;
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

        // Variables for the view
        $progress = $progressPercentage;

        return view('student.student-courses.student-course', compact(
            'user', 
            'course', 
            'program', 
            'progress', 
            'totalModules', 
            'completedModules', 
            'modulesByType',
            'showAccessModal',
            'enrollment',
            'paymentStatus',
            'enrollmentStatus'
        ));
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
        
        // Check if module is completed
        $isCompleted = false;
        if ($student) {
            $completion = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('module_id', $moduleId)
                ->first();
            $isCompleted = (bool) $completion;
        }
        
        // Parse content data properly
        $contentData = [];
        
        // First check the content_data field (new format)
        if ($module->content_data) {
            if (is_array($module->content_data)) {
                $contentData = $module->content_data;
            } else {
                $decodedData = json_decode($module->content_data, true);
                if (is_array($decodedData)) {
                    $contentData = $decodedData;
                }
            }
        }
        
        // Fallback to additional_content field (old format)
        if (empty($contentData) && $module->additional_content) {
            $additionalContent = json_decode($module->additional_content, true);
            if (is_array($additionalContent)) {
                $contentData = $additionalContent;
            }
        }
        
        // Determine module type based on content
        $moduleType = $module->content_type ?? 'module';
        
        // Override based on content data if not explicitly set
        if (!empty($contentData['external_url'])) {
            $moduleType = 'link';
        } elseif (!empty($contentData['assignment_title'])) {
            $moduleType = 'assignment';
        } elseif (!empty($contentData['quiz_title'])) {
            $moduleType = 'quiz';
        } elseif (!empty($contentData['ai_quiz_title'])) {
            $moduleType = 'ai_quiz';
        } elseif (!empty($contentData['test_title'])) {
            $moduleType = 'test';
        }
        
        // Format module data for the view
        $moduleData = [
            'id' => $module->modules_id,
            'title' => $module->module_name,
            'description' => $module->module_description,
            'type' => $moduleType,
            'attachment' => $module->attachment,
            'attachment_url' => $module->attachment ? asset('storage/' . $module->attachment) : null,
            'program_id' => $module->program_id,
            'is_completed' => $isCompleted,
            'content_data' => $contentData
        ];
        
        // Check for video content
        if ($module->video_path) {
            $moduleData['content_data']['video_url'] = asset('storage/' . $module->video_path);
        }
        
        return view('student.student-courses.student-module', compact('user', 'module', 'program', 'moduleData'));
    }
    
    /**
     * Mark a module as completed
     */
    public function completeModule(Request $request, $moduleId)
    {
        try {
            // Get the student
            $student = Student::where('user_id', session('user_id'))->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }
            
            // Get the module
            $module = Module::find($moduleId);
            
            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found.'
                ]);
            }
            
            // Check if student is enrolled in the program
            $enrollment = $student->enrollments()->where('program_id', $module->program_id)->first();
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this course.'
                ]);
            }
            
            // Check if already completed
            $existingCompletion = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('module_id', $moduleId)
                ->first();
            
            if ($existingCompletion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module is already completed.'
                ]);
            }
            
            // Create completion record
            \App\Models\ModuleCompletion::create([
                'student_id' => $student->student_id,
                'module_id' => $moduleId,
                'program_id' => $module->program_id,
                'completed_at' => now(),
                'score' => 100, // Default score for completing the module
                'time_spent' => 0, // You can track this if needed
                'submission_data' => null
            ]);
            
            // Calculate progress percentage
            $totalModules = Module::where('program_id', $module->program_id)->count();
            $completedModules = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('program_id', $module->program_id)
                ->count();
            
            $progressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;
            
            return response()->json([
                'success' => true,
                'message' => 'Module completed successfully!',
                'progress_percentage' => $progressPercentage
            ]);
            
        } catch (\Exception $e) {
            Log::error('Module completion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while completing the module.'
            ]);
        }
    }
    
    /**
     * Submit assignment
     */
    public function submitAssignment(Request $request)
    {
        try {
            $request->validate([
                'assignment_file' => 'required|file|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png|max:10240',
                'module_id' => 'required|integer',
                'comments' => 'nullable|string'
            ]);
            
            $student = Student::where('user_id', session('user_id'))->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }
            
            $moduleId = $request->input('module_id');
            $module = Module::find($moduleId);
            
            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found.'
                ]);
            }
            
            // Store the uploaded file
            $file = $request->file('assignment_file');
            $fileName = time() . '_' . $student->student_id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            
            // Create assignment submission record
            \App\Models\AssignmentSubmission::create([
                'student_id' => $student->student_id,
                'module_id' => $moduleId,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'comments' => $request->comments,
                'submitted_at' => now(),
                'status' => 'submitted'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assignment submitted successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Assignment submission error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the assignment.'
            ]);
        }
    }
    
    /**
     * Start quiz
     */
    public function startQuiz($moduleId)
    {
        $module = Module::find($moduleId);
        
        if (!$module) {
            return redirect()->back()->with('error', 'Module not found.');
        }
        
        // For now, redirect to the module page
        // In the future, you can create a dedicated quiz interface
        return redirect()->route('student.module', ['moduleId' => $moduleId]);
    }
    
    /**
     * Practice quiz
     */
    public function practiceQuiz($moduleId)
    {
        $module = Module::find($moduleId);
        
        if (!$module) {
            return redirect()->back()->with('error', 'Module not found.');
        }
        
        // For now, redirect to the module page
        // In the future, you can create a dedicated practice quiz interface
        return redirect()->route('student.module', ['moduleId' => $moduleId]);
    }
    
    /**
     * Submit quiz
     */
    public function submitQuiz(Request $request, $moduleId)
    {
        try {
            $student = Student::where('user_id', session('user_id'))->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }
            
            $module = Module::find($moduleId);
            
            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found.'
                ]);
            }
            
            // Create quiz submission record
            \App\Models\QuizSubmission::create([
                'student_id' => $student->student_id,
                'module_id' => $moduleId,
                'answers' => json_encode($request->answers ?? []),
                'score' => $request->score ?? 0,
                'total_questions' => $request->total_questions ?? 0,
                'time_taken' => $request->time_taken ?? 0,
                'submitted_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Quiz submission error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the quiz.'
            ]);
        }
    }

    /**
     * Display the paywall for students who haven't paid
     */
    public function paywall()
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
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Get the student's latest enrollment
        $enrollment = $student->enrollments()->latest()->first();
        
        if (!$enrollment) {
            return redirect()->route('student.dashboard')->with('error', 'No enrollment found.');
        }

        $program = Program::find($enrollment->program_id);
        $paymentStatus = $enrollment->payment_status;
        $enrollmentStatus = $enrollment->enrollment_status;
        $courseId = $enrollment->program_id;

        // Get package information from database
        $package = null;
        $packageName = 'Selected Package';
        $enrollmentFee = 5000; // Default fallback
        
        if ($enrollment->package_id) {
            $package = Package::find($enrollment->package_id);
            if ($package) {
                $packageName = $package->package_name;
                $enrollmentFee = $package->amount;
            }
        }

        return view('student.paywall', compact(
            'user', 
            'program', 
            'enrollment', 
            'paymentStatus', 
            'enrollmentStatus',
            'courseId',
            'package',
            'packageName',
            'enrollmentFee'
        ));
    }
    
    /**
     * Start AI-generated quiz
     */
    public function startAiQuiz($quizId)
    {
        $quiz = \App\Models\Quiz::with('questions')->find($quizId);
        
        if (!$quiz) {
            return redirect()->back()->with('error', 'Quiz not found.');
        }
        
        $student = Student::where('user_id', session('user_id'))->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }
        
        // Check if student has permission to take this quiz
        $deadline = Deadline::where('student_id', $student->student_id)
            ->where('type', 'quiz')
            ->where('reference_id', $quizId)
            ->first();
            
        if (!$deadline) {
            return redirect()->back()->with('error', 'You do not have permission to take this quiz.');
        }
        
        if ($deadline->status === 'completed') {
            return redirect()->back()->with('error', 'You have already completed this quiz.');
        }
        
        return view('student.take-quiz', compact('quiz', 'deadline'));
    }
    
    /**
     * Submit AI-generated quiz
     */
    public function submitAiQuiz(Request $request, $quizId)
    {
        try {
            $student = Student::where('user_id', session('user_id'))->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }
            
            $quiz = \App\Models\Quiz::with('questions')->find($quizId);
            
            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found.'
                ]);
            }
            
            // Check if student has permission to take this quiz
            $deadline = Deadline::where('student_id', $student->student_id)
                ->where('type', 'quiz')
                ->where('reference_id', $quizId)
                ->first();
                
            if (!$deadline) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to take this quiz.'
                ]);
            }
            
            if ($deadline->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already completed this quiz.'
                ]);
            }
            
            // Calculate score
            $answers = $request->answers ?? [];
            $score = 0;
            $totalQuestions = $quiz->questions->count();
            
            foreach ($quiz->questions as $question) {
                $questionId = $question->quiz_id; // Using quiz_id as question identifier
                $studentAnswer = $answers[$questionId] ?? null;
                
                if ($studentAnswer && $studentAnswer === $question->correct_answer) {
                    $score += $question->points;
                }
            }
            
            // Create quiz submission record
            \App\Models\QuizSubmission::create([
                'student_id' => $student->student_id,
                'quiz_id' => $quizId,
                'answers' => json_encode($answers),
                'score' => $score,
                'total_questions' => $totalQuestions,
                'time_taken' => $request->time_taken ?? 0,
                'submitted_at' => now()
            ]);
            
            // Update deadline status
            $deadline->status = 'completed';
            $deadline->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully!',
                'score' => $score,
                'total_questions' => $totalQuestions
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Quiz submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the quiz.'
            ]);
        }
    }
}
