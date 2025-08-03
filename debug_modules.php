<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING MODULES AND COURSES ===\n\n";

// Get modules for program 40
echo "1. Modules for Program 40:\n";
$modules = DB::table('modules')->where('program_id', 40)->get();
foreach($modules as $module) {
    echo "   - Module: {$module->module_name} (ID: {$module->modules_id})\n";
}

// Check if courses table has module_id column
echo "\n2. Courses table structure:\n";
$columns = DB::select("DESCRIBE courses");
foreach($columns as $column) {
    echo "   - {$column->Field} ({$column->Type})\n";
}

// Get all courses to see what they look like
echo "\n3. Sample courses (first 5):\n";
$courses = DB::table('courses')->limit(5)->get();
foreach($courses as $course) {
    echo "   - Course: {$course->subject_name} (Subject ID: {$course->subject_id})\n";
    if(isset($course->module_id)) {
        echo "     Module ID: {$course->module_id}\n";
    } else {
        echo "     No module_id field found\n";
    }
}

// Check if there's a different relationship
echo "\n4. Looking for program-course relationships:\n";
$enrollments = DB::table('enrollments')
    ->join('courses', 'enrollments.subject_id', '=', 'courses.subject_id')
    ->where('enrollments.program_id', 40)
    ->select('courses.subject_name', 'courses.subject_id', 'enrollments.program_id')
    ->limit(5)
    ->get();

foreach($enrollments as $enrollment) {
    echo "   - Course: {$enrollment->subject_name} linked to Program {$enrollment->program_id}\n";
}

echo "\n=== END DEBUG ===\n";
?>
