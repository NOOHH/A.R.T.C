<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuizAttempt;
use App\Models\Student;

echo "=== DEBUGGING QUIZ SUBMISSION ISSUE ===" . PHP_EOL;

// Check the current attempt status
$attemptId = 4;
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
echo "  - Started at: " . $attempt->started_at . PHP_EOL;
echo "  - Completed at: " . ($attempt->completed_at ?? 'Not completed') . PHP_EOL;

if ($attempt->answers) {
    echo "  - Answers: " . json_encode($attempt->answers) . PHP_EOL;
} else {
    echo "  - Answers: None" . PHP_EOL;
}

// Test the route generation
echo PHP_EOL . "=== TESTING ROUTES ===" . PHP_EOL;

try {
    $submitRoute = route('student.quiz.submit', ['attemptId' => $attemptId]);
    echo "✅ Submit route: " . $submitRoute . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Submit route failed: " . $e->getMessage() . PHP_EOL;
}

try {
    $resultsRoute = route('student.quiz.results', ['attemptId' => $attemptId]);
    echo "✅ Results route: " . $resultsRoute . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ Results route failed: " . $e->getMessage() . PHP_EOL;
}

// Check session data
echo PHP_EOL . "=== SESSION CHECK ===" . PHP_EOL;
session(['user_id' => 1]);

$student = Student::where('user_id', 1)->first();
if ($student) {
    echo "✅ Student found: " . $student->firstname . " " . $student->lastname . PHP_EOL;
    echo "  - Student ID: " . $student->student_id . PHP_EOL;
} else {
    echo "❌ Student not found!" . PHP_EOL;
}

echo PHP_EOL . "=== DEBUG COMPLETE ===" . PHP_EOL;
