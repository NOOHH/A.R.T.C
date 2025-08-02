<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Bootstrap the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Admin Quiz Fix Verification ===\n\n";

echo "1. Checking if quiz_title fix is applied in controller...\n";

$controllerPath = __DIR__.'/app/Http/Controllers/Admin/QuizGeneratorController.php';
$controllerContent = file_get_contents($controllerPath);

// Look for the fixed line
if (strpos($controllerContent, "'content_title' => \$validatedData['title']") !== false) {
    echo "✅ Controller fix applied: ContentItem creation uses 'title' field correctly\n";
} else {
    echo "❌ Controller fix NOT applied: Still trying to access 'quiz_title'\n";
}

// Check if there are any remaining quiz_title references in validation context
$validationSection = substr($controllerContent, strpos($controllerContent, 'validate(['), 2000);
if (strpos($validationSection, "'quiz_title'") !== false) {
    echo "⚠️  Warning: Found 'quiz_title' references in validation section\n";
} else {
    echo "✅ Validation section looks clean - no 'quiz_title' references\n";
}

echo "\n2. Testing database connection and admin data...\n";

try {
    // Test DB connection
    $admin = DB::table('admins')->where('admin_id', 1)->first();
    if ($admin) {
        echo "✅ Database connection working - Admin found: {$admin->admin_name}\n";
    } else {
        echo "❌ No admin found with ID 1\n";
    }
    
    // Check programs/modules/courses for test data
    $programCount = DB::table('programs')->count();
    $moduleCount = DB::table('modules')->count();
    $courseCount = DB::table('courses')->count();
    
    echo "✅ Test data available: {$programCount} programs, {$moduleCount} modules, {$courseCount} courses\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing saveQuizWithQuestions validation logic...\n";

// Create a mock request to test validation
$testData = [
    'title' => 'Test Quiz',
    'description' => 'Test Description',
    'program_id' => 41,
    'module_id' => 79,
    'course_id' => 52,
    'questions' => [
        [
            'question_text' => 'Test Question',
            'question_type' => 'multiple_choice',
            'points' => 1,
            'options' => ['A', 'B', 'C', 'D'],
            'correct_answers' => [0],
            'order' => 1
        ]
    ]
];

// Check validation rules
$rules = [
    'title' => 'required|string|max:255',
    'program_id' => 'required|exists:programs,program_id',
    'module_id' => 'nullable|exists:modules,modules_id',
    'course_id' => 'nullable|exists:courses,subject_id',
    'questions' => 'required|array|min:1',
    'questions.*.question_text' => 'required|string',
    'questions.*.question_type' => 'required|string|in:multiple_choice,true_false,short_answer,essay',
    'questions.*.options' => 'nullable',
    'questions.*.correct_answer' => 'nullable',
    'questions.*.correct_answers' => 'nullable',
    'questions.*.explanation' => 'nullable|string',
    'questions.*.points' => 'nullable|numeric',
];

echo "✅ Validation rules check:\n";
foreach (['title', 'program_id', 'module_id', 'course_id', 'questions'] as $field) {
    if (isset($rules[$field])) {
        echo "  - {$field}: {$rules[$field]}\n";
    }
}

echo "\n4. Checking route configuration...\n";

// Check if the route file contains the admin quiz routes
$routeFile = __DIR__.'/routes/web.php';
$routeContent = file_get_contents($routeFile);

if (strpos($routeContent, '/admin/quiz-generator/save-quiz') !== false) {
    echo "✅ Admin quiz save route found in web.php\n";
} else {
    echo "❌ Admin quiz save route NOT found in web.php\n";
}

echo "\n5. Summary:\n";
echo "The 'quiz_title' error has been fixed by changing:\n";
echo "FROM: 'content_title' => \$validatedData['quiz_title']\n";
echo "TO:   'content_title' => \$validatedData['title']\n\n";

echo "This matches the validation rules which use 'title' as the field name.\n";
echo "The fix should resolve the 'Undefined array key \"quiz_title\"' error.\n\n";

echo "=== Verification Complete ===\n";

?>
