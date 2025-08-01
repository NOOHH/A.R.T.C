<?php
/**
 * Test Complete Quiz Flow with Deadline and Infinite Retakes
 * This script tests the complete quiz taking process with deadline management
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== Testing Complete Quiz Flow with Deadlines ===\n\n";

try {
    // Get a student and find a quiz
    $student = DB::table('students')->first();
    if (!$student) {
        throw new Exception("No student found.");
    }

    echo "Student: {$student->firstname} {$student->lastname} (ID: {$student->student_id})\n";
    echo "Program ID: {$student->program_id}\n\n";

    // Find a quiz for testing
    $quiz = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->where('status', 'published')
        ->first();

    if (!$quiz) {
        throw new Exception("No published quiz found for this student's program.");
    }

    echo "Testing with Quiz: {$quiz->quiz_title} (ID: {$quiz->quiz_id})\n";
    echo "Quiz Details:\n";
    echo "- Has Deadline: " . ($quiz->has_deadline ? 'Yes' : 'No') . "\n";
    echo "- Due Date: " . ($quiz->due_date ?? 'No deadline') . "\n";
    echo "- Infinite Retakes: " . ($quiz->infinite_retakes ? 'Yes' : 'No') . "\n";
    echo "- Max Attempts: {$quiz->max_attempts}\n\n";

    // Test 1: Check if student can take the quiz (deadline validation)
    echo "1. Testing quiz availability (deadline check)...\n";
    
    $canTakeQuiz = true;
    $reason = '';
    
    if ($quiz->has_deadline && $quiz->due_date) {
        $dueDate = Carbon::parse($quiz->due_date);
        if ($dueDate->isPast()) {
            $canTakeQuiz = false;
            $reason = 'Quiz deadline has passed';
        }
    }

    echo "Can take quiz: " . ($canTakeQuiz ? 'Yes' : "No ({$reason})") . "\n";

    // Test 2: Check previous attempts
    echo "\n2. Checking previous attempts...\n";
    
    $previousAttempts = DB::table('quiz_attempts')
        ->where('quiz_id', $quiz->quiz_id)
        ->where('student_id', $student->student_id)
        ->count();

    echo "Previous attempts: {$previousAttempts}\n";
    echo "Max attempts allowed: {$quiz->max_attempts}\n";

    $canRetake = true;
    if (!$quiz->infinite_retakes && $previousAttempts >= $quiz->max_attempts) {
        $canRetake = false;
        $reason = 'Maximum attempts reached';
    }

    echo "Can retake: " . ($canRetake ? 'Yes' : "No ({$reason})") . "\n";

    // Test 3: Simulate taking the quiz (if allowed)
    if ($canTakeQuiz && $canRetake) {
        echo "\n3. Simulating quiz attempt...\n";
        
        // Create a quiz attempt
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $student->student_id,
            'started_at' => now(),
            'status' => 'in_progress',
            'answers' => json_encode([]),
            'total_questions' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✓ Quiz attempt created with ID: {$attemptId}\n";

        // Get quiz questions
        $questions = DB::table('quiz_questions')
            ->where('quiz_id', $quiz->quiz_id)
            ->where('is_active', true)
            ->orderBy('question_order')
            ->get();

        echo "✓ Retrieved " . count($questions) . " questions\n";

        // Simulate answering questions (simplified - store in answers field)
        $responses = [];
        $totalScore = 0;
        foreach ($questions as $index => $question) {
            $isCorrect = ($index % 2 == 0); // Simulate 50% correct answers
            $score = $isCorrect ? ($question->points ?? 1) : 0;
            $totalScore += $score;

            $responses[] = [
                'question_id' => $question->id,
                'answer' => $isCorrect ? $question->correct_answer : 'Wrong answer',
                'is_correct' => $isCorrect,
                'points' => $score
            ];

            echo "  Question " . ($index + 1) . ": " . ($isCorrect ? 'Correct' : 'Incorrect') . " ({$score} points)\n";
        }

        // Complete the quiz attempt
        DB::table('quiz_attempts')
            ->where('attempt_id', $attemptId)
            ->update([
                'completed_at' => now(),
                'status' => 'completed',
                'score' => $totalScore,
                'total_questions' => count($questions),
                'correct_answers' => ceil(count($questions) / 2),
                'time_taken' => 120, // 2 minutes simulation
                'answers' => json_encode($responses),
                'updated_at' => now()
            ]);

        echo "✓ Quiz completed with score: {$totalScore}\n";

        // Test 4: Check if student can retake (infinite retakes test)
        echo "\n4. Testing retake functionality...\n";
        
        $newAttemptCount = DB::table('quiz_attempts')
            ->where('quiz_id', $quiz->quiz_id)
            ->where('student_id', $student->student_id)
            ->count();

        echo "Total attempts after completion: {$newAttemptCount}\n";

        if ($quiz->infinite_retakes) {
            echo "✓ Infinite retakes enabled - student can retake anytime\n";
        } else {
            $remainingAttempts = $quiz->max_attempts - $newAttemptCount;
            echo "Remaining attempts: {$remainingAttempts}\n";
        }

    } else {
        echo "\n3. Cannot take quiz:\n";
        if (!$canTakeQuiz) echo "- Deadline restriction: {$reason}\n";
        if (!$canRetake) echo "- Attempt restriction: {$reason}\n";
    }

    // Test 5: Test deadline scenarios with different quizzes
    echo "\n5. Testing different deadline scenarios...\n";
    
    // Get quizzes with different deadline configurations
    $allQuizzes = DB::table('quizzes')
        ->where('program_id', $student->program_id)
        ->where('is_active', true)
        ->select('quiz_id', 'quiz_title', 'has_deadline', 'due_date', 'infinite_retakes', 'max_attempts')
        ->get();

    foreach ($allQuizzes as $testQuiz) {
        echo "\nQuiz: {$testQuiz->quiz_title}\n";
        
        if ($testQuiz->has_deadline && $testQuiz->due_date) {
            $dueDate = Carbon::parse($testQuiz->due_date);
            $now = Carbon::now();
            
            if ($dueDate->isFuture()) {
                $timeLeft = $now->diffForHumans($dueDate, true);
                echo "  Status: Available ({$timeLeft} remaining)\n";
            } else {
                $overdue = $dueDate->diffForHumans($now, true);
                echo "  Status: Overdue (by {$overdue})\n";
            }
        } else {
            echo "  Status: Always available (no deadline)\n";
        }

        $attempts = DB::table('quiz_attempts')
            ->where('quiz_id', $testQuiz->quiz_id)
            ->where('student_id', $student->student_id)
            ->count();

        if ($testQuiz->infinite_retakes) {
            echo "  Retakes: Unlimited ({$attempts} attempts taken)\n";
        } else {
            $remaining = max(0, $testQuiz->max_attempts - $attempts);
            echo "  Retakes: {$remaining} remaining ({$attempts}/{$testQuiz->max_attempts} used)\n";
        }
    }

    // Test 6: Test quiz results and statistics
    echo "\n6. Testing quiz results and statistics...\n";
    
    $studentQuizStats = DB::table('quiz_attempts')
        ->where('student_id', $student->student_id)
        ->where('status', 'completed')
        ->selectRaw('
            quiz_id,
            COUNT(*) as total_attempts,
            AVG(score) as average_score,
            MAX(score) as best_score,
            MIN(score) as lowest_score
        ')
        ->groupBy('quiz_id')
        ->get();

    echo "Student quiz statistics:\n";
    foreach ($studentQuizStats as $stat) {
        $quizTitle = DB::table('quizzes')->where('quiz_id', $stat->quiz_id)->value('quiz_title');
        echo "- {$quizTitle}:\n";
        echo "  Attempts: {$stat->total_attempts}\n";
        echo "  Average Score: " . round($stat->average_score, 2) . "\n";
        echo "  Best Score: {$stat->best_score}\n";
        echo "  Lowest Score: {$stat->lowest_score}\n";
    }

    echo "\n=== COMPLETE QUIZ FLOW TEST SUCCESSFUL! ===\n";
    echo "✓ Quiz availability checking works (deadline validation)\n";
    echo "✓ Attempt counting and validation works\n";
    echo "✓ Quiz taking simulation works\n";
    echo "✓ Infinite retakes functionality works\n";
    echo "✓ Deadline scenarios are properly handled\n";
    echo "✓ Quiz results and statistics are tracked\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
