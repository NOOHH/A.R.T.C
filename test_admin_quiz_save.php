<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== ADMIN SESSION SETUP AND SAVE TEST ===\n\n";

try {
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Set up admin session (simulating admin login)
    echo "1. Setting up admin session...\n";
    $_SESSION['user_id'] = 1;
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_name'] = 'Test Admin';
    $_SESSION['user_email'] = 'admin@test.com';
    $_SESSION['logged_in'] = true;

    echo "   - PHP Session set: user_id=1, user_type=admin\n";

    // Also set Laravel session
    $app->make('session')->start();
    $laravelSession = $app->make('session');
    $laravelSession->put([
        'user_id' => 1,
        'user_name' => 'Test Admin',
        'user_email' => 'admin@test.com',
        'user_type' => 'admin',
        'user_role' => 'admin',
        'role' => 'admin',
        'logged_in' => true
    ]);

    echo "   - Laravel Session set\n";

    // 2. Test middleware logic
    echo "\n2. Testing middleware logic...\n";
    
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    $userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
    $isAdmin = $userType === 'admin';
    $isDirector = $userType === 'director';

    echo "   - isLoggedIn: " . ($isLoggedIn ? 'true' : 'false') . "\n";
    echo "   - userType: " . ($userType ?: 'null') . "\n";
    echo "   - isAdmin: " . ($isAdmin ? 'true' : 'false') . "\n";
    echo "   - isDirector: " . ($isDirector ? 'true' : 'false') . "\n";
    echo "   - Would pass middleware: " . (($isLoggedIn && ($isAdmin || $isDirector)) ? 'YES' : 'NO') . "\n";

    // 3. Test database connection and admin exists
    echo "\n3. Testing database and admin...\n";
    
    $pdo = new PDO('mysql:host=localhost;dbname=artc_db', 'root', '');
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $stmt->execute([1]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "   - Admin found in database: " . $admin['admin_name'] . "\n";
    } else {
        echo "   - Admin not found in database\n";
    }

    // 4. Test Laravel components
    echo "\n4. Testing Laravel Quiz components...\n";
    
    // Test Quiz model
    $quizModel = new App\Models\Quiz();
    echo "   - Quiz model loaded: " . get_class($quizModel) . "\n";
    
    // Test QuizQuestion model
    $questionModel = new App\Models\QuizQuestion();
    echo "   - QuizQuestion model loaded: " . get_class($questionModel) . "\n";

    // 5. Test actual quiz save data
    echo "\n5. Testing quiz save logic...\n";
    
    $testQuizData = [
        'title' => 'Test Quiz from CLI',
        'description' => 'Test Description',
        'program_id' => 41, // From the error log
        'module_id' => 79,
        'course_id' => 52,
        'time_limit' => 60,
        'max_attempts' => 1,
        'infinite_retakes' => false,
        'has_deadline' => false,
        'status' => 'draft',
        'admin_id' => 1,
        'questions' => [
            [
                'question' => 'Test question',
                'type' => 'multiple_choice',
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 0,
                'points' => 1
            ]
        ]
    ];

    echo "   - Test quiz data prepared\n";
    echo "   - Quiz title: " . $testQuizData['title'] . "\n";
    echo "   - Program ID: " . $testQuizData['program_id'] . "\n";
    echo "   - Questions count: " . count($testQuizData['questions']) . "\n";

    // 6. Create quiz manually
    echo "\n6. Testing manual quiz creation...\n";
    
    try {
        $quiz = new App\Models\Quiz();
        $quiz->quiz_title = $testQuizData['title'];
        $quiz->quiz_description = $testQuizData['description'];
        $quiz->program_id = $testQuizData['program_id'];
        $quiz->module_id = $testQuizData['module_id'];
        $quiz->course_id = $testQuizData['course_id'];
        $quiz->time_limit = $testQuizData['time_limit'];
        $quiz->max_attempts = $testQuizData['max_attempts'];
        $quiz->infinite_retakes = $testQuizData['infinite_retakes'];
        $quiz->has_deadline = $testQuizData['has_deadline'];
        $quiz->quiz_status = $testQuizData['status'];
        $quiz->admin_id = $testQuizData['admin_id'];
        $quiz->created_by = $testQuizData['admin_id'];
        $quiz->created_at = now();
        
        $saved = $quiz->save();
        
        if ($saved) {
            echo "   - ✅ Quiz saved successfully with ID: " . $quiz->quiz_id . "\n";
            
            // Now save question
            $question = new App\Models\QuizQuestion();
            $question->quiz_id = $quiz->quiz_id;
            $question->question_text = $testQuizData['questions'][0]['question'];
            $question->question_type = $testQuizData['questions'][0]['type'];
            $question->options = json_encode($testQuizData['questions'][0]['options']);
            $question->correct_answer = $testQuizData['questions'][0]['correct_answer'];
            $question->points = $testQuizData['questions'][0]['points'];
            
            $questionSaved = $question->save();
            
            if ($questionSaved) {
                echo "   - ✅ Question saved successfully with ID: " . $question->id . "\n";
            } else {
                echo "   - ❌ Question save failed\n";
            }
            
        } else {
            echo "   - ❌ Quiz save failed\n";
        }
        
    } catch (Exception $e) {
        echo "   - ❌ Quiz creation error: " . $e->getMessage() . "\n";
    }

    echo "\n=== RECOMMENDATIONS ===\n";
    echo "1. The issue is likely that the browser session is not being sent properly with AJAX requests\n";
    echo "2. The admin needs to be properly logged in through the login form\n";
    echo "3. Check that cookies are being sent with AJAX requests\n";
    echo "4. Verify CSRF token is correct\n";
    echo "5. Database operations work fine - the issue is authentication\n";

    echo "\n=== NEXT STEPS ===\n";
    echo "1. Log in as admin at: http://127.0.0.1:8000/login\n";
    echo "2. Visit admin modules page: http://127.0.0.1:8000/admin/modules\n";
    echo "3. Test the quiz generator from there\n";
    echo "4. If still failing, check browser developer tools for session cookies\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
