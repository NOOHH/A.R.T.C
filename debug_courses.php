<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING COURSES FOR SPECIFIC MODULES ===\n\n";

$moduleIds = [82, 83, 84];

foreach($moduleIds as $moduleId) {
    echo "Courses for Module ID {$moduleId}:\n";
    $courses = DB::table('courses')->where('module_id', $moduleId)->get();
    
    if($courses->count() > 0) {
        foreach($courses as $course) {
            echo "   - {$course->subject_name} (ID: {$course->subject_id})\n";
        }
    } else {
        echo "   No courses found for this module.\n";
    }
    echo "\n";
}

// Let's also check which modules DO have courses
echo "Modules that have courses:\n";
$modulesWithCourses = DB::table('courses')
    ->join('modules', 'courses.module_id', '=', 'modules.modules_id')
    ->select('modules.modules_id', 'modules.module_name', 'modules.program_id', DB::raw('COUNT(courses.subject_id) as course_count'))
    ->groupBy('modules.modules_id', 'modules.module_name', 'modules.program_id')
    ->get();

foreach($modulesWithCourses as $module) {
    echo "   - Module: {$module->module_name} (ID: {$module->modules_id}, Program: {$module->program_id}) - {$module->course_count} courses\n";
}

echo "\n=== END CHECK ===\n";
?>
