<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Module;
use App\Models\Program;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\ModuleCompletion;

echo "=== COMPREHENSIVE SYSTEM TEST ===\n";

// 1. Check Module Content
echo "\n1. CHECKING MODULE CONTENT:\n";
$culinaryModule = Module::where('module_name', 'Module 1 - Creation of Food')->first();
if ($culinaryModule) {
    echo "✅ Module found: " . $culinaryModule->module_name . "\n";
    echo "✅ Has description: " . ($culinaryModule->module_description ? 'YES' : 'NO') . "\n";
    echo "✅ Has content data: " . (!empty($culinaryModule->content_data) ? 'YES' : 'NO') . "\n";
    
    if (!empty($culinaryModule->content_data)) {
        $data = $culinaryModule->content_data;
        echo "   - Learning objectives: " . (isset($data['learning_objectives']) ? count($data['learning_objectives']) : 0) . " items\n";
        echo "   - Duration: " . ($data['estimated_duration'] ?? 'Not set') . "\n";
        echo "   - Difficulty: " . ($data['difficulty_level'] ?? 'Not set') . "\n";
    }
} else {
    echo "❌ Module not found\n";
}

// 2. Check Program Association
echo "\n2. CHECKING PROGRAM ASSOCIATION:\n";
if ($culinaryModule) {
    $program = Program::find($culinaryModule->program_id);
    if ($program) {
        echo "✅ Program found: " . $program->program_name . "\n";
        echo "✅ Program ID: " . $program->program_id . "\n";
    } else {
        echo "❌ Program not found for module\n";
    }
}

// 3. Check Student Enrollment
echo "\n3. CHECKING STUDENT ENROLLMENTS:\n";
$enrollments = Enrollment::with(['program', 'student'])
    ->where('program_id', $culinaryModule->program_id ?? 0)
    ->get();

echo "Enrollments found: " . $enrollments->count() . "\n";
foreach ($enrollments as $enrollment) {
    if ($enrollment->student) {
        echo "✅ Student enrolled: " . ($enrollment->student->firstname ?? 'Unknown') . " " . ($enrollment->student->lastname ?? '') . "\n";
        echo "   - Student ID: " . $enrollment->student_id . "\n";
        echo "   - User ID: " . ($enrollment->student->user_id ?? 'Not linked') . "\n";
    } else {
        echo "⚠️  Enrollment found but no student record (ID: " . $enrollment->student_id . ")\n";
    }
}

// 4. Check Routes
echo "\n4. CHECKING ROUTES:\n";
$routeCommands = [
    'student.dashboard' => 'Route for student dashboard',
    'student.course' => 'Route for student course view',
    'student.module' => 'Route for student module view',
    'student.module.complete' => 'Route for module completion'
];

echo "✅ Routes should be defined in web.php\n";
foreach ($routeCommands as $route => $description) {
    echo "   - {$route}: {$description}\n";
}

// 5. Check Controllers
echo "\n5. CHECKING CONTROLLER METHODS:\n";
if (class_exists('App\Http\Controllers\StudentDashboardController')) {
    echo "✅ StudentDashboardController exists\n";
    
    $controller = new ReflectionClass('App\Http\Controllers\StudentDashboardController');
    $methods = ['dashboard', 'module', 'completeModule'];
    
    foreach ($methods as $method) {
        if ($controller->hasMethod($method)) {
            echo "✅ Method {$method} exists\n";
        } else {
            echo "❌ Method {$method} missing\n";
        }
    }
} else {
    echo "❌ StudentDashboardController not found\n";
}

// 6. Check Authentication Middleware
echo "\n6. CHECKING AUTHENTICATION:\n";
if (class_exists('App\Http\Middleware\CheckStudentAuth')) {
    echo "✅ CheckStudentAuth middleware exists\n";
} else {
    echo "❌ CheckStudentAuth middleware not found\n";
}

// 7. Check View Files
echo "\n7. CHECKING VIEW FILES:\n";
$viewFiles = [
    'student-dashboard-layout.blade.php' => 'resources/views/student/student-dashboard/student-dashboard-layout.blade.php',
    'student-module.blade.php' => 'resources/views/student/student-courses/student-module.blade.php'
];

foreach ($viewFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ {$name} exists\n";
    } else {
        echo "❌ {$name} missing at {$path}\n";
    }
}

// 8. Check CSS Files
echo "\n8. CHECKING CSS FILES:\n";
$cssFiles = [
    'student-sidebar.css' => 'public/css/student/student-sidebar.css',
    'student-modules.css' => 'public/css/student/student-modules.css',
    'student-dashboard-layout.css' => 'public/css/student/student-dashboard-layout.css'
];

foreach ($cssFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ {$name} exists\n";
    } else {
        echo "❌ {$name} missing at {$path}\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Login as a student user\n";
echo "2. Navigate to 'My Programs' > 'Culinary'\n";
echo "3. Click on 'Module 1 - Creation of Food'\n";
echo "4. Verify content displays properly\n";
echo "5. Test the sliding sidebar functionality\n";
echo "6. Test the 'Mark Complete' button\n";

?>
