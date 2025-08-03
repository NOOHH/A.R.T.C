<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING API RESPONSE ===\n\n";

$programId = 40;

// Simulate the exact API logic
$modules = DB::table('modules')
    ->where('program_id', $programId)
    ->where('is_archived', false)
    ->orderBy('module_order', 'asc')
    ->select('modules_id', 'module_name', 'module_description', 'program_id')
    ->get();

echo "Raw modules query result:\n";
print_r($modules->toArray());

$moduleIds = $modules->pluck('modules_id')->toArray();
echo "\nModule IDs: " . implode(', ', $moduleIds) . "\n\n";

$courses = DB::table('courses')
    ->whereIn('module_id', $moduleIds)
    ->select('subject_id as course_id', 'subject_name as course_name', 'subject_description as description', 'module_id')
    ->get();

echo "Raw courses query result:\n";
print_r($courses->toArray());

$coursesByModule = [];
foreach ($courses as $course) {
    $coursesByModule[$course->module_id][] = [
        'course_id' => $course->course_id,
        'course_name' => $course->course_name,
        'description' => $course->description,
    ];
}

echo "\nCourses by module:\n";
print_r($coursesByModule);

$transformedModules = [];
foreach ($modules as $module) {
    $transformedModules[] = [
        'modules_id' => $module->modules_id,
        'module_id' => $module->modules_id,
        'module_name' => $module->module_name,
        'id' => $module->modules_id,
        'name' => $module->module_name,
        'description' => $module->module_description,
        'program_id' => $module->program_id,
        'courses' => $coursesByModule[$module->modules_id] ?? [],
    ];
}

echo "\nFinal transformed modules:\n";
print_r($transformedModules);

$response = [
    'success' => true,
    'modules' => $transformedModules
];

echo "\nFinal API response:\n";
echo json_encode($response, JSON_PRETTY_PRINT);

echo "\n=== END TEST ===\n";
?>
