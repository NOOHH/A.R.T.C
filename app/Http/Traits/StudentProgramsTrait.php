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

        try {
            // Determine which database connection to use
            $connection = $this->determineConnection();
            
            // Get the student data
            $student = Student::on($connection)->where('user_id', $userId)->first();
            
            // Get enrollments
            $enrollments = collect();
            
            // Get enrollments by user_id (including pending ones)
            $userEnrollments = Enrollment::on($connection)->where('user_id', $userId)
                ->with(['program', 'package'])
                ->get();
            $enrollments = $enrollments->merge($userEnrollments);
            
            if ($student) {
                // Also get enrollments by student_id (for approved ones)
                $studentEnrollments = Enrollment::on($connection)->where('student_id', $student->student_id)
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
                'connection' => $connection,
                'programs_count' => count($studentPrograms),
                'programs' => $studentPrograms
            ]);
            
            return $studentPrograms;
            
        } catch (\Exception $e) {
            // If database tables don't exist or there's an error, return empty array
            Log::warning('StudentProgramsTrait: Failed to fetch student programs', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Determine which database connection to use
     */
    private function determineConnection()
    {
        // Check if this is a preview request
        if (request()->has('preview') && request('preview') === 'true' && request()->has('website')) {
            $websiteId = request('website');
            
            try {
                $client = \App\Models\Client::on('mysql')->find($websiteId);
                if ($client && $client->db_name) {
                    // Set up tenant connection
                    $tenantConfig = config('database.connections.mysql');
                    $tenantConfig['database'] = $client->db_name;
                    config(['database.connections.tenant_for_trait' => $tenantConfig]);
                    
                    Log::info('StudentProgramsTrait: Using tenant connection', [
                        'website_id' => $websiteId,
                        'database' => $client->db_name
                    ]);
                    
                    return 'tenant_for_trait';
                }
            } catch (\Exception $e) {
                Log::warning('StudentProgramsTrait: Failed to setup tenant connection', [
                    'website_id' => $websiteId ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Default to current connection
        return config('database.default');
    }
}
