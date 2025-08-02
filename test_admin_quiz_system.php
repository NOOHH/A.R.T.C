<?php
/**
 * Test Admin Quiz Generator System
 * This script tests the complete admin quiz creation and management flow
 */

// Include Laravel bootstrap
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuizGeneratorController;
use App\Models\Admin;
use App\Models\Program;

echo "=== ADMIN QUIZ GENERATOR SYSTEM TEST ===\n\n";

try {
    // 1. Test admin authentication simulation
    echo "1. TESTING ADMIN AUTHENTICATION:\n";
    echo "------------------------------\n";
    
    $admin = Admin::first();
    if (!$admin) {
        echo "ERROR: No admin found!\n";
        exit(1);
    }
    
    // Simulate admin session
    session([
        'user_id' => $admin->admin_id,
        'user_name' => $admin->admin_name,
        'user_email' => $admin->email,
        'user_type' => 'admin',
        'logged_in' => true
    ]);
    
    echo "✓ Admin session simulated:\n";
    echo "    User ID: " . session('user_id') . "\n";
    echo "    User Name: " . session('user_name') . "\n";
    echo "    User Type: " . session('user_type') . "\n";
    
    // 2. Test QuizGeneratorController index method
    echo "\n2. TESTING QUIZ GENERATOR INDEX:\n";
    echo "-------------------------------\n";
    
    // Enable AI quiz setting
    \App\Models\AdminSetting::updateOrCreate(
        ['setting_key' => 'ai_quiz_enabled'],
        ['setting_value' => 'true']
    );
    
    $controller = new QuizGeneratorController(app(\App\Services\GeminiQuizService::class));
    
    // Create a mock request
    $request = Request::create('/admin/quiz-generator', 'GET');
    app()->instance('request', $request);
    
    try {
        $response = $controller->index();
        echo "✓ Controller index method executed successfully\n";
        
        // Check if we get a view response
        if ($response instanceof \Illuminate\View\View) {
            $data = $response->getData();
            echo "✓ View data retrieved:\n";
            echo "    Assigned Programs: " . count($data['assignedPrograms']) . "\n";
            echo "    Draft Quizzes: " . count($data['draftQuizzes']) . "\n";
            echo "    Published Quizzes: " . count($data['publishedQuizzes']) . "\n";
            echo "    Archived Quizzes: " . count($data['archivedQuizzes']) . "\n";
        } else {
            echo "WARNING: Expected view response, got: " . get_class($response) . "\n";
        }
        
    } catch (Exception $e) {
        echo "ERROR in controller index: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    // 3. Test getModulesByProgram
    echo "\n3. TESTING GET MODULES BY PROGRAM:\n";
    echo "---------------------------------\n";
    
    $program = Program::where('is_archived', false)->first();
    if ($program) {
        echo "Testing with Program: {$program->program_name} (ID: {$program->program_id})\n";
        
        try {
            $response = $controller->getModulesByProgram($program->program_id);
            $responseData = $response->getData(true);
            
            if ($responseData['success']) {
                echo "✓ Successfully fetched modules:\n";
                echo "    Module count: " . count($responseData['modules']) . "\n";
                foreach ($responseData['modules'] as $module) {
                    echo "      - {$module['module_name']} (ID: {$module['module_id']})\n";
                }
            } else {
                echo "ERROR: Failed to fetch modules\n";
            }
        } catch (Exception $e) {
            echo "ERROR in getModulesByProgram: " . $e->getMessage() . "\n";
        }
    }
    
    // 4. Test quiz creation data preparation
    echo "\n4. TESTING QUIZ CREATION DATA:\n";
    echo "-----------------------------\n";
    
    $testQuizData = [
        'title' => 'TEST ADMIN QUIZ - ' . date('Y-m-d H:i:s'),
        'description' => 'Test quiz created by admin test script',
        'instructions' => 'Please answer all questions carefully',
        'program_id' => $program->program_id,
        'module_id' => null,
        'course_id' => null,
        'is_draft' => true,
        'time_limit' => 30,
        'max_attempts' => 3,
        'infinite_retakes' => false,
        'has_deadline' => false,
        'due_date' => null,
        'questions' => [
            [
                'question_text' => 'What is 2 + 2?',
                'question_type' => 'multiple_choice',
                'options' => ['3', '4', '5', '6'],
                'correct_answers' => ['1'], // Index 1 = '4'
                'explanation' => '2 + 2 equals 4',
                'points' => 1,
                'order' => 1
            ],
            [
                'question_text' => 'The sky is blue.',
                'question_type' => 'true_false',
                'options' => ['True', 'False'],
                'correct_answers' => ['0'], // Index 0 = 'True'
                'explanation' => 'The sky appears blue during clear weather',
                'points' => 1,
                'order' => 2
            ]
        ]
    ];
    
    echo "Test quiz data prepared:\n";
    echo "  Title: {$testQuizData['title']}\n";
    echo "  Program ID: {$testQuizData['program_id']}\n";
    echo "  Questions: " . count($testQuizData['questions']) . "\n";
    
    // 5. Test quiz saving
    echo "\n5. TESTING QUIZ SAVE METHOD:\n";
    echo "---------------------------\n";
    
    try {
        // Create a POST request with the quiz data
        $request = Request::create('/admin/quiz-generator/save', 'POST', $testQuizData);
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('X-CSRF-TOKEN', 'test-token');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        // Set the request in the app container
        app()->instance('request', $request);
        
        $response = $controller->save($request);
        $responseData = $response->getData(true);
        
        if ($responseData['success']) {
            echo "✓ Quiz saved successfully:\n";
            echo "    Quiz ID: {$responseData['quiz_id']}\n";
            echo "    Status: {$responseData['status']}\n";
            echo "    Message: {$responseData['message']}\n";
            
            // Verify the quiz in database
            $savedQuiz = \App\Models\Quiz::find($responseData['quiz_id']);
            if ($savedQuiz) {
                echo "✓ Quiz verification in database:\n";
                echo "    Title: {$savedQuiz->quiz_title}\n";
                echo "    Admin ID: {$savedQuiz->admin_id}\n";
                echo "    Professor ID: " . ($savedQuiz->professor_id ?? 'null') . "\n";
                echo "    Status: {$savedQuiz->status}\n";
                echo "    Questions: " . $savedQuiz->questions()->count() . "\n";
                
                // Clean up - delete the test quiz
                $savedQuiz->questions()->delete();
                $savedQuiz->delete();
                echo "✓ Test quiz cleaned up\n";
            }
        } else {
            echo "ERROR: Quiz save failed\n";
            echo "Message: " . ($responseData['message'] ?? 'No message') . "\n";
            if (isset($responseData['errors'])) {
                echo "Errors: " . json_encode($responseData['errors'], JSON_PRETTY_PRINT) . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "ERROR in quiz save: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    // 6. Test session data and authentication
    echo "\n6. FINAL SESSION VERIFICATION:\n";
    echo "-----------------------------\n";
    echo "Current session data:\n";
    echo "  user_id: " . session('user_id') . "\n";
    echo "  user_name: " . session('user_name') . "\n";
    echo "  user_type: " . session('user_type') . "\n";
    echo "  logged_in: " . (session('logged_in') ? 'true' : 'false') . "\n";
    
    echo "\n=== ALL TESTS COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
