<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\Registration;
use App\Models\EnrollmentCourse;
use App\Models\Certificate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates for admin
     */
    public function index()
    {
        // Get students with their progress and enrollment information
        $studentsForCertificates = Student::with([
                'user', 
                'enrollments.program', 
                'enrollments.package',
                'moduleCompletions',
                'courseCompletions'
            ])
            ->whereHas('enrollments', function ($query) {
                $query->where('enrollment_status', 'approved')
                      ->orWhere('enrollment_status', 'completed')
                      ->orWhere('progress_percentage', '>=', 100);
            })
            ->get()
            ->map(function ($student) {
                return $this->calculateStudentProgress($student);
            })
            ->filter(function ($studentData) {
                // Only include students who have made significant progress or completed
                return $studentData['overall_progress'] >= 80 || $studentData['is_completed'];
            })
            ->sortByDesc('overall_progress');

        // Get existing certificates
        $certificates = Certificate::with(['student', 'program', 'enrollment', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.certificates.index', compact('studentsForCertificates', 'certificates'));
    }

    /**
     * Preview certificate for admin
     */
    public function preview(Student $student, Request $request)
    {
        $enrollmentId = $request->get('enrollment_id');
        $enrollment = Enrollment::with(['program', 'package'])
            ->where('student_id', $student->student_id)
            ->where('id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found'], 404);
        }

        $progress = $this->calculateStudentProgress($student);
        
        if ($progress['overall_progress'] < 100) {
            return response()->json(['error' => 'Student has not completed the program'], 400);
        }

        $certificateData = $this->prepareCertificateData($student, $enrollment, $progress);
        
        return response()->json([
            'success' => true,
            'certificate_data' => $certificateData,
            'html' => view('admin.certificates.preview', compact('certificateData'))->render()
        ]);
    }

    /**
     * Generate certificate for a student
     */
    public function generate(Student $student, Request $request)
    {
        $enrollmentId = $request->get('enrollment_id');
        $enrollment = Enrollment::with(['program', 'package'])
            ->where('student_id', $student->student_id)
            ->where('id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Enrollment not found'], 404);
        }

        // Check if student has completed the program (100% progress)
        $progress = $this->calculateStudentProgress($student);
        
        if ($progress['overall_progress'] < 100) {
            return response()->json(['error' => 'Student has not completed the program (100% required)'], 400);
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('student_id', $student->student_id)
            ->where('enrollment_id', $enrollmentId)
            ->first();

        if ($existingCertificate) {
            return response()->json(['error' => 'Certificate already exists for this student and enrollment'], 400);
        }

        // Create certificate record
        $certificateData = $this->prepareCertificateData($student, $enrollment, $progress);
        
        $certificate = Certificate::create([
            'student_id' => $student->student_id,
            'enrollment_id' => $enrollmentId,
            'program_id' => $enrollment->program_id,
            'certificate_number' => Certificate::generateCertificateNumber($student->student_id, $enrollment->program_id),
            'student_name' => $certificateData['student_name'],
            'program_name' => $certificateData['program_name'],
            'start_date' => $enrollment->start_date ?? $enrollment->created_at,
            'completion_date' => $enrollment->completion_date ?? now(),
            'final_score' => $progress['average_score'] ?? null,
            'certificate_type' => 'completion',
            'status' => 'pending',
            'certificate_data' => $certificateData
        ]);

        // Update enrollment to mark certificate as requested
        $enrollment->update([
            'certificate_requested' => true,
            'certificate_eligible' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificate generated successfully',
            'certificate_id' => $certificate->certificate_id
        ]);
    }

    /**
     * Approve certificate
     */
    public function approve(Student $student, Request $request)
    {
        $certificate = Certificate::where('student_id', $student->student_id)
            ->where('status', 'pending')
            ->first();

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found or already processed'], 404);
        }

        $adminId = session('admin_id') ?? session('user_id');
        $certificate->approve($adminId);

        // Update enrollment
        $enrollment = Enrollment::find($certificate->enrollment_id);
        if ($enrollment) {
            $enrollment->update([
                'certificate_issued' => true,
                'enrollment_status' => 'completed'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Certificate approved successfully'
        ]);
    }

    /**
     * Reject certificate
     */
    public function reject(Student $student, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $certificate = Certificate::where('student_id', $student->student_id)
            ->where('status', 'pending')
            ->first();

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found or already processed'], 404);
        }

        $adminId = session('admin_id') ?? session('user_id');
        $certificate->reject($adminId, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Certificate rejected successfully'
        ]);
    }

    /**
     * Download certificate for admin
     */
    public function adminDownload(Student $student, Request $request)
    {
        $certificate = Certificate::where('student_id', $student->student_id)
            ->where('status', 'approved')
            ->first();

        if (!$certificate) {
            return response()->json(['error' => 'Approved certificate not found'], 404);
        }

        return $this->generatePDF($certificate->certificate_data, $certificate->certificate_number);
    }

    /**
     * Bulk approve certificates
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:certificates,certificate_id'
        ]);

        $adminId = session('admin_id') ?? session('user_id');
        
        Certificate::whereIn('certificate_id', $request->certificate_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'issued_by' => $adminId,
                'issued_at' => now()
            ]);

        // Update corresponding enrollments
        $certificates = Certificate::whereIn('certificate_id', $request->certificate_ids)->get();
        $enrollmentIds = $certificates->pluck('enrollment_id');
        
        Enrollment::whereIn('id', $enrollmentIds)->update([
            'certificate_issued' => true,
            'enrollment_status' => 'completed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificates approved successfully'
        ]);
    }

    /**
     * Prepare certificate data
     */
    private function prepareCertificateData($student, $enrollment, $progress)
    {
        $studentName = $this->buildFullName($student->firstname, $student->middlename, $student->lastname);
        
        return [
            'student_name' => $studentName,
            'student_id' => $student->student_id,
            'program_name' => $enrollment->program->program_name ?? 'Unknown Program',
            'start_date' => $enrollment->start_date ?? $enrollment->created_at,
            'completion_date' => $enrollment->completion_date ?? now(),
            'final_score' => $progress['average_score'] ?? null,
            'duration' => $this->calculateDuration($enrollment->start_date ?? $enrollment->created_at, $enrollment->completion_date ?? now()),
            'modules_completed' => $progress['completed_modules'] ?? 0,
            'total_modules' => $progress['total_modules'] ?? 0,
            'courses_completed' => $progress['completed_courses'] ?? 0,
            'total_courses' => $progress['total_courses'] ?? 0,
            'progress_percentage' => $progress['overall_progress'] ?? 0
        ];
    }

    /**
     * Calculate duration in human readable format
     */
    private function calculateDuration($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $months = $start->diffInMonths($end);
        $days = $start->copy()->addMonths($months)->diffInDays($end);
        
        if ($months > 0) {
            return $months . ' month' . ($months > 1 ? 's' : '') . 
                   ($days > 0 ? ' and ' . $days . ' day' . ($days > 1 ? 's' : '') : '');
        }
        
        return $days . ' day' . ($days > 1 ? 's' : '');
    }

    // Display certificate in browser
    public function show(Request $request)
    {
        // Check if user is authenticated
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access certificates.');
        }

        // Check if admin is generating certificate for specific user
        $targetUserId = $request->input('user_id');
        $targetEnrollmentId = $request->input('enrollment_id');
        
        if ($targetUserId && $targetEnrollmentId) {
            // Admin/Director generating certificate for specific student
            return $this->generateCertificateForUser($targetUserId, $targetEnrollmentId);
        }

        // Student accessing their own certificate
        $userId = session('user_id');
        
        // Try to get student from database
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // If no student record, try to get from session data
            $student_name = $this->getFullNameFromSession();
        } else {
            // Build full name from database
            $student_name = $this->buildFullName($student->firstname, $student->middlename, $student->lastname);
        }

        // Get enrollment data
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('enrollment_status', 'approved')
            ->with(['program', 'package'])
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'No approved enrollment found. Please contact your administrator.');
        }

        return $this->renderCertificate($student_name, $enrollment, $userId);
    }

    // Download certificate as PDF
    public function download(Request $request)
    {
        // Check if user is authenticated
        if (!session('logged_in') || !session('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to access certificates.');
        }

        // Check if admin is generating certificate for specific user
        $targetUserId = $request->input('user_id');
        $targetEnrollmentId = $request->input('enrollment_id');
        
        if ($targetUserId && $targetEnrollmentId) {
            // Admin/Director generating certificate for specific student
            return $this->downloadCertificateForUser($targetUserId, $targetEnrollmentId);
        }

        // Student downloading their own certificate
        $userId = session('user_id');
        
        // Try to get student from database
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // If no student record, try to get from session data
            $student_name = $this->getFullNameFromSession();
        } else {
            // Build full name from database
            $student_name = $this->buildFullName($student->firstname, $student->middlename, $student->lastname);
        }

        // Get enrollment data
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('enrollment_status', 'approved')
            ->with(['program', 'package'])
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'No approved enrollment found. Please contact your administrator.');
        }

        return $this->generatePdfCertificate($student_name, $enrollment, $userId);
    }

    /**
     * Download certificate for specific user (admin function)
     */
    private function downloadCertificateForUser($userId, $enrollmentId)
    {
        // Get student data
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // Try to get user data if no student record
            $user = \App\Models\User::find($userId);
            if ($user) {
                $student_name = $this->buildFullName($user->user_firstname, '', $user->user_lastname);
            } else {
                return redirect()->back()->with('error', 'Student not found.');
            }
        } else {
            $student_name = $this->buildFullName($student->firstname, $student->middlename, $student->lastname);
        }

        // Get specific enrollment
        $enrollment = Enrollment::where('enrollment_id', $enrollmentId)
            ->where('user_id', $userId)
            ->with(['program', 'package'])
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        return $this->generatePdfCertificate($student_name, $enrollment, $userId);
    }

    /**
     * Generate PDF certificate
     */
    private function generatePdfCertificate($student_name, $enrollment, $userId)
    {
        // Get program details
        $program = $enrollment->program;
        $program_name = $program ? $program->program_name : 'Unknown Program';
        
        // Build program details based on enrollment type
        $program_details = $this->buildProgramDetails($enrollment, $program);
        
        // Get batch information
        $batch = $enrollment->batch ? $enrollment->batch->batch_name : 'Current Batch';
        
        // Determine plan type and module details
        $plan_type = strtolower($enrollment->enrollment_type);
        $module_names = $this->getEnrolledModuleNames($enrollment, $userId);
        
        // Set completion date
        $completion_date = $enrollment->completed_at ? 
            Carbon::parse($enrollment->completed_at)->format('F d, Y') : 
            Carbon::now()->format('F d, Y');

        // Generate QR code for verification
        $verification_url = url('/certificate/verify?student=' . urlencode($student_name) . '&enrollment=' . $enrollment->enrollment_id);
        $qr_code_src = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(120)->generate($verification_url));

        $pdf = Pdf::loadView('components.certificate', compact(
            'student_name', 'batch', 'program_details', 'plan_type', 'module_names', 'completion_date', 'qr_code_src'
        ));

        $filename = 'certificate_' . str_replace(' ', '_', $student_name) . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Get full name from session data
     */
    private function getFullNameFromSession()
    {
        $firstname = session('user_firstname', '');
        $middlename = session('user_middlename', '');
        $lastname = session('user_lastname', '');
        
        // If session doesn't have individual name parts, try to parse user_name
        if (empty($firstname) && empty($lastname)) {
            $fullName = session('user_name', 'Student Name');
            $nameParts = explode(' ', $fullName);
            
            if (count($nameParts) >= 2) {
                $firstname = $nameParts[0];
                $lastname = end($nameParts);
                if (count($nameParts) > 2) {
                    $middlename = implode(' ', array_slice($nameParts, 1, -1));
                }
            } else {
                return $fullName; // Return as is if can't parse
            }
        }

        return $this->buildFullName($firstname, $middlename, $lastname);
    }

    /**
     * Build full name from components
     */
    private function buildFullName($firstname, $middlename = '', $lastname = '')
    {
        $nameParts = array_filter([$firstname, $middlename, $lastname]);
        return implode(' ', $nameParts) ?: 'Student Name';
    }

    /**
     * Build program details string based on enrollment type
     */
    private function buildProgramDetails($enrollment, $program)
    {
        if (!$program) {
            return 'Program Details Not Available';
        }

        $details = strtoupper($program->program_name);
        
        // Add program description if available
        if ($program->program_description) {
            $details .= ' - ' . strtoupper($program->program_description);
        }

        return $details;
    }

    /**
     * Get enrolled module names for modular enrollments
     */
    private function getEnrolledModuleNames($enrollment, $userId)
    {
        $moduleNames = [];

        try {
            // Check if it's a modular enrollment
            if (strtolower($enrollment->enrollment_type) === 'modular') {
                // Try to get from registration table first
                $registration = Registration::where('user_id', $userId)
                    ->where('enrollment_type', 'Modular')
                    ->first();

                if ($registration && $registration->selected_modules) {
                    $selectedModules = json_decode($registration->selected_modules, true);
                    
                    if (is_array($selectedModules)) {
                        foreach ($selectedModules as $moduleData) {
                            if (is_array($moduleData) && isset($moduleData['id'])) {
                                $module = Module::find($moduleData['id']);
                                if ($module) {
                                    $moduleNames[] = $module->module_name;
                                    
                                    // Also get selected courses if available
                                    if (isset($moduleData['selected_courses']) && is_array($moduleData['selected_courses'])) {
                                        $courseIds = $moduleData['selected_courses'];
                                        $courses = Course::whereIn('subject_id', $courseIds)->pluck('subject_name')->toArray();
                                        if (!empty($courses)) {
                                            $moduleNames[] = '  • Courses: ' . implode(', ', $courses);
                                        }
                                    }
                                }
                            } elseif (is_numeric($moduleData)) {
                                // Handle simple module ID array
                                $module = Module::find($moduleData);
                                if ($module) {
                                    $moduleNames[] = $module->module_name;
                                }
                            }
                        }
                    }
                }

                // Also check enrollment_courses table for specific course enrollments
                $enrollmentCourses = EnrollmentCourse::where('enrollment_id', $enrollment->enrollment_id)
                    ->with(['course', 'module'])
                    ->get();

                if ($enrollmentCourses->isNotEmpty()) {
                    $coursesByModule = [];
                    foreach ($enrollmentCourses as $ec) {
                        if ($ec->module && $ec->course) {
                            $coursesByModule[$ec->module->module_name][] = $ec->course->subject_name;
                        }
                    }

                    // Add courses grouped by module if we haven't already added them
                    foreach ($coursesByModule as $moduleName => $courses) {
                        if (!in_array($moduleName, $moduleNames)) {
                            $moduleNames[] = $moduleName;
                        }
                        $moduleNames[] = '  • Courses: ' . implode(', ', $courses);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting enrolled module names: ' . $e->getMessage());
        }

        return $moduleNames;
    }

    /**
     * Generate certificate for specific user (admin function)
     */
    private function generateCertificateForUser($userId, $enrollmentId)
    {
        // Get student data
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            // Try to get user data if no student record
            $user = \App\Models\User::find($userId);
            if ($user) {
                $student_name = $this->buildFullName($user->user_firstname, '', $user->user_lastname);
            } else {
                return redirect()->back()->with('error', 'Student not found.');
            }
        } else {
            $student_name = $this->buildFullName($student->firstname, $student->middlename, $student->lastname);
        }

        // Get specific enrollment
        $enrollment = Enrollment::where('enrollment_id', $enrollmentId)
            ->where('user_id', $userId)
            ->with(['program', 'package'])
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        return $this->renderCertificate($student_name, $enrollment, $userId);
    }

    /**
     * Render certificate view
     */
    private function renderCertificate($student_name, $enrollment, $userId)
    {
        // Get program details
        $program = $enrollment->program;
        $program_name = $program ? $program->program_name : 'Unknown Program';
        
        // Build program details based on enrollment type
        $program_details = $this->buildProgramDetails($enrollment, $program);
        
        // Get batch information
        $batch = $enrollment->batch ? $enrollment->batch->batch_name : 'Current Batch';
        
        // Determine plan type and module details
        $plan_type = strtolower($enrollment->enrollment_type);
        $module_names = $this->getEnrolledModuleNames($enrollment, $userId);
        
        // Set completion date
        $completion_date = $enrollment->completed_at ? 
            Carbon::parse($enrollment->completed_at)->format('F d, Y') : 
            Carbon::now()->format('F d, Y');

        // Generate QR code for verification
        $verification_url = url('/certificate/verify?student=' . urlencode($student_name) . '&enrollment=' . $enrollment->enrollment_id);
        $qr_code_src = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(120)->generate($verification_url));

        return view('components.certificate', compact(
            'student_name', 'batch', 'program_details', 'plan_type', 'module_names', 'completion_date', 'qr_code_src'
        ));
    }

    /**
     * Verify certificate (for QR code verification)
     */
    public function verify(Request $request)
    {
        $studentName = $request->input('student');
        $enrollmentId = $request->input('enrollment');

        if ($enrollmentId) {
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment && $enrollment->enrollment_status === 'approved') {
                return view('admin.certificates.verify', [
                    'valid' => true,
                    'student_name' => $studentName,
                    'program' => $enrollment->program->program_name ?? 'Unknown Program',
                    'completion_date' => $enrollment->completed_at ? 
                        Carbon::parse($enrollment->completed_at)->format('F d, Y') : 
                        'In Progress'
                ]);
            }
        }

        return view('admin.certificates.verify', [
            'valid' => false,
            'message' => 'Certificate could not be verified.'
        ]);
    }

    /**
     * Calculate student progress based on their enrollment and completion data
     */
    private function calculateStudentProgress($student)
    {
        $totalModules = 0;
        $completedModules = 0;
        $totalCourses = 0;
        $completedCourses = 0;
        $totalContent = 0;
        $completedContent = 0;
        $enrollments = [];

        foreach ($student->enrollments as $enrollment) {
            if (!in_array($enrollment->enrollment_status, ['approved', 'completed'])) {
                continue;
            }

            $program = $enrollment->program;
            if (!$program) continue;

            // Count total modules and courses in the program using DB queries
            $programModules = DB::table('modules')
                ->where('program_id', $program->program_id)
                ->get();
            $totalModules += $programModules->count();
            
            $programCourses = DB::table('courses')
                ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
                ->where('modules.program_id', $program->program_id)
                ->get();
            $totalCourses += $programCourses->count();
            
            $programContent = DB::table('content_items')
                ->join('courses', 'content_items.course_id', '=', 'courses.subject_id')
                ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
                ->where('modules.program_id', $program->program_id)
                ->get();
            $totalContent += $programContent->count();

            // Count completed modules using module_completions table
            $studentModuleCompletions = DB::table('module_completions')
                ->where('student_id', $student->student_id)
                ->where('program_id', $program->program_id)
                ->count();
            $completedModules += $studentModuleCompletions;

            // Count completed courses using course_completions table
            $moduleIds = $programModules->pluck('modules_id');
            $studentCourseCompletions = DB::table('course_completions')
                ->where('student_id', $student->student_id)
                ->whereIn('module_id', $moduleIds)
                ->count();
            $completedCourses += $studentCourseCompletions;

            // Count completed content using content_completions table
            $courseIds = $programCourses->pluck('subject_id');
            $studentContentCompletions = DB::table('content_completions')
                ->where('student_id', $student->student_id)
                ->whereIn('course_id', $courseIds)
                ->count();
            $completedContent += $studentContentCompletions;

            $enrollments[] = [
                'enrollment' => $enrollment,
                'program' => $program,
                'completion_status' => $enrollment->enrollment_status,
            ];
        }

        // Calculate progress percentages with proper weights
        $moduleProgress = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;
        $courseProgress = $totalCourses > 0 ? ($completedCourses / $totalCourses) * 100 : 0;
        $contentProgress = $totalContent > 0 ? ($completedContent / $totalContent) * 100 : 0;
        
        // Weighted overall progress: Content 40%, Courses 40%, Modules 20%
        $overallProgress = ($contentProgress * 0.4) + ($courseProgress * 0.4) + ($moduleProgress * 0.2);

        $isCompleted = $overallProgress >= 100 || 
                      $student->enrollments->where('enrollment_status', 'completed')->count() > 0;

        // Auto-update enrollment progress and certificate eligibility
        foreach ($student->enrollments as $enrollment) {
            if (in_array($enrollment->enrollment_status, ['approved', 'completed'])) {
                $updateData = [
                    'progress_percentage' => round($overallProgress, 2),
                    'total_modules' => $totalModules,
                    'completed_modules' => $completedModules,
                    'total_courses' => $totalCourses,
                    'completed_courses' => $completedCourses,
                    'last_activity' => now()
                ];

                // Mark as certificate eligible when progress reaches 100%
                if ($overallProgress >= 100) {
                    $updateData['certificate_eligible'] = true;
                    $updateData['completion_date'] = $updateData['completion_date'] ?? now();
                    
                    // Change enrollment status to completed if not already
                    if ($enrollment->enrollment_status !== 'completed') {
                        $updateData['enrollment_status'] = 'completed';
                    }
                    
                    Log::info("Student {$student->student_id} reached 100% progress and is now certificate eligible", [
                        'student_id' => $student->student_id,
                        'overall_progress' => $overallProgress,
                        'enrollment_id' => $enrollment->enrollment_id
                    ]);
                }

                $enrollment->update($updateData);
            }
        }

        return [
            'student' => $student,
            'enrollments' => $enrollments,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'total_content' => $totalContent,
            'completed_content' => $completedContent,
            'module_progress' => round($moduleProgress, 1),
            'course_progress' => round($courseProgress, 1),
            'content_progress' => round($contentProgress, 1),
            'overall_progress' => round($overallProgress, 1),
            'is_completed' => $isCompleted,
            'eligible_for_certificate' => $overallProgress >= 100 || $isCompleted,
            'average_score' => $this->calculateAverageScore($student)
        ];
    }

    /**
     * Calculate average score for student based on actual score tables
     */
    private function calculateAverageScore($student)
    {
        $scores = [];
        
        // Get scores from quiz_attempts table
        try {
            $quizScores = DB::table('quiz_attempts')
                ->where('student_id', $student->student_id)
                ->where('status', 'completed')
                ->whereNotNull('score')
                ->where('score', '>', 0)
                ->pluck('score');

            if ($quizScores->isNotEmpty()) {
                $scores = array_merge($scores, $quizScores->toArray());
            }
        } catch (Exception $e) {
            Log::warning('Error fetching quiz scores: ' . $e->getMessage());
        }

        // Get scores from student_grades table
        try {
            $gradeScores = DB::table('student_grades')
                ->where('student_id', $student->student_id)
                ->whereNotNull('grade')
                ->where('grade', '>', 0)
                ->whereNotNull('max_points')
                ->where('max_points', '>', 0)
                ->select(DB::raw('(grade / max_points) * 100 as percentage'))
                ->pluck('percentage');

            if ($gradeScores->isNotEmpty()) {
                $scores = array_merge($scores, $gradeScores->toArray());
            }
        } catch (Exception $e) {
            Log::warning('Error fetching grade scores: ' . $e->getMessage());
        }

        // Calculate average if we have scores
        if (!empty($scores)) {
            return round(array_sum($scores) / count($scores), 2);
        }

        return null; // No scores available
    }
} 