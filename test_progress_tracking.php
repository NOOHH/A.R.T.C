<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "PROGRESS TRACKING ANALYSIS" . PHP_EOL;
echo "==========================" . PHP_EOL;

try {
    // Check table structures
    echo "1. CONTENT_COMPLETIONS TABLE:" . PHP_EOL;
    $contentColumns = DB::select('DESCRIBE content_completions');
    foreach($contentColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
    }
    
    echo PHP_EOL . "2. COURSE_COMPLETIONS TABLE:" . PHP_EOL;
    $courseColumns = DB::select('DESCRIBE course_completions');
    foreach($courseColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
    }
    
    echo PHP_EOL . "3. MODULE_COMPLETIONS TABLE:" . PHP_EOL;
    $moduleColumns = DB::select('DESCRIBE module_completions');
    foreach($moduleColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})" . PHP_EOL;
    }
    
    // Check data counts
    echo PHP_EOL . "4. DATA COUNTS:" . PHP_EOL;
    $contentCount = DB::table('content_completions')->count();
    $courseCount = DB::table('course_completions')->count();
    $moduleCount = DB::table('module_completions')->count();
    
    echo "   - Content completions: {$contentCount} records" . PHP_EOL;
    echo "   - Course completions: {$courseCount} records" . PHP_EOL;
    echo "   - Module completions: {$moduleCount} records" . PHP_EOL;
    
    // Sample progress calculation for first student
    echo PHP_EOL . "5. SAMPLE PROGRESS CALCULATION:" . PHP_EOL;
    $firstStudent = DB::table('students')->first();
    if ($firstStudent) {
        echo "   Student: {$firstStudent->firstname} {$firstStudent->lastname}" . PHP_EOL;
        
        // Get enrollments
        $enrollments = DB::table('enrollments')
            ->where('student_id', $firstStudent->student_id)
            ->get();
        
        foreach($enrollments as $enrollment) {
            echo "   Enrollment ID: {$enrollment->enrollment_id}, Program: {$enrollment->program_id}" . PHP_EOL;
            
            // Count completed content for this student
            $completedContent = DB::table('content_completions')
                ->where('student_id', $firstStudent->student_id)
                ->count();
            
            $completedCourses = DB::table('course_completions')
                ->where('student_id', $firstStudent->student_id)
                ->count();
                
            $completedModules = DB::table('module_completions')
                ->where('student_id', $firstStudent->student_id)
                ->count();
            
            echo "   - Completed content: {$completedContent}" . PHP_EOL;
            echo "   - Completed courses: {$completedCourses}" . PHP_EOL;
            echo "   - Completed modules: {$completedModules}" . PHP_EOL;
            
            // Get total content for the program
            $totalContent = DB::table('content')
                ->join('courses', 'content.course_id', '=', 'courses.course_id')
                ->join('modules', 'courses.module_id', '=', 'modules.module_id')
                ->where('modules.program_id', $enrollment->program_id)
                ->count();
                
            $totalCourses = DB::table('courses')
                ->join('modules', 'courses.module_id', '=', 'modules.module_id')
                ->where('modules.program_id', $enrollment->program_id)
                ->count();
                
            $totalModules = DB::table('modules')
                ->where('program_id', $enrollment->program_id)
                ->count();
            
            echo "   - Total content: {$totalContent}" . PHP_EOL;
            echo "   - Total courses: {$totalCourses}" . PHP_EOL;
            echo "   - Total modules: {$totalModules}" . PHP_EOL;
            
            // Calculate progress percentages
            $contentProgress = $totalContent > 0 ? round(($completedContent / $totalContent) * 100, 2) : 0;
            $courseProgress = $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100, 2) : 0;
            $moduleProgress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100, 2) : 0;
            
            echo "   - Content progress: {$contentProgress}%" . PHP_EOL;
            echo "   - Course progress: {$courseProgress}%" . PHP_EOL;
            echo "   - Module progress: {$moduleProgress}%" . PHP_EOL;
            
            // Overall progress (weighted average)
            $overallProgress = round(($contentProgress * 0.4 + $courseProgress * 0.4 + $moduleProgress * 0.2), 2);
            echo "   - Overall progress: {$overallProgress}%" . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "✅ Progress tracking analysis completed!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}
