<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Student;

echo "=== DEBUGGING QUIZ RESULTS ERROR ===" . PHP_EOL;

// Test the specific attempt that's causing the error
$attemptId = 3;
echo "Testing attempt ID: $attemptId" . PHP_EOL;

// Get attempt with relationships
$attempt = QuizAttempt::with(['quiz.questions', 'student'])->find($attemptId);

if (!$attempt) {
    echo "❌ Attempt not found!" . PHP_EOL;
    exit;
}

echo "✅ Attempt found:" . PHP_EOL;
echo "  - Attempt ID: " . $attempt->attempt_id . PHP_EOL;
echo "  - Quiz ID: " . $attempt->quiz_id . PHP_EOL;
echo "  - Student ID: " . $attempt->student_id . PHP_EOL;
echo "  - Status: " . $attempt->status . PHP_EOL;
echo "  - Score: " . $attempt->score . PHP_EOL;

// Check quiz relationship
if ($attempt->quiz) {
    echo "✅ Quiz relationship loaded:" . PHP_EOL;
    echo "  - Quiz Title: " . $attempt->quiz->quiz_title . PHP_EOL;
    echo "  - Allow Retakes: " . ($attempt->quiz->allow_retakes ? 'Yes' : 'No') . PHP_EOL;
    echo "  - Max Attempts: " . ($attempt->quiz->max_attempts ?? 'Unlimited') . PHP_EOL;
} else {
    echo "❌ Quiz relationship not loaded!" . PHP_EOL;
    
    // Try to get quiz directly
    $quiz = Quiz::find($attempt->quiz_id);
    if ($quiz) {
        echo "✅ Quiz found directly: " . $quiz->quiz_title . PHP_EOL;
    } else {
        echo "❌ Quiz not found in database!" . PHP_EOL;
    }
}

// Check student relationship
if ($attempt->student) {
    echo "✅ Student relationship loaded:" . PHP_EOL;
    echo "  - Student Name: " . $attempt->student->firstname . ' ' . $attempt->student->lastname . PHP_EOL;
} else {
    echo "❌ Student relationship not loaded!" . PHP_EOL;
    
    // Try to get student directly
    $student = Student::find($attempt->student_id);
    if ($student) {
        echo "✅ Student found directly: " . $student->firstname . ' ' . $student->lastname . PHP_EOL;
    } else {
        echo "❌ Student not found in database!" . PHP_EOL;
    }
}

// Test the retake logic that was causing the error
if ($attempt->quiz) {
    $quiz = $attempt->quiz;
    $student = $attempt->student;
    
    echo PHP_EOL . "=== TESTING RETAKE LOGIC ===" . PHP_EOL;
    
    // New safe logic
    $canRetake = $quiz->allow_retakes || 
                !isset($quiz->max_attempts) || 
                $quiz->max_attempts == 0;
    
    if (!$canRetake && isset($quiz->max_attempts) && $quiz->max_attempts > 0) {
        $completedAttempts = QuizAttempt::where('quiz_id', $quiz->quiz_id)
            ->where('student_id', $student->student_id)
            ->where('status', 'completed')
            ->count();
        $canRetake = $completedAttempts < $quiz->max_attempts;
        
        echo "Max attempts check:" . PHP_EOL;
        echo "  - Max allowed: " . $quiz->max_attempts . PHP_EOL;
        echo "  - Completed: " . $completedAttempts . PHP_EOL;
    }
    
    echo "Can retake: " . ($canRetake ? 'Yes' : 'No') . PHP_EOL;
}

// Test ContentItem lookup
echo PHP_EOL . "=== TESTING CONTENT LOOKUP ===" . PHP_EOL;
if ($attempt->quiz) {
    $content = \App\Models\ContentItem::where('content_type', 'quiz')
        ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [$attempt->quiz->quiz_id])
        ->first();
    
    if ($content) {
        echo "✅ Content item found: ID " . $content->id . PHP_EOL;
    } else {
        echo "❌ Content item not found for quiz" . PHP_EOL;
    }
}

echo PHP_EOL . "=== DEBUG COMPLETE ===" . PHP_EOL;
