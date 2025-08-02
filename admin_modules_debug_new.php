<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Course;

// Start the Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🔧 ADMIN MODULES DEBUG - 2025\n";
echo "==============================\n\n";

// 1. Check Database Connection
echo "1. DATABASE CONNECTION:\n";
try {
    $connection = DB::connection()->getPdo();
    echo "✅ Database connected successfully\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check Courses Table Structure
echo "\n2. COURSES TABLE STRUCTURE:\n";
try {
    $columns = DB::select("DESCRIBE courses");
    echo "✅ Courses table exists with columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking courses table: " . $e->getMessage() . "\n";
}

// 3. Check Sample Course Data
echo "\n3. SAMPLE COURSE DATA:\n";
try {
    $courses = DB::table('courses')->limit(5)->get();
    echo "✅ Found " . count($courses) . " courses in database:\n";
    foreach ($courses as $course) {
        echo "   - ID: {$course->subject_id}, Name: {$course->subject_name}, Module: {$course->module_id}\n";
    }
} catch (Exception $e) {
    echo "❌ Error fetching courses: " . $e->getMessage() . "\n";
}

// 4. Check JavaScript Syntax Issues
echo "\n4. CHECKING ADMIN MODULES BLADE FILE:\n";
$adminModulesFile = __DIR__ . '/resources/views/admin/admin-modules/admin-modules.blade.php';
if (file_exists($adminModulesFile)) {
    echo "✅ Admin modules blade file exists\n";
    
    $content = file_get_contents($adminModulesFile);
    
    // Look for specific problematic patterns
    if (strpos($content, '{{ isset($course)') !== false) {
        echo "❌ Found problematic PHP blade syntax in JavaScript\n";
    } else {
        echo "✅ No problematic PHP/JS syntax mixing detected\n";
    }
    
    // Check for syntax errors around function calls
    $lines = explode("\n", $content);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, 'editCourse({{') !== false || strpos($line, 'deleteCourse({{') !== false) {
            echo "❌ Line " . ($lineNum + 1) . " has problematic syntax: " . trim($line) . "\n";
        }
    }
    
} else {
    echo "❌ Admin modules blade file not found\n";
}

// 5. Test Course Model Direct Access
echo "\n5. TESTING COURSE MODEL:\n";
try {
    $courseModel = new Course();
    $testCourse = $courseModel->first();
    if ($testCourse) {
        echo "✅ Course model working, sample course: {$testCourse->subject_name}\n";
        echo "   - ID: {$testCourse->subject_id}\n";
        echo "   - Module ID: {$testCourse->module_id}\n";
    } else {
        echo "⚠️  No courses found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Course model error: " . $e->getMessage() . "\n";
}

// 6. Simulate Admin Auth
echo "\n6. ADMIN AUTH SIMULATION:\n";
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['logged_in'] = true;
$_SESSION['user_type'] = 'admin';

echo "✅ Session simulated:\n";
echo "   - User ID: {$_SESSION['user_id']}\n";
echo "   - Logged in: " . ($_SESSION['logged_in'] ? 'true' : 'false') . "\n";
echo "   - User type: {$_SESSION['user_type']}\n";

echo "\n🏁 DEBUG COMPLETE\n";
echo "If issues found above, they need to be fixed.\n";
