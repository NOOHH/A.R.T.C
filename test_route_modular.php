<?php

// Test route to verify modular enrollment course filtering
// Add this to your web.php routes file temporarily for testing

Route::get('/test-modular-filtering/{user_id}', function($userId) {
    try {
        $student = \App\Models\Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found']);
        }
        
        $enrollments = \App\Models\Enrollment::where('user_id', $userId)
            ->where('enrollment_type', 'Modular')
            ->with(['enrollmentCourses', 'program'])
            ->get();
        
        $result = [];
        foreach ($enrollments as $enrollment) {
            $enrollmentCourses = $enrollment->enrollmentCourses()->get();
            
            $result[] = [
                'enrollment_id' => $enrollment->enrollment_id,
                'program_name' => $enrollment->program->program_name ?? 'Unknown',
                'enrollment_type' => $enrollment->enrollment_type,
                'enrollment_status' => $enrollment->enrollment_status,
                'enrolled_courses_count' => $enrollmentCourses->count(),
                'enrolled_courses' => $enrollmentCourses->map(function($ec) {
                    $course = \App\Models\Course::find($ec->course_id);
                    $module = \App\Models\Module::find($ec->module_id);
                    return [
                        'course_id' => $ec->course_id,
                        'course_name' => $course ? $course->subject_name : 'Course not found',
                        'module_id' => $ec->module_id,
                        'module_name' => $module ? $module->module_name : 'Module not found',
                        'is_active' => $ec->is_active
                    ];
                })
            ];
        }
        
        return response()->json([
            'user_id' => $userId,
            'student_id' => $student->student_id,
            'enrollments' => $result
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
})->name('test.modular.filtering');
