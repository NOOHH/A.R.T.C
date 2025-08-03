<?php

use Illuminate\Http\Request;

// Test enrollment assignment with real data
Route::get('/test-enrollment-assignment', function () {
    try {
        // Get available data
        $students = \App\Models\Student::where('is_archived', false)
            ->whereNotNull('date_approved')
            ->get(['student_id', 'firstname', 'lastname', 'email']);
            
        $programs = \App\Models\Program::where('is_archived', false)->get(['program_id', 'program_name']);
        $batches = \App\Models\StudentBatch::get(['batch_id', 'batch_name', 'start_date']);
        $packages = \App\Models\Package::get(['package_id', 'package_name', 'program_id']);
        
        $response = [
            'status' => 'success',
            'data_available' => [
                'students' => $students->count(),
                'programs' => $programs->count(),
                'batches' => $batches->count(),
                'packages' => $packages->count()
            ],
            'sample_data' => [
                'student' => $students->first() ? [
                    'id' => $students->first()->student_id,
                    'name' => $students->first()->firstname . ' ' . $students->first()->lastname,
                    'email' => $students->first()->email
                ] : null,
                'program' => $programs->first() ? [
                    'id' => $programs->first()->program_id,
                    'name' => $programs->first()->program_name
                ] : null,
                'batch' => $batches->first() ? [
                    'id' => $batches->first()->batch_id,
                    'name' => $batches->first()->batch_name
                ] : null,
                'package' => $packages->first() ? [
                    'id' => $packages->first()->package_id,
                    'name' => $packages->first()->package_name
                ] : null
            ]
        ];
        
        // Test if we can create a sample enrollment assignment
        if ($students->count() > 0 && $programs->count() > 0 && $batches->count() > 0 && $packages->count() > 0) {
            $existingEnrollment = \App\Models\Enrollment::where([
                'student_id' => $students->first()->student_id,
                'program_id' => $programs->first()->program_id
            ])->first();
            
            $response['enrollment_test'] = [
                'can_enroll' => !$existingEnrollment,
                'existing_enrollment' => $existingEnrollment ? $existingEnrollment->enrollment_id : null
            ];
        }
        
        return response()->json($response);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});
