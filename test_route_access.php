<?php
/**
 * Test Route Access
 * Tests the actual quiz route access to identify the blocking issue
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Helpers\SessionManager;

echo "=== TESTING ROUTE ACCESS ===\n\n";

try {
    // 1. Get the latest quiz attempt
    $attempt = QuizAttempt::with(['quiz.questions', 'student'])->find(13);
    if (!$attempt) {
        echo "✗ Attempt not found\n";
        exit(1);
    }
    
    echo "1. Testing with attempt ID: {$attempt->attempt_id}\n";
    echo "   - Student ID: {$attempt->student_id}\n";
    echo "   - Quiz ID: {$attempt->quiz_id}\n";
    echo "   - Status: {$attempt->status}\n";
    echo "\n";
    
    // 2. Set up session to match the attempt
    SessionManager::init();
    SessionManager::set('user_id', 15); // From your globals
    SessionManager::set('user_type', 'student');
    SessionManager::set('user_role', 'student');
    
    echo "2. Session set up:\n";
    echo "   - user_id: " . SessionManager::get('user_id') . "\n";
    echo "   - user_type: " . SessionManager::get('user_type') . "\n";
    echo "   - user_role: " . SessionManager::get('user_role') . "\n";
    echo "   - isLoggedIn: " . (SessionManager::isLoggedIn() ? 'Yes' : 'No') . "\n";
    echo "\n";
    
    // 3. Test the route generation
    $route = route('student.quiz.take', $attempt->attempt_id);
    echo "3. Generated route: {$route}\n";
    echo "\n";
    
    // 4. Test the controller method directly
    echo "4. Testing controller method directly...\n";
    
    // Get the controller
    $controller = new \App\Http\Controllers\StudentDashboardController();
    
    // Test the takeQuiz method
    try {
        $response = $controller->takeQuiz($attempt->attempt_id);
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "   - Response: Redirect\n";
            echo "   - Target: " . $response->getTargetUrl() . "\n";
            echo "   - Status: " . $response->getStatusCode() . "\n";
            
            // Check if it's redirecting to dashboard
            if (strpos($response->getTargetUrl(), 'dashboard') !== false) {
                echo "   - ✗ Redirecting to dashboard (access denied)\n";
            } else {
                echo "   - ✓ Redirecting to quiz (success)\n";
            }
        } elseif ($response instanceof \Illuminate\View\View) {
            echo "   - Response: View\n";
            echo "   - View name: " . $response->getName() . "\n";
            echo "   - ✓ Successfully returning quiz view\n";
        } else {
            echo "   - Response: " . get_class($response) . "\n";
            echo "   - Response type: " . (is_object($response) ? get_class($response) : gettype($response)) . "\n";
        }
    } catch (Exception $e) {
        echo "   - ✗ Exception: " . $e->getMessage() . "\n";
        echo "   - File: " . $e->getFile() . "\n";
        echo "   - Line: " . $e->getLine() . "\n";
    }
    echo "\n";
    
    // 5. Test middleware chain
    echo "5. Testing middleware chain...\n";
    
    // Get the route
    $routes = app('router')->getRoutes();
    $targetRoute = null;
    foreach ($routes as $routeObj) {
        if ($routeObj->getName() === 'student.quiz.take') {
            $targetRoute = $routeObj;
            break;
        }
    }
    
    if ($targetRoute) {
        echo "   - Route found: " . $targetRoute->uri() . "\n";
        echo "   - Methods: " . implode(', ', $targetRoute->methods()) . "\n";
        echo "   - Middleware: " . implode(', ', $targetRoute->middleware()) . "\n";
        
        // Check if middleware includes 'check.session'
        if (in_array('check.session', $targetRoute->middleware())) {
            echo "   - ✓ check.session middleware is applied\n";
        } else {
            echo "   - ✗ check.session middleware is missing\n";
        }
        
        // Check if middleware includes 'role.dashboard'
        if (in_array('role.dashboard', $targetRoute->middleware())) {
            echo "   - ✓ role.dashboard middleware is applied\n";
        } else {
            echo "   - ✗ role.dashboard middleware is missing\n";
        }
    } else {
        echo "   - ✗ Route not found\n";
    }
    echo "\n";
    
    // 6. Test the actual takeQuiz logic step by step
    echo "6. Testing takeQuiz logic step by step...\n";
    
    // Step 1: Get user_id from session
    $userId = SessionManager::get('user_id');
    echo "   Step 1 - Session user_id: {$userId}\n";
    
    if (!$userId) {
        echo "   - ✗ No user_id in session\n";
    } else {
        echo "   - ✓ User_id found in session\n";
    }
    
    // Step 2: Find student
    $student = Student::where('user_id', $userId)->first();
    echo "   Step 2 - Student lookup: " . ($student ? $student->student_id : 'NULL') . "\n";
    
    if (!$student) {
        echo "   - ✗ Student not found\n";
    } else {
        echo "   - ✓ Student found\n";
    }
    
    // Step 3: Find attempt
    $attemptData = QuizAttempt::with(['quiz.questions', 'student'])
        ->find($attempt->attempt_id);
    echo "   Step 3 - Attempt lookup: " . ($attemptData ? $attemptData->attempt_id : 'NULL') . "\n";
    
    if (!$attemptData) {
        echo "   - ✗ Attempt not found\n";
    } else {
        echo "   - ✓ Attempt found\n";
    }
    
    // Step 4: Check ownership
    if ($student && $attemptData) {
        $ownershipMatch = $attemptData->student_id === $student->student_id;
        echo "   Step 4 - Ownership check: " . ($ownershipMatch ? 'MATCH' : 'MISMATCH') . "\n";
        echo "     Attempt student_id: {$attemptData->student_id}\n";
        echo "     Current student_id: {$student->student_id}\n";
        
        if (!$ownershipMatch) {
            echo "   - ✗ Ownership mismatch - this would cause access denied\n";
        } else {
            echo "   - ✓ Ownership verified\n";
        }
    }
    
    // Step 5: Check status
    if ($attemptData) {
        $statusOk = $attemptData->status === 'in_progress';
        echo "   Step 5 - Status check: {$attemptData->status}\n";
        
        if (!$statusOk) {
            echo "   - ✗ Status is not in_progress - this would cause access denied\n";
        } else {
            echo "   - ✓ Status is in_progress\n";
        }
    }
    
    echo "\n";
    
    echo "=== ANALYSIS COMPLETE ===\n";
    echo "If you're getting 'access denied', check:\n";
    echo "1. Browser console for JavaScript errors\n";
    echo "2. Network tab for the actual HTTP response\n";
    echo "3. Laravel logs for detailed error messages\n";
    echo "4. Check if the route is being hit at all\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 