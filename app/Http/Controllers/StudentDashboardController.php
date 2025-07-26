<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\ContentItem;
use App\Models\Deadline;
use App\Models\Announcement;
use App\Models\Package;
use App\Models\AssignmentSubmission;

class StudentDashboardController extends Controller
{
    /**
     * Ensure enrollment course records exist for modular enrollments
     * This is a helper method to fix missing enrollment_courses records
     */
    private function ensureEnrollmentCourseRecords($enrollment)
    {
        if (!$enrollment || $enrollment->enrollment_type !== 'Modular') {
            return;
        }
        
        // Check if enrollment course records already exist
        $existingCourseCount = $enrollment->enrollmentCourses()->count();
        if ($existingCourseCount > 0) {
            return; // Records already exist
        }
        
        Log::info('Creating missing enrollment course records', [
            'enrollment_id' => $enrollment->enrollment_id,
            'user_id' => $enrollment->user_id
        ]);
        
        // Get the registration to find selected courses
        $registration = \App\Models\Registration::where('user_id', $enrollment->user_id)
            ->where('program_id', $enrollment->program_id)
            ->where('enrollment_type', 'Modular')
            ->first();
        
        if (!$registration || !$registration->selected_modules) {
            Log::warning('No registration data found for enrollment course creation', [
                'enrollment_id' => $enrollment->enrollment_id
            ]);
            return;
        }
        
        $selectedModulesData = json_decode($registration->selected_modules, true);
        if (!is_array($selectedModulesData)) {
            Log::warning('Invalid selected modules data', [
                'enrollment_id' => $enrollment->enrollment_id,
                'selected_modules' => $registration->selected_modules
            ]);
            return;
        }
        
        $createdCourses = 0;
        foreach ($selectedModulesData as $moduleData) {
            $moduleId = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
            
            if (!$moduleId) continue;
            
            // If specific courses are selected for this module
            if (isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                foreach ($moduleData['selected_courses'] as $courseData) {
                    $courseId = is_array($courseData) ? ($courseData['id'] ?? $courseData['course_id'] ?? $courseData) : $courseData;
                    
                    if ($courseId) {
                        try {
                            \App\Models\EnrollmentCourse::create([
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $courseId,
                                'module_id' => $moduleId,
                                'enrollment_type' => 'course',
                                'course_price' => 0,
                                'is_active' => true
                            ]);
                            $createdCourses++;
                            Log::info('Created enrollment course record', [
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $courseId,
                                'module_id' => $moduleId
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to create enrollment course record', [
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $courseId,
                                'module_id' => $moduleId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            } else {
                // If no specific courses selected, enroll in all courses of the module
                $module = \App\Models\Module::find($moduleId);
                if ($module) {
                    $moduleCourses = \App\Models\Course::where('module_id', $moduleId)
                        ->where('is_active', true)
                        ->get();
                    
                    foreach ($moduleCourses as $course) {
                        try {
                            \App\Models\EnrollmentCourse::create([
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $course->subject_id,
                                'module_id' => $moduleId,
                                'enrollment_type' => 'course',
                                'course_price' => 0,
                                'is_active' => true
                            ]);
                            $createdCourses++;
                            Log::info('Created enrollment course record for module course', [
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $course->subject_id,
                                'module_id' => $moduleId
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to create enrollment course record for module course', [
                                'enrollment_id' => $enrollment->enrollment_id,
                                'course_id' => $course->subject_id,
                                'module_id' => $moduleId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }
        
        Log::info('Completed enrollment course record creation', [
            'enrollment_id' => $enrollment->enrollment_id,
            'created_count' => $createdCourses
        ]);
    }

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
                $courseProgress = 0;
                if ($student) {
                    // Use enhanced progress calculation for more accurate results
                    if (isset($enrollment->enrollment_type) && $enrollment->enrollment_type === 'Modular') {
                        // For modular, calculate based on enrolled courses only
                        $enrolledCourseIds = $enrollment->enrollmentCourses()
                            ->where('is_active', true)
                            ->pluck('course_id')
                            ->toArray();
                        
                        if (!empty($enrolledCourseIds)) {
                            $totalCourseContent = \App\Models\ContentItem::whereIn('course_id', $enrolledCourseIds)->count();
                            $completedCourseContent = \App\Models\ContentCompletion::where('student_id', $student->student_id)
                                ->whereIn('course_id', $enrolledCourseIds)
                                ->count();
                            
                            $courseProgress = $totalCourseContent > 0 ? round(($completedCourseContent / $totalCourseContent) * 100) : 0;
                            
                            // Get module completion count for this specific enrollment
                            $moduleIdsWithEnrolledCourses = \App\Models\Course::whereIn('subject_id', $enrolledCourseIds)
                                ->pluck('module_id')
                                ->unique()
                                ->toArray();
                            
                            $completedCount = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                                ->where('program_id', $enrollment->program->program_id)
                                ->whereIn('modules_id', $moduleIdsWithEnrolledCourses)
                                ->count();
                        }
                    } else {
                        // For full program enrollments, calculate based on all content
                        $completedCount = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                            ->where('program_id', $enrollment->program->program_id)
                            ->count();
                        
                        // Also calculate content-based progress for more granular updates
                        $allProgramCourses = \App\Models\Module::where('program_id', $enrollment->program->program_id)
                            ->with('courses')
                            ->get()
                            ->pluck('courses')
                            ->flatten()
                            ->pluck('subject_id')
                            ->toArray();
                        
                        if (!empty($allProgramCourses)) {
                            $totalProgramContent = \App\Models\ContentItem::whereIn('course_id', $allProgramCourses)->count();
                            $completedProgramContent = \App\Models\ContentCompletion::where('student_id', $student->student_id)
                                ->whereIn('course_id', $allProgramCourses)
                                ->count();
                            
                            $courseProgress = $totalProgramContent > 0 ? round(($completedProgramContent / $totalProgramContent) * 100) : 0;
                        } else {
                            $courseProgress = $moduleCount > 0 ? round(($completedCount / $moduleCount) * 100) : 0;
                        }
                    }
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
                    'progress' => $courseProgress, // Use content-based progress instead of module-based
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
        
        // Check for rejected registrations that are not yet converted to enrollments
        if (session('user_id')) {
            $rejectedRegistrations = \App\Models\Registration::where('user_id', session('user_id'))
                ->whereIn('status', ['rejected', 'resubmitted'])
                ->with(['program', 'package'])
                ->get();
                
            foreach ($rejectedRegistrations as $registration) {
                if ($registration->program) {
                    // Check if there's already an enrollment for this program
                    $existingCourseIndex = null;
                    foreach ($courses as $index => $course) {
                        if ($course['id'] == $registration->program->program_id) {
                            $existingCourseIndex = $index;
                            break;
                        }
                    }
                    
                    // Determine button text based on registration status
                    if ($registration->status === 'rejected') {
                        $buttonText = 'Registration Rejected - Click to Edit';
                        $buttonClass = 'resume-btn rejected';
                        $buttonAction = '#';
                    } elseif ($registration->status === 'resubmitted') {
                        $buttonText = 'Registration Resubmitted - Pending Review';
                        $buttonClass = 'resume-btn resubmitted';
                        $buttonAction = '#';
                    }
                    
                    $courseData = [
                        'id' => $registration->program->program_id,
                        'name' => $registration->program->program_name,
                        'description' => $registration->program->program_description ?? 'No description available.',
                        'progress' => 0, // No progress for rejected registrations
                        'status' => 'registration_rejected',
                        'learning_mode' => $registration->enrollment_type ?? 'Full',
                        'enrollment_type' => $registration->enrollment_type,
                        'package_name' => $registration->package->package_name ?? 'Unknown Package',
                        'total_modules' => 0,
                        'completed_modules' => 0,
                        'enrollment_status' => $registration->status, // This will be 'rejected' or 'resubmitted'
                        'payment_status' => 'pending',
                        'button_text' => $buttonText,
                        'button_class' => $buttonClass,
                        'button_action' => $buttonAction,
                        'batch_name' => null,
                        'batch_dates' => null,
                        'enrollment_id' => null,
                        'registration_id' => $registration->registration_id, // Add registration ID for rejected handling
                        'show_access_modal' => false,
                        'batch_access_granted' => false,
                    ];
                    
                    if ($existingCourseIndex !== null) {
                        // Replace the existing course with rejected registration data (rejected takes priority)
                        $courses[$existingCourseIndex] = $courseData;
                    } else {
                        // Add new course for rejected registration
                        $courses[] = $courseData;
                    }
                }
            }
        }
        
        // If no enrollments found, show empty state
        if (empty($courses)) {
            $courses = [];
        }

        // Prepare studentPrograms for sidebar
        $studentPrograms = array_map(function($course) {
            return [
                'program_id' => $course['id'],
                'program_name' => $course['name'],
                'package_name' => $course['package_name'],
            ];
        }, $courses);

        // Log final courses array for debugging
        Log::info('Dashboard courses array', [
            'courses_count' => count($courses),
            'courses_data' => $courses
        ]);

        // Get student deadlines
        $deadlines = [];
        $announcements = [];
        
        if ($student) {
            // Auto-update overdue deadline statuses
            $this->updateOverdueAssignmentStatuses($student);
            
            // Get deadlines for this student from all enrolled programs
            $enrolledProgramIds = $enrollments->pluck('program_id')->toArray();
            // Get all active course_ids the student is enrolled in (via enrollment_courses)
            $enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($q) use ($student) {
                $q->where('student_id', $student->student_id);
            })->where('is_active', true)->pluck('course_id')->toArray();

            // Get deadlines from deadlines table (including overdue ones)
            $deadlines = \App\Models\Deadline::where('student_id', $student->student_id)
                ->orWhereIn('program_id', $enrolledProgramIds)
                ->where(function($query) {
                    $query->where('due_date', '>=', now())
                          ->orWhere('status', 'overdue');
                })
                ->orderBy('due_date', 'asc')
                ->get();

            // Add assignment deadlines from content_items (no lesson references)
            $assignmentDeadlines = \App\Models\ContentItem::where('content_type', 'assignment')
                ->whereNotNull('due_date')
                ->whereIn('course_id', $enrolledCourseIds)
                ->get()
                ->map(function($item) use ($student) {
                    // Check if student has already submitted this assignment
                    $submission = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
                        ->where('content_id', $item->id)
                        ->first();
                    // Determine status based on submission and due date
                    $now = now();
                    if ($submission) {
                        $status = 'completed';
                        $feedback = $submission->feedback;
                        $grade = $submission->grade;
                    } elseif ($item->due_date < $now) {
                        $status = 'overdue';
                        $feedback = null;
                        $grade = null;
                    } else {
                        $status = 'pending';
                        $feedback = null;
                        $grade = null;
                    }
                    // Get course and program info for navigation and display
                    $course = \App\Models\Course::find($item->course_id);
                    $module = $course ? \App\Models\Module::find($course->module_id) : null;
                    $program = $module ? \App\Models\Program::find($module->program_id) : null;
                    return (object) [
                        'title' => $item->content_title,
                        'description' => $item->content_description ?? 'Assignment deadline',
                        'due_date' => $item->due_date,
                        'type' => 'assignment',
                        'reference_id' => $item->id,
                        'status' => $status,
                        'feedback' => $feedback,
                        'grade' => $grade,
                        'course_id' => $item->course_id,
                        'module_id' => $module ? $module->modules_id : null,
                        'course_name' => $course ? $course->subject_name : null,
                        'module_name' => $module ? $module->module_name : null,
                        'program_name' => $program ? $program->program_name : null,
                        'program_id' => $program ? $program->program_id : null,
                        'submission' => $submission
                    ];
                })
                ->filter(function($deadline) {
                    // Show upcoming deadlines and overdue assignments
                    return $deadline->due_date >= now()->subDays(7) || $deadline->status === 'overdue';
                });

            // Auto-create missing deadline entries for assignments that don't have them
            $this->createMissingAssignmentDeadlines($student, $enrolledProgramIds, $enrolledCourseIds);

            // Merge and sort deadlines by due date
            $allDeadlines = $deadlines->concat($assignmentDeadlines)
                ->sortBy('due_date')
                ->take(5)
                ->values();
            
            $deadlines = $allDeadlines;

            // Get announcements for this student using new targeting system
            $announcements = $this->getTargetedAnnouncements($student, $enrolledProgramIds);
        }

        return view('student.student-dashboard.student-dashboard', compact('user', 'courses', 'deadlines', 'announcements', 'studentPrograms'));
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

        // Get student's enrollment info
        $student = Student::where('user_id', session('user_id'))->first();
        
        $upcomingMeetings = collect();
        $todaysMeetings = collect();
        $allMeetings = collect();
        $studentPrograms = collect();
        
        if ($student) {
            // Get student's enrolled programs for sidebar
            $studentPrograms = $student->enrollments()
                ->with(['program', 'package'])
                ->whereNotNull('program_id')
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'program_id' => $enrollment->program_id,
                        'program_name' => $enrollment->program->program_name ?? 'Unknown Program',
                        'package_name' => $enrollment->package->package_name ?? 'Unknown Package',
                        'enrollment_type' => $enrollment->enrollment_type,
                        'enrollment_status' => $enrollment->enrollment_status
                    ];
                })
                ->unique('program_id')
                ->values();
            
            // Get student's enrolled batches
            $enrolledBatches = $student->enrollments()
                ->with('batch')
                ->whereNotNull('batch_id')
                ->pluck('batch_id')
                ->unique();
            
            if ($enrolledBatches->isNotEmpty()) {
                // Get meetings for enrolled batches
                $upcomingMeetings = \App\Models\ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->upcoming()
                    ->orderBy('meeting_date', 'asc')
                    ->get();
                
                $todaysMeetings = \App\Models\ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->today()
                    ->orderBy('meeting_date', 'asc')
                    ->get();
                
                // Get all meetings for calendar display (next 3 months)
                $allMeetings = \App\Models\ClassMeeting::with(['batch.program', 'professor'])
                    ->whereIn('batch_id', $enrolledBatches)
                    ->where('meeting_date', '>=', now())
                    ->where('meeting_date', '<=', now()->addMonths(3))
                    ->orderBy('meeting_date', 'asc')
                    ->get();
            }
        }

        return view('student.student-calendar.student-calendar', compact('user', 'upcomingMeetings', 'todaysMeetings', 'allMeetings', 'studentPrograms'));
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
        // ENHANCED ACCESS CONTROL: Only show modules for courses the student is actually enrolled in
        
        // Verify student has valid enrollment in the program
        $hasValidEnrollment = $enrollment && $enrollment->program_id == $courseId;
        if ($hasValidEnrollment) {
            Log::info('Student has valid program-level enrollment', [
                'program_id' => $courseId
            ]);
        }
        
        // If student doesn't have valid enrollment, redirect with error
        if (!$hasValidEnrollment) {
            Log::warning('Student attempted to access unauthorized course', [
                'user_id' => session('user_id'),
                'requested_course_id' => $courseId,
                'enrollment_id' => $enrollment ? $enrollment->enrollment_id : null,
            ]);
            
            return redirect()->route('student.dashboard')
                ->with('error', 'You do not have access to this course. Please contact your administrator if you believe this is an error.');
        }
        
        // Retrieve modules based on program-level enrollment with proper ordering
        $modules = Module::where('program_id', $courseId)
                         ->where('is_archived', false)
                         ->orderBy('module_order', 'asc')
                         ->orderBy('modules_id', 'asc')
                         ->get();

        // Filter modules for modular enrollments based on enrolled courses
        if ($enrollment && isset($enrollment->enrollment_type) && $enrollment->enrollment_type === 'Modular') {
            // Ensure enrollment course records exist
            $this->ensureEnrollmentCourseRecords($enrollment);
            
            // Get the courses the student is enrolled in
            $enrolledCourseIds = $enrollment->enrollmentCourses()
                ->where('is_active', true)
                ->pluck('course_id')
                ->toArray();
            
            if (!empty($enrolledCourseIds)) {
                // Get the module IDs that contain these courses
                $moduleIdsWithEnrolledCourses = \App\Models\Course::whereIn('subject_id', $enrolledCourseIds)
                    ->pluck('module_id')
                    ->unique()
                    ->toArray();
                
                // Filter modules to only include those that contain enrolled courses
                $modules = $modules->filter(function($module) use ($moduleIdsWithEnrolledCourses) {
                    return in_array($module->modules_id, $moduleIdsWithEnrolledCourses);
                });
                
                Log::info('Filtered modules for modular enrollment based on enrolled courses', [
                    'original_count' => Module::where('program_id', $courseId)->count(),
                    'filtered_count' => $modules->count(),
                    'enrolled_courses' => $enrolledCourseIds,
                    'modules_with_courses' => $moduleIdsWithEnrolledCourses
                ]);
            } else {
                // No enrolled courses found, try fallback to registration data
                $registration = \App\Models\Registration::where('user_id', session('user_id'))
                    ->where('program_id', $courseId)
                    ->where('enrollment_type', 'Modular')
                    ->first();
                
                if ($registration && $registration->selected_modules) {
                    $selectedModulesData = json_decode($registration->selected_modules, true);
                    
                    // Handle both old format (array of IDs) and new format (object with courses)
                    $selectedCourseIds = [];
                    if (is_array($selectedModulesData)) {
                        foreach ($selectedModulesData as $moduleData) {
                            if (is_array($moduleData) && isset($moduleData['selected_courses'])) {
                                $selectedCourseIds = array_merge($selectedCourseIds, $moduleData['selected_courses']);
                            }
                        }
                    }
                    
                    if (!empty($selectedCourseIds)) {
                        // Get the module IDs that contain these courses
                        $moduleIdsWithSelectedCourses = \App\Models\Course::whereIn('subject_id', $selectedCourseIds)
                            ->pluck('module_id')
                            ->unique()
                            ->toArray();
                        
                        // Filter modules to only include those that contain selected courses
                        $modules = $modules->filter(function($module) use ($moduleIdsWithSelectedCourses) {
                            return in_array($module->modules_id, $moduleIdsWithSelectedCourses);
                        });
                        
                        Log::info('Filtered modules using registration fallback', [
                            'selected_courses' => $selectedCourseIds,
                            'modules_with_courses' => $moduleIdsWithSelectedCourses,
                            'filtered_count' => $modules->count()
                        ]);
                    }
                }
            }
        }
        
        // Get completed modules for this student
        $completedModuleIds = [];
        if ($student) {
            $completedModuleIds = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('program_id', $courseId)
                ->pluck('modules_id')
                ->toArray();
        }
        
        // Get completed content for this student
        $completedContentIds = [];
        $completedCourseIds = [];
        if ($student) {
            $completedContentIds = \App\Models\ContentCompletion::where('student_id', $student->student_id)
                ->pluck('content_id')
                ->toArray();
            
            // Get completed courses for this student
            $completedCourseIds = \App\Models\CourseCompletion::where('student_id', $student->student_id)
                ->pluck('course_id')
                ->toArray();
        }
        
        // Convert collection to array to prevent duplicates and maintain ordering
        $modules = $modules->values()->unique('modules_id')->sortBy(function($module) {
            return [$module->order, $module->created_at];
        });
        
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

        // Prepare student programs data for sidebar component
        $studentPrograms = [];
        if ($student) {
            $enrollments = \App\Models\Enrollment::where('user_id', session('user_id'))
                ->with(['program', 'package'])
                ->where('enrollment_status', 'approved')
                ->get();
            
            foreach ($enrollments as $enrollmentData) {
                if ($enrollmentData->program) {
                    $studentPrograms[] = [
                        'program_id' => $enrollmentData->program->program_id,
                        'program_name' => $enrollmentData->program->program_name,
                        'package_name' => $enrollmentData->package ? $enrollmentData->package->package_name : 'No Package'
                    ];
                }
            }
        }

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
            'enrollmentStatus',
            'studentPrograms',
            'completedModuleIds',
            'completedContentIds',
            'completedCourseIds' // <-- add completed course IDs
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
    
    /*
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
                ->where('modules_id', $moduleId)
                ->first();
            $isCompleted = (bool) $completion;
        }
        
        // Get courses associated with this module from the database
        $coursesQuery = Course::where('module_id', $moduleId)
            ->where('is_active', true)
            ->ordered();
        
        // Apply modular enrollment filtering
        if ($student && $enrollment && $enrollment->enrollment_type === 'Modular') {
            // Ensure enrollment course records exist
            $this->ensureEnrollmentCourseRecords($enrollment);
            
            $allowedCourseIds = $enrollment->enrollmentCourses()
                ->where('is_active', true)
                ->pluck('course_id')
                ->toArray();
            
            Log::info('Module view course filtering for modular enrollment', [
                'enrollment_id' => $enrollment->enrollment_id,
                'module_id' => $moduleId,
                'allowed_course_ids' => $allowedCourseIds
            ]);
            
            if (!empty($allowedCourseIds)) {
                $coursesQuery->whereIn('subject_id', $allowedCourseIds);
            } else {
                // Fallback to registration data if no enrollment courses found
                $registration = \App\Models\Registration::where('user_id', session('user_id'))
                    ->where('program_id', $enrollment->program_id)
                    ->where('enrollment_type', 'Modular')
                    ->first();
                
                if ($registration && $registration->selected_modules) {
                    $selectedModulesData = json_decode($registration->selected_modules, true);
                    $fallbackCourseIds = [];
                    
                    if (is_array($selectedModulesData)) {
                        foreach ($selectedModulesData as $moduleData) {
                            $moduleIdFromData = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                            
                            if ($moduleIdFromData == $moduleId && isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                                foreach ($moduleData['selected_courses'] as $courseId) {
                                    $fallbackCourseIds[] = is_array($courseId) ? ($courseId['id'] ?? $courseId['course_id'] ?? $courseId) : $courseId;
                                }
                            }
                        }
                    }
                    
                    if (!empty($fallbackCourseIds)) {
                        Log::info('Using fallback course filtering in module view', [
                            'fallback_course_ids' => $fallbackCourseIds,
                            'module_id' => $moduleId
                        ]);
                        $coursesQuery->whereIn('subject_id', $fallbackCourseIds);
                    } else {
                        // No specific courses selected for this module - show none
                        $coursesQuery->where('subject_id', -1); // This will return no results
                    }
                } else {
                    // No registration data - show none
                    $coursesQuery->where('subject_id', -1); // This will return no results
                }
            }
        }
        
        $courses = $coursesQuery->get();
        
        // Format courses with their content (PDFs, assignments, etc.)
        $formattedCourses = [];
        foreach ($courses as $course) {
            // Get content items for this course
            $contentItems = ContentItem::where('course_id', $course->subject_id)
                ->where('is_active', true)
                ->orderBy('content_order', 'asc')
                ->get();
            
            $formattedCourses[] = [
                'id' => $course->subject_id,
                'name' => $course->subject_name,
                'description' => $course->subject_description,
                'price' => $course->subject_price,
                'order' => $course->subject_order,
                'is_required' => $course->is_required,
                'content_items' => $contentItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->content_title,
                        'description' => $item->content_description,
                        'type' => $item->content_type,
                        'data' => $item->content_data,
                        'attachment_path' => $item->attachment_path,
                        'attachment_url' => $item->attachment_path ? asset('storage/' . $item->attachment_path) : null,
                        'max_points' => $item->max_points,
                        'due_date' => $item->due_date,
                        'time_limit' => $item->time_limit,
                        'order' => $item->content_order,
                    ];
                })
            ];
        }
        
        // Parse content data properly (for module-level content)
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
            'content_data' => $contentData,
            'courses' => $formattedCourses
        ];
        
        // Check for video content
        if ($module->video_path) {
            $moduleData['content_data']['video_url'] = asset('storage/' . $module->video_path);
        }

        return view('student.student-courses.student-module', compact('user', 'module', 'program', 'moduleData', 'courses', 'formattedCourses'));
    }
    */    /**
     * Mark a module as completed (toggle on)
     */
    public function completeModule($id, \Illuminate\Http\Request $request)
    {
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }
        $programId = $request->input('program_id');
        // Check if already completed
        $exists = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
            ->where('modules_id', $id)
            ->exists();
        if (!$exists) {
            \App\Models\ModuleCompletion::create([
                'student_id' => $student->student_id,
                'modules_id' => $id,
                'program_id' => $programId,
                'completed_at' => now(),
            ]);
        }
        // Calculate progress
        $totalModules = \App\Models\Module::where('program_id', $programId)->count();
        $completedModules = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
            ->where('program_id', $programId)
            ->count();
        $progress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
        return response()->json([
            'success' => true,
            'progress_percentage' => $progress,
            'completed_modules' => $completedModules,
            'total_modules' => $totalModules,
        ]);
    }
    
    /**
     * Unmark a module as completed (toggle off)
     */
    public function uncompleteModule(Request $request)
    {
        try {
            $student = \App\Models\Student::where('user_id', session('user_id'))->first();
            $moduleId = $request->input('module_id');
            if (!$student || !$moduleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student or module not found.'
                ]);
            }
            $completion = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                ->where('modules_id', $moduleId)
                ->first();
            if ($completion) {
                $programId = $completion->program_id;
                $completion->delete();
                // Recalculate progress
                $totalModules = \App\Models\Module::where('program_id', $programId)->count();
                $completedModules = \App\Models\ModuleCompletion::where('student_id', $student->student_id)
                    ->where('program_id', $programId)
                    ->count();
                $progressPercentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;
                return response()->json([
                    'success' => true,
                    'message' => 'Module unmarked as complete.',
                    'progress_percentage' => $progressPercentage,
                    'completed_modules' => $completedModules,
                    'total_modules' => $totalModules,
                    'course_id' => $programId
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Completion record not found.'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Module uncompletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error unmarking module as complete.'
            ]);
        }
    }
    
    /**
     * Save assignment as draft
     */
    public function saveAssignmentDraft(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required',
                'files.*' => 'file|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png|max:102400',
                'module_id' => 'required|integer',
                'content_id' => 'required|integer', // <-- ensure content_id is required
                'comments' => 'nullable|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }

        $student = Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }

        $moduleId = $request->input('module_id');
        $contentId = $request->input('content_id');
        $module = Module::find($moduleId);
        if (!$module) {
            return response()->json(['success' => false, 'message' => 'Module not found.']);
        }

        $fileInfos = [];
        foreach ($request->file('files') as $file) {
            $fileName = time() . '_' . $student->student_id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            $fileInfos[] = [
                'path' => $filePath,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        // Check if a draft exists for this content_id
        $draft = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
            ->where('module_id', $moduleId)
            ->where('content_id', $contentId)
            ->where('status', 'draft')
            ->first();
        if ($draft) {
            $draft->update([
                'files' => $fileInfos,
                'comments' => $request->comments,
                'content_id' => $contentId, // Always set content_id on update
            ]);
        } else {
            \App\Models\AssignmentSubmission::create([
                'student_id' => $student->student_id,
                'module_id' => $moduleId,
                'program_id' => $module->program_id,
                'content_id' => $contentId,
                'files' => $fileInfos,
                'comments' => $request->comments,
                'status' => 'draft',
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Draft saved successfully!']);
    }

    /**
     * Submit assignment (from draft or new)
     */
    public function submitAssignment(Request $request)
    {
        try {
            $request->validate([
                'files' => 'sometimes|required',
                'files.*' => 'file|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png|max:102400',
                'module_id' => 'required|integer',
                'content_id' => 'required|integer', // <-- ensure content_id is required
                'comments' => 'nullable|string'
            ]);

            $student = Student::where('user_id', session('user_id'))->first();
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.']);
            }

            $moduleId = $request->input('module_id');
            $contentId = $request->input('content_id');
            $module = Module::find($moduleId);
            if (!$module) {
                return response()->json(['success' => false, 'message' => 'Module not found.']);
            }

            // Check for draft for this content_id
            $draft = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
                ->where('module_id', $moduleId)
                ->where('content_id', $contentId)
                ->where('status', 'draft')
                ->first();
            if ($draft) {
                // Update draft to submitted
                $fileInfos = $draft->files;
                if ($request->hasFile('files')) {
                    $fileInfos = [];
                    foreach ($request->file('files') as $file) {
                        $fileName = time() . '_' . $student->student_id . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('assignments', $fileName, 'public');
                        $fileInfos[] = [
                            'path' => $filePath,
                            'type' => $file->getMimeType(),
                            'original_name' => $file->getClientOriginalName(),
                            'size' => $file->getSize(),
                        ];
                    }
                }
                $draft->update([
                    'files' => $fileInfos,
                    'comments' => $request->comments ?? $draft->comments,
                    'submitted_at' => now(),
                    'status' => 'submitted',
                ]);
                
                // Update deadline status for this assignment
                $this->updateAssignmentDeadlineStatus($student->student_id, $contentId, 'completed');
                
                return response()->json(['success' => true, 'message' => 'Assignment submitted successfully!']);
            }

            // No draft, create new submission
            $fileInfos = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $student->student_id . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('assignments', $fileName, 'public');
                    $fileInfos[] = [
                        'path' => $filePath,
                        'type' => $file->getMimeType(),
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }
            \App\Models\AssignmentSubmission::create([
                'student_id' => $student->student_id,
                'module_id' => $moduleId,
                'program_id' => $module->program_id,
                'content_id' => $contentId,
                'files' => $fileInfos,
                'comments' => $request->comments,
                'submitted_at' => now(),
                'status' => 'submitted'
            ]);
            
            // Update deadline status for this assignment
            $this->updateAssignmentDeadlineStatus($student->student_id, $contentId, 'completed');
            
            return response()->json(['success' => true, 'message' => 'Assignment submitted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Edit assignment draft
     */
    public function editAssignmentDraft(Request $request)
    {
        $request->validate([
            'files' => 'sometimes|required',
            'files.*' => 'file|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png|max:102400',
            'module_id' => 'required|integer',
            'content_id' => 'required|integer', // <-- ensure content_id is required
            'comments' => 'nullable|string'
        ]);

        $student = Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }

        $moduleId = $request->input('module_id');
        $contentId = $request->input('content_id');
        $draft = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
            ->where('module_id', $moduleId)
            ->where('content_id', $contentId)
            ->where('status', 'draft')
            ->first();
        if (!$draft) {
            return response()->json(['success' => false, 'message' => 'Draft not found.']);
        }
        $fileInfos = $draft->files;
        if ($request->hasFile('files')) {
            $fileInfos = [];
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $student->student_id . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('assignments', $fileName, 'public');
                $fileInfos[] = [
                    'path' => $filePath,
                    'type' => $file->getMimeType(),
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }
        $draft->update([
            'files' => $fileInfos,
            'comments' => $request->comments ?? $draft->comments,
        ]);
        return response()->json(['success' => true, 'message' => 'Draft updated successfully!']);
    }

    /**
     * Remove assignment draft
     */
    public function removeAssignmentDraft(Request $request)
    {
        $request->validate([
            'module_id' => 'required|integer',
            'content_id' => 'required|integer' // <-- ensure content_id is required
        ]);
        $student = Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.']);
        }
        $moduleId = $request->input('module_id');
        $contentId = $request->input('content_id');
        $draft = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
            ->where('module_id', $moduleId)
            ->where('content_id', $contentId)
            ->where('status', 'draft')
            ->first();
        if ($draft) {
            $draft->delete();
            return response()->json(['success' => true, 'message' => 'Draft removed successfully!']);
        }
        return response()->json(['success' => false, 'message' => 'Draft not found.']);
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
            Log::error('Quiz submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the quiz.'
            ]);
        }
    }
    
    /**
     * Get module courses with lessons and content items
     */
    public function getModuleCourses($moduleId)
    {
        try {
            // Get the module
            $module = Module::find($moduleId);
            
            if (!$module) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module not found.'
                ], 404);
            }
            
            // Get the student
            $student = Student::where('user_id', session('user_id'))->first();
            $enrollment = null;
            if ($student) {
                // Always get the latest approved or pending enrollment for this program
                $enrollment = $student->enrollments()
                    ->where('program_id', $module->program_id)
                    ->orderByDesc('enrollment_status') // approved > pending > others
                    ->orderByDesc('created_at')
                    ->first();
                if (!$enrollment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not enrolled in this course.'
                    ], 403);
                }
            }
            
            // Get courses associated with this module
            $coursesQuery = \App\Models\Course::where('module_id', $moduleId)
                //->with(['lessons' => function($query) {
                //    $query->with(['contentItems' => function($contentQuery) {
                //        $contentQuery->select('id', 'lesson_id', 'course_id', 'content_type', 'content_title', 'content_description', 'attachment_path', 'content_data', 'max_points', 'due_date', 'is_required');
                //    }])->orderBy('lesson_order');
                //}])
                ->select('subject_id', 'subject_name', 'subject_description', 'subject_price', 'is_required', 'module_id')
                ->orderBy('subject_order');

            // Filter by EnrollmentCourse for modular enrollments
            if ($student && $enrollment && $enrollment->enrollment_type === 'Modular') {
                // Ensure enrollment course records exist
                $this->ensureEnrollmentCourseRecords($enrollment);
                
                $allowedCourseIds = $enrollment->enrollmentCourses()
                    ->where('is_active', true)
                    ->pluck('course_id')
                    ->toArray();
                
                Log::info('Modular enrollment course filtering', [
                    'enrollment_id' => $enrollment->enrollment_id,
                    'module_id' => $moduleId,
                    'enrollment_type' => $enrollment->enrollment_type,
                    'allowed_course_ids' => $allowedCourseIds,
                    'student_id' => $student->student_id,
                    'user_id' => session('user_id')
                ]);
                
                if (!empty($allowedCourseIds)) {
                    $coursesQuery->whereIn('subject_id', $allowedCourseIds);
                } else {
                    // If no allowed courses found, check if we should fall back to registration data
                    Log::warning('No enrolled courses found for modular enrollment, checking registration data', [
                        'enrollment_id' => $enrollment->enrollment_id,
                        'module_id' => $moduleId
                    ]);
                    
                    // Fallback: check registration selected_modules for this specific module
                    $registration = \App\Models\Registration::where('user_id', session('user_id'))
                        ->where('program_id', $enrollment->program_id)
                        ->where('enrollment_type', 'Modular')
                        ->first();
                    
                    if ($registration && $registration->selected_modules) {
                        $selectedModulesData = json_decode($registration->selected_modules, true);
                        $fallbackCourseIds = [];
                        
                        if (is_array($selectedModulesData)) {
                            foreach ($selectedModulesData as $moduleData) {
                                $moduleIdFromData = is_array($moduleData) ? ($moduleData['id'] ?? $moduleData['module_id'] ?? null) : $moduleData;
                                
                                if ($moduleIdFromData == $moduleId && isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                                    foreach ($moduleData['selected_courses'] as $courseId) {
                                        $fallbackCourseIds[] = is_array($courseId) ? ($courseId['id'] ?? $courseId['course_id'] ?? $courseId) : $courseId;
                                    }
                                }
                            }
                        }
                        
                        if (!empty($fallbackCourseIds)) {
                            Log::info('Using fallback course filtering from registration data', [
                                'fallback_course_ids' => $fallbackCourseIds,
                                'module_id' => $moduleId
                            ]);
                            $coursesQuery->whereIn('subject_id', $fallbackCourseIds);
                        } else {
                            // If still no courses found, return empty collection
                            $courses = collect();
                        }
                    } else {
                        // No registration data available, return empty
                        $courses = collect();
                    }
                }
            }

            if (!isset($courses)) {
                $courses = $coursesQuery->get();
            }
            
            // Format the response
            $formattedCourses = $courses->map(function($course) {
                $contentItems = \App\Models\ContentItem::where('course_id', $course->subject_id)
                    ->select('id', 'content_type', 'content_title', 'content_description', 'attachment_path', 'content_data', 'max_points', 'due_date', 'is_required')
                    ->orderBy('content_order')
                    ->get();
                return [
                    'course_id' => $course->subject_id,
                    'course_name' => $course->subject_name,
                    'course_description' => $course->subject_description,
                    'price' => $course->subject_price,
                    'duration' => null, // Not available in this table structure
                    'required' => (bool) $course->is_required,
                    'content_items' => $contentItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'content_type' => $item->content_type,
                            'content_title' => $item->content_title,
                            'content_description' => $item->content_description,
                            'content_url' => $item->attachment_path,
                            'attachment_path' => $item->attachment_path,
                            'content_data' => $item->content_data,
                            'max_points' => $item->max_points,
                            'due_date' => $item->due_date,
                            'is_required' => (bool) $item->is_required
                        ];
                    })
                ];
            });
            
            return response()->json([
                'success' => true,
                'courses' => $formattedCourses,
                'module' => [
                    'id' => $module->modules_id,
                    'name' => $module->module_name,
                    'description' => $module->module_description
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get module courses error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading course content.'
            ], 500);
        }
    }

    /**
     * Get submission information for a content item
     */
    public function getSubmissionInfo($contentId)
    {
        try {
            $content = ContentItem::findOrFail($contentId);
            
            return response()->json([
                'success' => true,
                'allowed_file_types' => $content->allowed_file_types,
                'max_file_size' => $content->max_file_size / 1024, // Convert KB to MB
                'submission_instructions' => $content->submission_instructions,
                'allow_multiple_submissions' => $content->allow_multiple_submissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found'
            ], 404);
        }
    }

    /**
     * Submit assignment file
     */
    public function submitAssignmentFile(Request $request)
    {
        try {
            $student = Student::where('user_id', session('user_id'))->firstOrFail();
            $content = ContentItem::findOrFail($request->content_id);
            // Validate the request
            $request->validate([
                'content_id' => 'required|exists:content_items,id',
                'files' => 'required', // We'll validate each file below
                'notes' => 'nullable|string|max:1000'
            ]);
            $files = $request->file('files');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = 'submission_' . $student->student_id . '_' . $content->id . '_' . time() . '_' . uniqid() . '.' . $extension;
                $filePath = $file->storeAs('submissions', $filename, 'public');
                \App\Models\AssignmentSubmission::create([
                    'student_id' => $student->student_id,
                    'content_id' => $content->id,
                    'file_path' => $filePath,
                    'original_filename' => $originalName,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'submission_notes' => $request->notes,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);
                
                // Update deadline status for this assignment
                $this->updateAssignmentDeadlineStatus($student->student_id, $content->id, 'completed');
            }
            return response()->json(['success' => true, 'message' => 'Assignment submitted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error submitting assignment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get allowed MIME types based on file type restriction
     */
    private function getAllowedMimeTypes($allowedTypes)
    {
        $mimeTypes = [];
        $types = explode(',', $allowedTypes);
        
        foreach ($types as $type) {
            switch (trim($type)) {
                case 'image':
                    $mimeTypes = array_merge($mimeTypes, [
                        'image/jpeg', 'image/png', 'image/gif', 'image/webp'
                    ]);
                    break;
                case 'document':
                    $mimeTypes = array_merge($mimeTypes, [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                    ]);
                    break;
                case 'pdf':
                    $mimeTypes[] = 'application/pdf';
                    break;
            }
        }
        
        return $mimeTypes;
    }

    /**
     * Check if file extension is allowed
     */
    private function isAllowedExtension($extension, $allowedTypes)
    {
        $allowedExtensions = [];
        $types = explode(',', $allowedTypes);
        
        foreach ($types as $type) {
            switch (trim($type)) {
                case 'image':
                    $allowedExtensions = array_merge($allowedExtensions, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    break;
                case 'document':
                    $allowedExtensions = array_merge($allowedExtensions, ['pdf', 'doc', 'docx', 'ppt', 'pptx']);
                    break;
                case 'pdf':
                    $allowedExtensions[] = 'pdf';
                    break;
                default:
                    // Custom extensions
                    $allowedExtensions[] = trim($type);
                    break;
            }
        }
        
        return in_array($extension, $allowedExtensions);
    }

    /**
     * Get content details for content viewer
     */
    public function getContent($contentId)
    {
        try {
            // Log the request for debugging
            \Log::info('StudentDashboardController::getContent called', ['contentId' => $contentId]);
            
            // Find the content item
            $content = DB::table('content_items')
                ->where('id', $contentId)
                ->first();

            \Log::info('Content query result', ['content' => $content]);

            if (!$content) {
                \Log::warning('Content not found', ['contentId' => $contentId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Content not found'
                ], 404);
            }

            // Parse content data if it exists
            $contentData = null;
            if ($content->content_data) {
                try {
                    $contentData = json_decode($content->content_data, true);
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse content_data JSON', ['error' => $e->getMessage()]);
                    $contentData = null;
                }
            }

            $response = [
                'success' => true,
                'content' => [
                    'id' => $content->id,
                    'content_title' => $content->content_title ?? '',
                    'content_description' => $content->content_description ?? '',
                    'content_type' => $content->content_type ?? 'lesson',
                    'content_text' => $content->content_text ?? '',
                    'content_url' => $content->content_url ?? '',
                    'attachment_path' => $content->attachment_path ?? '',
                    'due_date' => $content->due_date ?? null,
                    'content_data' => $contentData,
                    'enable_submission' => $content->enable_submission ?? false,
                    'submission_instructions' => $content->submission_instructions ?? '',
                    'allowed_file_types' => $content->allowed_file_types ?? '',
                    'max_file_size' => $content->max_file_size ?? 10,
                    'is_required' => $content->is_required ?? false
                ]
            ];

            \Log::info('Successful content response', ['response' => $response]);
            
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error getting content details', [
                'contentId' => $contentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading content: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssignmentSubmissions($moduleId)
    {
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        $submissions = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
            ->where('module_id', $moduleId)
            ->orderBy('submitted_at', 'desc')
            ->get();
        foreach ($submissions as $sub) {
            $sub->files = is_string($sub->files) ? json_decode($sub->files, true) : $sub->files;
        }
        return response()->json(['success' => true, 'submissions' => $submissions]);
    }

    public function getSubmissionsByContent($contentId)
    {
        $student = \App\Models\Student::where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }
        
        // Get the content item to find the module
        $content = \App\Models\ContentItem::find($contentId);
        if (!$content) {
            return response()->json(['success' => false, 'message' => 'Content not found'], 404);
        }
        
        // FIX: Only get submissions for this student AND this content_id
        $submissions = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
            ->where('content_id', $contentId)
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        foreach ($submissions as $sub) {
            $sub->files = is_string($sub->files) ? json_decode($sub->files, true) : $sub->files;
        }
        
        return response()->json(['success' => true, 'submissions' => $submissions]);
    }

    public function markContentDone($contentId)
    {
        try {
            $student = \App\Models\Student::where('user_id', session('user_id'))->firstOrFail();
            $content = \App\Models\ContentItem::findOrFail($contentId);
            
            // Prevent duplicate completions - use the correct table
            $exists = \App\Models\ContentCompletion::where('student_id', $student->student_id)
                ->where('content_id', $contentId)
                ->exists();
            if ($exists) {
                return response()->json(['success' => true, 'message' => 'Already marked as done.']);
            }
            
            \App\Models\ContentCompletion::create([
                'student_id' => $student->student_id,
                'content_id' => $contentId,
                'course_id' => $content->course_id ?? null,
                'module_id' => $content->module_id ?? null,
                'completed_at' => now(),
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update overdue assignment statuses for a specific student (called automatically)
     */
    private function updateOverdueAssignmentStatuses($student)
    {
        try {
            $overdueDeadlines = \App\Models\Deadline::where('student_id', $student->student_id)
                ->where('type', 'assignment')
                ->where('due_date', '<', now())
                ->where('status', 'pending')
                ->get();

            foreach ($overdueDeadlines as $deadline) {
                // Check if the assignment has been submitted
                $submission = \App\Models\AssignmentSubmission::where('student_id', $deadline->student_id)
                    ->where('content_id', $deadline->reference_id)
                    ->first();

                if ($submission) {
                    $deadline->update(['status' => 'completed']);
                } else {
                    $deadline->update(['status' => 'overdue']);
                }
            }
        } catch (\Exception $e) {
            // Silently handle errors - this is called automatically
            Log::warning('Error updating overdue assignment statuses: ' . $e->getMessage(), [
                'student_id' => $student->student_id
            ]);
        }
    }

    /**
     * Update overdue assignment deadline statuses
     */
    public function updateOverdueDeadlines()
    {
        try {
            $overdueDeadlines = \App\Models\Deadline::where('type', 'assignment')
                ->where('due_date', '<', now())
                ->where('status', 'pending')
                ->get();

            foreach ($overdueDeadlines as $deadline) {
                // Check if the assignment has been submitted
                $submission = \App\Models\AssignmentSubmission::where('student_id', $deadline->student_id)
                    ->where('content_id', $deadline->reference_id)
                    ->first();

                if ($submission) {
                    $deadline->update(['status' => 'completed']);
                } else {
                    $deadline->update(['status' => 'overdue']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Updated ' . $overdueDeadlines->count() . ' deadline statuses'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating overdue deadlines: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update assignment deadline status when assignment is submitted
     */
    private function updateAssignmentDeadlineStatus($studentId, $contentId, $status = 'completed')
    {
        try {
            $deadline = \App\Models\Deadline::where('student_id', $studentId)
                ->where('type', 'assignment')
                ->where('reference_id', $contentId)
                ->first();

            if ($deadline) {
                $deadline->update(['status' => $status]);
            }
        } catch (\Exception $e) {
            Log::warning('Error updating assignment deadline status: ' . $e->getMessage(), [
                'student_id' => $studentId,
                'content_id' => $contentId,
                'status' => $status
            ]);
        }
    }

    /**
     * Create missing deadline entries for assignments that don't have them
     */
    private function createMissingAssignmentDeadlines($student, $enrolledProgramIds, $enrolledCourseIds)
    {
        try {
            // Get all assignments in enrolled courses that have due dates
            $assignments = \App\Models\ContentItem::where('content_type', 'assignment')
                ->whereNotNull('due_date')
                ->whereIn('course_id', $enrolledCourseIds)
                ->get();

            foreach ($assignments as $assignment) {
                // Check if deadline already exists for this student and assignment
                $existingDeadline = \App\Models\Deadline::where('student_id', $student->student_id)
                    ->where('type', 'assignment')
                    ->where('reference_id', $assignment->id)
                    ->first();

                if (!$existingDeadline) {
                    // Get the program ID for this assignment's course
                    $course = \App\Models\Course::find($assignment->course_id);
                    $programId = $course ? $course->module->program_id : null;

                    if ($programId && in_array($programId, $enrolledProgramIds)) {
                        // Check if student has submitted this assignment
                        $submission = \App\Models\AssignmentSubmission::where('student_id', $student->student_id)
                            ->where('content_id', $assignment->id)
                            ->first();

                        $status = $submission ? 'completed' : 'pending';
                        
                        // Only create deadline if assignment is not overdue or if it's still pending
                        if ($assignment->due_date >= now() || !$submission) {
                            \App\Models\Deadline::create([
                                'student_id' => $student->student_id,
                                'program_id' => $programId,
                                'title' => 'Assignment: ' . $assignment->content_title,
                                'description' => $assignment->content_description ?? 'Complete the assigned assignment',
                                'type' => 'assignment',
                                'reference_id' => $assignment->id,
                                'due_date' => $assignment->due_date,
                                'status' => $status,
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error creating missing assignment deadlines: ' . $e->getMessage(), [
                'student_id' => $student->student_id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get targeted announcements for a student based on new targeting system
     */
    private function getTargetedAnnouncements($student, $enrolledProgramIds)
    {
        Log::info('Getting targeted announcements for student', [
            'student_id' => $student->student_id,
            'enrolled_programs' => $enrolledProgramIds
        ]);

        $query = \App\Models\Announcement::where('is_active', true)
            ->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expire_date')
                  ->orWhere('expire_date', '>', now());
            });

        $query->where(function($mainQuery) use ($student, $enrolledProgramIds) {
            // Include announcements for all users
            $mainQuery->where('target_scope', 'all');

            // Include specific targeting announcements
            $mainQuery->orWhere(function($specificQuery) use ($student, $enrolledProgramIds) {
                $specificQuery->where('target_scope', 'specific');

                // Use a hybrid approach that works with both properly formatted and malformed JSON
                $specificQuery->where(function($hybridQuery) use ($student, $enrolledProgramIds) {
                    $hybridQuery->where(function($properQuery) use ($student, $enrolledProgramIds) {
                        // Proper JSON approach (for new data)
                        $properQuery->where(function($userQuery) {
                            $userQuery->whereNull('target_users')
                                     ->orWhereJsonContains('target_users', 'students');
                        });

                        if (!empty($enrolledProgramIds)) {
                            $properQuery->where(function($programQuery) use ($enrolledProgramIds) {
                                $programQuery->whereNull('target_programs');
                                foreach ($enrolledProgramIds as $programId) {
                                    $programQuery->orWhereJsonContains('target_programs', $programId);
                                }
                            });
                        } else {
                            $properQuery->whereNull('target_programs');
                        }

                        // Get student batch and plan info
                        $enrollments = $student->enrollments()->where('enrollment_status', 'approved')->get();
                        $batchIds = $enrollments->whereNotNull('batch_id')->pluck('batch_id')->unique()->toArray();
                        $enrollmentTypes = $enrollments->pluck('enrollment_type')->unique()->toArray();

                        if (!empty($batchIds)) {
                            $properQuery->where(function($batchQuery) use ($batchIds) {
                                $batchQuery->whereNull('target_batches');
                                foreach ($batchIds as $batchId) {
                                    $batchQuery->orWhereJsonContains('target_batches', $batchId);
                                }
                            });
                        } else {
                            $properQuery->whereNull('target_batches');
                        }

                        if (!empty($enrollmentTypes)) {
                            $properQuery->where(function($planQuery) use ($enrollmentTypes) {
                                $planQuery->whereNull('target_plans');
                                foreach ($enrollmentTypes as $type) {
                                    $planType = strtolower($type) === 'modular' ? 'modular' : 'full';
                                    $planQuery->orWhereJsonContains('target_plans', $planType);
                                }
                            });
                        } else {
                            $properQuery->whereNull('target_plans');
                        }
                    })
                    ->orWhere(function($legacyQuery) use ($student, $enrolledProgramIds) {
                        // Legacy approach for malformed JSON (fallback)
                        $legacyQuery->where(function($userQuery) {
                            $userQuery->whereNull('target_users')
                                     ->orWhere('target_users', 'LIKE', '%"students"%');
                        });

                        if (!empty($enrolledProgramIds)) {
                            $legacyQuery->where(function($programQuery) use ($enrolledProgramIds) {
                                $programQuery->whereNull('target_programs');
                                foreach ($enrolledProgramIds as $programId) {
                                    $programQuery->orWhere('target_programs', 'LIKE', '%"' . $programId . '"%');
                                }
                            });
                        } else {
                            $legacyQuery->whereNull('target_programs');
                        }

                        // Same batch and plan logic but with LIKE queries
                        $enrollments = $student->enrollments()->where('enrollment_status', 'approved')->get();
                        $batchIds = $enrollments->whereNotNull('batch_id')->pluck('batch_id')->unique()->toArray();
                        $enrollmentTypes = $enrollments->pluck('enrollment_type')->unique()->toArray();

                        if (!empty($batchIds)) {
                            $legacyQuery->where(function($batchQuery) use ($batchIds) {
                                $batchQuery->whereNull('target_batches');
                                foreach ($batchIds as $batchId) {
                                    $batchQuery->orWhere('target_batches', 'LIKE', '%"' . $batchId . '"%');
                                }
                            });
                        } else {
                            $legacyQuery->whereNull('target_batches');
                        }

                        if (!empty($enrollmentTypes)) {
                            $legacyQuery->where(function($planQuery) use ($enrollmentTypes) {
                                $planQuery->whereNull('target_plans');
                                foreach ($enrollmentTypes as $type) {
                                    $planType = strtolower($type) === 'modular' ? 'modular' : 'full';
                                    $planQuery->orWhere('target_plans', 'LIKE', '%"' . $planType . '"%');
                                }
                            });
                        } else {
                            $legacyQuery->whereNull('target_plans');
                        }
                    });
                });
            });
        });

        $results = $query->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

        Log::info('Found announcements for student', [
            'count' => $results->count(),
            'announcement_ids' => $results->pluck('announcement_id')->toArray()
        ]);

        return $results;
    }
}
