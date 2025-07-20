<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Module;
use App\Models\Course;
use App\Models\Registration;
use App\Models\EnrollmentCourse;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
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
                return view('certificate.verify', [
                    'valid' => true,
                    'student_name' => $studentName,
                    'program' => $enrollment->program->program_name ?? 'Unknown Program',
                    'completion_date' => $enrollment->completed_at ? 
                        Carbon::parse($enrollment->completed_at)->format('F d, Y') : 
                        'In Progress'
                ]);
            }
        }

        return view('certificate.verify', [
            'valid' => false,
            'message' => 'Certificate could not be verified.'
        ]);
    }
} 