<?php

// Test enrollment creation with real data
Route::post('/test-create-enrollment', function (Illuminate\Http\Request $request) {
    try {
        // Get a real student and program for testing
        $student = \App\Models\Student::where('is_archived', false)
            ->whereNotNull('date_approved')
            ->first();
            
        $program = \App\Models\Program::where('is_archived', false)->first();
        $batch = \App\Models\StudentBatch::first();
        $package = \App\Models\Package::where('program_id', $program->program_id)->first();
        
        if (!$student || !$program || !$batch || !$package) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing required data',
                'data' => [
                    'student' => $student ? 'found' : 'missing',
                    'program' => $program ? 'found' : 'missing',
                    'batch' => $batch ? 'found' : 'missing',
                    'package' => $package ? 'found' : 'missing'
                ]
            ]);
        }
        
        // Check if enrollment already exists
        $existingEnrollment = \App\Models\Enrollment::where([
            'student_id' => $student->student_id,
            'program_id' => $program->program_id
        ])->first();
        
        if ($existingEnrollment) {
            return response()->json([
                'status' => 'info',
                'message' => 'Student already enrolled',
                'enrollment_id' => $existingEnrollment->enrollment_id
            ]);
        }
        
        // Create the enrollment
        $enrollment = \App\Models\Enrollment::create([
            'student_id' => $student->student_id,
            'program_id' => $program->program_id,
            'package_id' => $package->package_id,
            'batch_id' => $batch->batch_id,
            'enrollment_type' => 'full',
            'learning_mode' => 'online',
            'enrollment_status' => 'enrolled',
            'payment_status' => 'pending',
            'amount' => $package->package_price ?? 0,
            'enrollment_date' => now()
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment created successfully',
            'enrollment_id' => $enrollment->enrollment_id,
            'data' => [
                'student' => $student->firstname . ' ' . $student->lastname,
                'program' => $program->program_name,
                'package' => $package->package_name,
                'batch' => $batch->batch_name
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});
