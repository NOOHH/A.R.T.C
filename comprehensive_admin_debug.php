<?php
/**
 * COMPREHENSIVE ADMIN QUIZ DEBUG TEST
 * Tests all aspects of the admin quiz system
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

echo "🔍 COMPREHENSIVE ADMIN QUIZ DEBUG TEST\n";
echo "=====================================\n\n";

// Test 1: Database Status
echo "1. DATABASE STATUS\n";
echo "-----------------\n";

try {
    $quizzes = DB::table('quizzes')->get();
    echo "✅ Total quizzes: " . count($quizzes) . "\n";
    
    foreach ($quizzes as $quiz) {
        $creator = 'Unknown';
        if ($quiz->admin_id) {
            $creator = "Admin ID: {$quiz->admin_id}";
        } elseif ($quiz->professor_id) {
            $creator = "Professor ID: {$quiz->professor_id}";
        }
        echo "   - Quiz #{$quiz->quiz_id}: '{$quiz->quiz_title}' | Status: {$quiz->status} | Creator: {$creator}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check Authentication Session
echo "2. AUTHENTICATION DEBUG\n";
echo "----------------------\n";

session_start();
echo "Session Data:\n";
$sessionKeys = ['user_id', 'logged_in', 'user_role', 'user_name', 'admin_id', 'director_id'];
foreach ($sessionKeys as $key) {
    $value = $_SESSION[$key] ?? 'NOT SET';
    echo "   - {$key}: {$value}\n";
}

echo "\n";

// Test 3: Controller Method Existence
echo "3. ADMIN CONTROLLER METHODS\n";
echo "---------------------------\n";

try {
    $controller = new App\Http\Controllers\Admin\QuizGeneratorController(new App\Services\GeminiQuizService());
    
    $requiredMethods = [
        'index', 'publish', 'archive', 'draft', 'delete',
        'editQuiz', 'editQuestions', 'updateQuiz'
    ];
    
    foreach ($requiredMethods as $method) {
        if (method_exists($controller, $method)) {
            echo "✅ {$method}()\n";
        } else {
            echo "❌ {$method}() - MISSING\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Controller instantiation failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Routes Check
echo "4. ROUTE VERIFICATION\n";
echo "--------------------\n";

try {
    $routes = Route::getRoutes();
    $adminQuizRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin/quiz-generator') !== false) {
            $adminQuizRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'action' => $route->getActionName()
            ];
        }
    }
    
    echo "Found " . count($adminQuizRoutes) . " admin quiz routes:\n";
    
    $requiredRoutes = [
        'admin/quiz-generator/{quiz}/edit',
        'admin/quiz-generator/update-quiz/{quiz}',
        'admin/quiz-generator/{quizId}/publish',
        'admin/quiz-generator/{quizId}/archive',
        'admin/quiz-generator/{quizId}/draft'
    ];
    
    foreach ($requiredRoutes as $required) {
        $found = false;
        foreach ($adminQuizRoutes as $route) {
            if (str_contains($route['uri'], str_replace(['{quiz}', '{quizId}'], '', $required))) {
                $found = true;
                echo "✅ {$required} - {$route['method']}\n";
                break;
            }
        }
        
        if (!$found) {
            echo "❌ {$required} - MISSING\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Route check failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Template Files
echo "5. TEMPLATE FILES\n";
echo "----------------\n";

$templates = [
    'resources/views/admin/quiz-generator/index.blade.php' => 'Main admin quiz page',
    'resources/views/admin/quiz-generator/quiz-table.blade.php' => 'Quiz table component',
    'resources/views/admin/quiz-generator/quiz-questions-edit.blade.php' => 'Edit quiz questions'
];

foreach ($templates as $path => $description) {
    if (file_exists($path)) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - MISSING: {$path}\n";
    }
}

echo "\n";

// Test 6: JavaScript Functions
echo "6. JAVASCRIPT FUNCTIONS\n";
echo "-----------------------\n";

$mainTemplate = 'resources/views/admin/quiz-generator/index.blade.php';
if (file_exists($mainTemplate)) {
    $content = file_get_contents($mainTemplate);
    
    $jsFunctions = [
        'changeQuizStatus',
        'publishQuiz',
        'archiveQuiz',
        'editQuiz'
    ];
    
    foreach ($jsFunctions as $func) {
        if (strpos($content, $func) !== false) {
            echo "✅ {$func}()\n";
        } else {
            echo "❌ {$func}() - MISSING\n";
        }
    }
} else {
    echo "❌ Cannot check JS functions - template missing\n";
}

echo "\n";

// Test 7: Quiz Status Test
echo "7. QUIZ STATUS TEST\n";
echo "------------------\n";

if (!empty($quizzes)) {
    $testQuiz = $quizzes[0];
    echo "Testing with Quiz #{$testQuiz->quiz_id}: '{$testQuiz->quiz_title}'\n";
    echo "Current status: {$testQuiz->status}\n";
    
    // Simulate admin session for API call
    $_SESSION['user_id'] = 1;
    $_SESSION['logged_in'] = true;
    $_SESSION['user_role'] = 'admin';
    
    echo "✅ Admin session simulated\n";
    echo "✅ Ready for status change testing\n";
} else {
    echo "❌ No quizzes found for testing\n";
}

echo "\n";

// Test 8: Authentication Middleware
echo "8. MIDDLEWARE CHECK\n";
echo "------------------\n";

try {
    $middleware = app()->router->getMiddleware();
    
    if (isset($middleware['admin.director.auth'])) {
        echo "✅ admin.director.auth middleware exists\n";
    } else {
        echo "❌ admin.director.auth middleware missing\n";
    }
    
} catch (Exception $e) {
    echo "❌ Middleware check failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Summary
echo "📋 SUMMARY\n";
echo "=========\n";
echo "Run this test to identify all issues before implementation.\n";
echo "Then fix each failing component systematically.\n";
echo "\n";
echo "Next steps:\n";
echo "1. Add missing editQuiz methods to admin controller\n";
echo "2. Add missing routes for edit functionality\n";
echo "3. Create edit template for admin\n";
echo "4. Fix authentication issues\n";
echo "5. Test all status change functions\n";

?>
