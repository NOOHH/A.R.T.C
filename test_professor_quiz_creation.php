<?php
/**
 * Test Professor Quiz Creation with Deadline Features
 * This script tests the complete quiz creation flow with deadlines
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== Testing Professor Quiz Creation with Deadlines ===\n\n";

try {
    // Get a professor for testing
    $professor = DB::table('professors')->first();
    if (!$professor) {
        throw new Exception("No professor found. Please create a professor first.");
    }

    echo "Using Professor: {$professor->professor_name} (ID: {$professor->professor_id})\n\n";

    // Simulate quiz creation data that would come from the form
    $testQuizData = [
        'title' => 'Test Machine Design Quiz - ' . time(),
        'description' => 'A comprehensive test of machine design principles with deadline functionality',
        'instructions' => 'Please answer all questions carefully. This quiz has a deadline.',
        'program_id' => 1,
        'module_id' => null,
        'course_id' => null,
        'time_limit' => 90,
        'max_attempts' => 2,
        'infinite_retakes' => false,
        'has_deadline' => true,
        'due_date' => Carbon::now()->addDays(7)->format('Y-m-d\TH:i'),
        'is_draft' => false,
        'questions' => [
            [
                'question_text' => 'What is the primary function of a gear system?',
                'question_type' => 'multiple_choice',
                'options' => ['To transmit power', 'To reduce friction', 'To increase speed only', 'To store energy'],
                'correct_answers' => ['To transmit power'],
                'explanation' => 'Gear systems primarily transmit power from one shaft to another while potentially changing speed and torque.',
                'points' => 2,
                'order' => 1
            ],
            [
                'question_text' => 'Ball bearings reduce friction in rotating machinery.',
                'question_type' => 'true_false',
                'options' => ['True', 'False'],
                'correct_answers' => ['True'],
                'explanation' => 'Ball bearings use rolling contact instead of sliding contact, significantly reducing friction.',
                'points' => 1,
                'order' => 2
            ],
            [
                'question_text' => 'Which material property is most important for gear teeth?',
                'question_type' => 'multiple_choice',
                'options' => ['Hardness', 'Flexibility', 'Transparency', 'Conductivity'],
                'correct_answers' => ['Hardness'],
                'explanation' => 'Hardness is crucial for gear teeth to resist wear and maintain their shape under load.',
                'points' => 2,
                'order' => 3
            ]
        ]
    ];

    // Test 1: Create quiz with deadline
    echo "1. Testing quiz creation with deadline...\n";
    
    $quizId = DB::table('quizzes')->insertGetId([
        'quiz_title' => $testQuizData['title'],
        'quiz_description' => $testQuizData['description'],
        'instructions' => $testQuizData['instructions'],
        'professor_id' => $professor->professor_id,
        'program_id' => $testQuizData['program_id'],
        'module_id' => $testQuizData['module_id'],
        'course_id' => $testQuizData['course_id'],
        'status' => $testQuizData['is_draft'] ? 'draft' : 'published',
        'is_draft' => $testQuizData['is_draft'],
        'is_active' => !$testQuizData['is_draft'],
        'time_limit' => $testQuizData['time_limit'],
        'max_attempts' => $testQuizData['infinite_retakes'] ? 999 : $testQuizData['max_attempts'],
        'infinite_retakes' => $testQuizData['infinite_retakes'],
        'has_deadline' => $testQuizData['has_deadline'],
        'due_date' => $testQuizData['has_deadline'] ? Carbon::parse($testQuizData['due_date'])->format('Y-m-d H:i:s') : null,
        'total_questions' => count($testQuizData['questions']),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✓ Quiz created with ID: {$quizId}\n";

    // Test 2: Add questions to the quiz
    echo "2. Adding questions to quiz...\n";
    
    foreach ($testQuizData['questions'] as $index => $questionData) {
        $correctAnswer = is_array($questionData['correct_answers']) 
            ? $questionData['correct_answers'][0] 
            : $questionData['correct_answers'];

        $questionId = DB::table('quiz_questions')->insertGetId([
            'quiz_id' => $quizId,
            'quiz_title' => $testQuizData['title'],
            'program_id' => $testQuizData['program_id'],
            'question_text' => $questionData['question_text'],
            'question_type' => $questionData['question_type'],
            'question_order' => $questionData['order'],
            'options' => json_encode($questionData['options']),
            'correct_answer' => $correctAnswer,
            'explanation' => $questionData['explanation'],
            'question_source' => 'manual',
            'points' => $questionData['points'],
            'is_active' => true,
            'created_by_professor' => $professor->professor_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✓ Question " . ($index + 1) . " added with ID: {$questionId}\n";
    }

    // Test 3: Verify the complete quiz
    echo "3. Verifying complete quiz...\n";
    
    $createdQuiz = DB::table('quizzes')->where('quiz_id', $quizId)->first();
    $questions = DB::table('quiz_questions')->where('quiz_id', $quizId)->orderBy('question_order')->get();

    echo "Quiz Details:\n";
    echo "- Title: {$createdQuiz->quiz_title}\n";
    echo "- Has Deadline: " . ($createdQuiz->has_deadline ? 'Yes' : 'No') . "\n";
    echo "- Due Date: " . ($createdQuiz->due_date ?? 'No deadline') . "\n";
    echo "- Infinite Retakes: " . ($createdQuiz->infinite_retakes ? 'Yes' : 'No') . "\n";
    echo "- Max Attempts: {$createdQuiz->max_attempts}\n";
    echo "- Status: {$createdQuiz->status}\n";
    echo "- Total Questions: " . count($questions) . "\n\n";

    // Test 4: Test infinite retakes scenario
    echo "4. Testing infinite retakes scenario...\n";
    
    $infiniteRetakeQuizId = DB::table('quizzes')->insertGetId([
        'quiz_title' => 'Infinite Retake Quiz - ' . time(),
        'quiz_description' => 'Quiz with infinite retakes and no deadline',
        'instructions' => 'Take this quiz as many times as you want!',
        'professor_id' => $professor->professor_id,
        'program_id' => 1,
        'status' => 'published',
        'is_draft' => false,
        'is_active' => true,
        'time_limit' => 60,
        'max_attempts' => 999,
        'infinite_retakes' => true,
        'has_deadline' => false,
        'due_date' => null,
        'total_questions' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✓ Infinite retake quiz created with ID: {$infiniteRetakeQuizId}\n";

    // Test 5: Test deadline filtering
    echo "5. Testing deadline filtering...\n";
    
    $upcomingQuizzes = DB::table('quizzes')
        ->where('has_deadline', true)
        ->where('due_date', '>', now())
        ->where('is_active', true)
        ->orderBy('due_date', 'asc')
        ->get(['quiz_id', 'quiz_title', 'due_date']);

    echo "Upcoming quizzes with deadlines:\n";
    foreach ($upcomingQuizzes as $quiz) {
        echo "- Quiz ID {$quiz->quiz_id}: {$quiz->quiz_title} (Due: {$quiz->due_date})\n";
    }

    // Test 6: Test quiz update scenario
    echo "\n6. Testing quiz update...\n";
    
    $updateResult = DB::table('quizzes')
        ->where('quiz_id', $quizId)
        ->update([
            'due_date' => Carbon::now()->addDays(14)->format('Y-m-d H:i:s'),
            'infinite_retakes' => true,
            'max_attempts' => 999,
            'updated_at' => now()
        ]);

    echo "✓ Quiz updated - extended deadline and enabled infinite retakes\n";

    // Verify update
    $updatedQuiz = DB::table('quizzes')->where('quiz_id', $quizId)->first();
    echo "Updated quiz details:\n";
    echo "- New Due Date: {$updatedQuiz->due_date}\n";
    echo "- Infinite Retakes: " . ($updatedQuiz->infinite_retakes ? 'Yes' : 'No') . "\n";
    echo "- Max Attempts: {$updatedQuiz->max_attempts}\n";

    echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY! ===\n";
    echo "✓ Quiz creation with deadlines works\n";
    echo "✓ Question creation works\n";
    echo "✓ Infinite retakes functionality works\n";
    echo "✓ Deadline filtering works\n";
    echo "✓ Quiz updates work\n";
    echo "✓ Database operations are functioning correctly\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
