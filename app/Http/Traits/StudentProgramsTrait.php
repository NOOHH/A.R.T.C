<?php

namespace App\Http\Traits;

use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;

trait StudentProgramsTrait
{
    /**
     * Get student programs for sidebar navigation
     * 
     * @return array
     */
    protected function getStudentPrograms()
    {
        $userId = session('user_id');
        if (!$userId) {
            return [];
        }

        // Get the student data
        $student = Student::where('user_id', $userId)->first();
        
        // Get enrollments
        $enrollments = collect();
        
        // Get enrollments by user_id (including pending ones)
        $userEnrollments = Enrollment::where('user_id', $userId)
            ->with(['program', 'package'])
            ->get();
        $enrollments = $enrollments->merge($userEnrollments);
        
        if ($student) {
            // Also get enrollments by student_id (for approved ones)
            $studentEnrollments = Enrollment::where('student_id', $student->student_id)
                ->with(['program', 'package'])
                ->get();
            $enrollments = $enrollments->merge($studentEnrollments);
        }
        
        // Remove duplicates based on enrollment_id
        $enrollments = $enrollments->unique('enrollment_id');
        
        $studentPrograms = [];
        
        foreach ($enrollments as $enrollment) {
            if ($enrollment->program) {
                // Only include programs that are accessible
                $canAccess = false;
                
                // Check if student has batch access
                if ($enrollment->batch_access_granted) {
                    $canAccess = true;
                } elseif ($enrollment->enrollment_status === 'approved' && $enrollment->payment_status === 'paid') {
                    $canAccess = true;
                }
                
                // Include all programs but mark accessibility
                $studentPrograms[] = [
                    'program_id' => $enrollment->program->program_id,
                    'program_name' => $enrollment->program->program_name,
                    'package_name' => $enrollment->package->package_name ?? 'Unknown Package',
                    'enrollment_status' => $enrollment->enrollment_status,
                    'payment_status' => $enrollment->payment_status,
                    'can_access' => $canAccess,
                    'batch_access_granted' => $enrollment->batch_access_granted ?? false
                ];
            }
        }
        
        Log::info('StudentProgramsTrait: Retrieved programs', [
            'user_id' => $userId,
            'programs_count' => count($studentPrograms),
            'programs' => $studentPrograms
        ]);
        
        return $studentPrograms;
    }
}
