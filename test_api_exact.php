<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING API LOGIC EXACTLY ===\n";

// Simulate the API call with user ID 174
$userId = 174;

echo "User ID: $userId\n";

// Get enrolled course IDs exactly as the API does
$enrolledCourseIds = \App\Models\EnrollmentCourse::whereHas('enrollment', function($query) use ($userId) {
    $query->where('user_id', $userId)
          ->where('enrollment_status', '!=', 'rejected');
})->where('is_active', true)
  ->pluck('course_id')
  ->toArray();

echo "Enrolled Course IDs: " . implode(', ', $enrolledCourseIds) . "\n\n";

// Get programs exactly as the API does
$programs = \App\Models\Program::with(['modules.courses.contentItems'])
    ->where('is_archived', false)
    ->get();

$filteredPrograms = [];
foreach ($programs as $program) {
    $filteredModules = [];
    foreach ($program->modules as $module) {
        $filteredCourses = [];
        foreach ($module->courses as $course) {
            // Check if course is already enrolled exactly as in the API
            $isAlreadyEnrolled = in_array($course->subject_id, $enrolledCourseIds);
            
            echo "Processing Course: {$course->subject_name} (ID: {$course->subject_id}) - ";
            echo "Already Enrolled: " . ($isAlreadyEnrolled ? 'YES' : 'NO') . "\n";
            
            // Include all courses, but mark those already enrolled exactly as in the API
            $filteredCourses[] = [
                'course_id' => $course->subject_id,
                'course_name' => $course->subject_name,
                'description' => $course->subject_description,
                'content_items_count' => $course->contentItems->count(),
                'already_enrolled' => $isAlreadyEnrolled,
            ];
        }
        
        if (count($filteredCourses) > 0) {
            $filteredModules[] = [
                'module_id' => $module->modules_id,
                'module_name' => $module->module_name,
                'description' => $module->module_description,
                'courses' => $filteredCourses
            ];
        }
    }
    
    if (count($filteredModules) > 0) {
        $filteredPrograms[] = [
            'program_id' => $program->program_id,
            'program_name' => $program->program_name,
            'description' => $program->program_description,
            'modules' => $filteredModules
        ];
    }
}

echo "\n=== API RESPONSE STRUCTURE ===\n";
echo "Number of programs: " . count($filteredPrograms) . "\n";

foreach ($filteredPrograms as $program) {
    echo "\nProgram: {$program['program_name']} (ID: {$program['program_id']})\n";
    foreach ($program['modules'] as $module) {
        echo "  Module: {$module['module_name']} (ID: {$module['module_id']})\n";
        foreach ($module['courses'] as $course) {
            echo "    Course: {$course['course_name']} (ID: {$course['course_id']}) - ";
            echo "Already Enrolled: " . ($course['already_enrolled'] ? 'TRUE' : 'FALSE') . "\n";
        }
    }
}

echo "\n=== JSON RESPONSE (Sample Civil Engineer Courses) ===\n";
foreach ($filteredPrograms as $program) {
    if ($program['program_name'] === 'Civil Engineer') {
        echo json_encode(['programs' => [$program]], JSON_PRETTY_PRINT);
        break;
    }
}
