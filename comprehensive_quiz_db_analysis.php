<?php
/**
 * Comprehensive database analysis for quiz questions and attempts
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DATABASE ANALYSIS FOR QUIZ SCORING ISSUES ===\n\n";

// 1. Check quiz_questions table structure
echo "1. QUIZ_QUESTIONS TABLE STRUCTURE:\n";
$questionColumns = \Illuminate\Support\Facades\Schema::getColumnListing('quiz_questions');
echo "Columns: " . implode(", ", $questionColumns) . "\n\n";

// 2. Check quiz_attempts table structure
echo "2. QUIZ_ATTEMPTS TABLE STRUCTURE:\n";
$attemptColumns = \Illuminate\Support\Facades\Schema::getColumnListing('quiz_attempts');
echo "Columns: " . implode(", ", $attemptColumns) . "\n\n";

// 3. Find the EEE quiz and its questions
echo "3. EEE QUIZ QUESTIONS ANALYSIS:\n";
$eeeQuestions = \App\Models\QuizQuestion::where('question_text', 'EEE')->get();
foreach ($eeeQuestions as $question) {
    echo "Question ID: " . ($question->question_id ?: '(empty)') . "\n";
    echo "Quiz ID: " . $question->quiz_id . "\n";
    echo "Question Text: " . $question->question_text . "\n";
    echo "Question Type: " . $question->question_type . "\n";
    echo "Correct Answer: " . $question->correct_answer . " (type: " . gettype($question->correct_answer) . ")\n";
    echo "Options: " . json_encode($question->options) . "\n";
    echo "Created At: " . $question->created_at . "\n";
    echo "Updated At: " . $question->updated_at . "\n\n";
}

// 4. Check all quiz attempts for quiz ID 47 (EEE quiz)
echo "4. ALL QUIZ ATTEMPTS FOR EEE QUIZ (Quiz ID 47):\n";
$attempts = \App\Models\QuizAttempt::where('quiz_id', 47)->orderBy('created_at', 'desc')->get();
foreach ($attempts as $attempt) {
    echo "Attempt ID: " . $attempt->attempt_id . "\n";
    echo "Student ID: " . $attempt->student_id . "\n";
    echo "Status: " . $attempt->status . "\n";
    echo "Score: " . $attempt->score . "\n";
    echo "Correct Answers: " . $attempt->correct_answers . "\n";
    echo "Total Questions: " . $attempt->total_questions . "\n";
    echo "Answers: " . json_encode($attempt->answers) . "\n";
    echo "Created At: " . $attempt->created_at . "\n";
    echo "Completed At: " . $attempt->completed_at . "\n";
    echo "---\n";
}

// 5. Check for any other questions in quiz 47
echo "\n5. ALL QUESTIONS IN QUIZ 47:\n";
$allQuestions = \App\Models\QuizQuestion::where('quiz_id', 47)->get();
foreach ($allQuestions as $question) {
    echo "Question ID: " . ($question->question_id ?: '(empty)') . "\n";
    echo "Text: " . $question->question_text . "\n";
    echo "Type: " . $question->question_type . "\n";
    echo "Correct Answer: " . $question->correct_answer . "\n";
    echo "---\n";
}

// 6. Direct database query to check raw data
echo "\n6. RAW DATABASE QUERY FOR QUIZ_QUESTIONS:\n";
$rawQuestions = \Illuminate\Support\Facades\DB::select("SELECT * FROM quiz_questions WHERE quiz_id = 47");
foreach ($rawQuestions as $question) {
    echo "Raw data: " . json_encode($question) . "\n";
}

echo "\n7. RAW DATABASE QUERY FOR QUIZ_ATTEMPTS:\n";
$rawAttempts = \Illuminate\Support\Facades\DB::select("SELECT * FROM quiz_attempts WHERE quiz_id = 47 ORDER BY created_at DESC LIMIT 10");
foreach ($rawAttempts as $attempt) {
    echo "Raw data: " . json_encode($attempt) . "\n";
}

// 8. Check if there are any foreign key or constraint issues
echo "\n8. CHECKING FOR DATA INTEGRITY ISSUES:\n";
$orphanedAttempts = \Illuminate\Support\Facades\DB::select("
    SELECT qa.attempt_id, qa.quiz_id 
    FROM quiz_attempts qa 
    LEFT JOIN quiz_questions qq ON qa.quiz_id = qq.quiz_id 
    WHERE qq.quiz_id IS NULL 
    AND qa.quiz_id = 47
");
if (count($orphanedAttempts) > 0) {
    echo "Found orphaned attempts (attempts without matching questions):\n";
    foreach ($orphanedAttempts as $orphan) {
        echo "Attempt ID: " . $orphan->attempt_id . ", Quiz ID: " . $orphan->quiz_id . "\n";
    }
} else {
    echo "No orphaned attempts found.\n";
}

// 9. Check answer format consistency
echo "\n9. ANSWER FORMAT ANALYSIS:\n";
foreach ($attempts as $attempt) {
    if ($attempt->answers) {
        $answers = $attempt->answers;
        echo "Attempt " . $attempt->attempt_id . " answers:\n";
        foreach ($answers as $questionId => $answer) {
            echo "  Question ID: $questionId, Answer: $answer (type: " . gettype($answer) . ")\n";
        }
    }
}
