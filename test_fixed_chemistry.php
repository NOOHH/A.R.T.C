<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Fixed Chemistry Course Functionality\n";
echo "============================================\n";

// Set up session for testing
session(['logged_in' => true, 'professor_id' => 8, 'user_role' => 'professor']);

// Test the controller methods directly
$controller = new \App\Http\Controllers\Professor\ProfessorModuleController();

echo "1. Testing getCourseContent for Chemistry course (ID: 48):\n";
try {
    $response = $controller->getCourseContent(48);
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);
        if ($data['success']) {
            echo "✓ getCourseContent works perfectly!\n";
            echo "- Course: " . $data['course']['subject_name'] . "\n";
            echo "- Content items returned: " . count($data['content']) . "\n";
            foreach($data['content'] as $content) {
                echo "  - ID: " . $content['id'] . " | Title: " . $content['content_title'] . " | Description: " . ($content['content_description'] ?: 'No description') . "\n";
            }
        } else {
            echo "✗ getCourseContent failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ getCourseContent error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing professor access chain:\n";
$professor = \App\Models\Professor::find(8);
$assignedPrograms = $professor->assignedPrograms()->get();
echo "Professor assigned to " . $assignedPrograms->count() . " programs:\n";
foreach($assignedPrograms as $program) {
    echo "- Program ID: " . $program->program_id . " | Name: " . $program->program_name . "\n";
}

$course = \App\Models\Course::find(48);
$module = \App\Models\Module::find($course->module_id);
echo "\nChemistry course module program: " . $module->program_id . "\n";

$hasAccess = $assignedPrograms->where('program_id', $module->program_id)->count() > 0;
echo "Professor has access to Chemistry: " . ($hasAccess ? 'YES' : 'NO') . "\n";

echo "\n3. Content details for Chemistry:\n";
$content = \App\Models\ContentItem::where('course_id', 48)
    ->where('is_active', true)
    ->where('is_archived', false)
    ->get();
    
echo "Active, non-archived content: " . $content->count() . "\n";
foreach($content as $item) {
    echo "- ID: " . $item->id . " | Title: " . $item->content_title . " | Description: " . ($item->content_description ?: 'No description') . "\n";
}

echo "\nAll issues should now be resolved!\n";
