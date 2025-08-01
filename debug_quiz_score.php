<?php
/**
 * Debug script to check quiz scores and calculations
 */

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Quiz Score Analysis ===\n\n";

// Get the attempt data for ID 11 (mentioned in the user request)
$attempt = \App\Models\QuizAttempt::find(11);

if (!$attempt) {
    echo "Attempt #11 not found!\n";
    exit;
}

echo "Attempt ID: " . $attempt->attempt_id . "\n";
echo "Quiz ID: " . $attempt->quiz_id . "\n";
echo "Student ID: " . $attempt->student_id . "\n";
echo "Score (Raw): " . $attempt->score . "\n";
echo "Total Questions: " . $attempt->total_questions . "\n";
echo "Correct Answers: " . $attempt->correct_answers . "\n\n";

// Calculate what the score should be
if ($attempt->total_questions > 0) {
    $calculatedScore = ($attempt->correct_answers / $attempt->total_questions) * 100;
    echo "Calculated Score: " . number_format($calculatedScore, 1) . "%\n";
} else {
    echo "Cannot calculate percentage: total_questions is zero.\n";
}

// Check where else this score might be displayed
echo "\n=== Quiz Results Locations ===\n";

// Get all content items that use this quiz
$contentItems = \App\Models\ContentItem::where('content_type', 'quiz')
    ->whereRaw("JSON_EXTRACT(content_data, '$.quiz_id') = ?", [$attempt->quiz_id])
    ->get();

if ($contentItems->count() > 0) {
    echo "This quiz appears in the following content items:\n";
    foreach ($contentItems as $item) {
        echo "- Content ID: " . $item->id . ", Title: " . $item->title . "\n";
    }
} else {
    echo "This quiz is not linked to any content items.\n";
}

// Check if the student has any other attempts for this quiz
$otherAttempts = \App\Models\QuizAttempt::where('quiz_id', $attempt->quiz_id)
    ->where('student_id', $attempt->student_id)
    ->where('attempt_id', '!=', $attempt->attempt_id)
    ->orderBy('created_at', 'desc')
    ->get();

if ($otherAttempts->count() > 0) {
    echo "\nOther attempts for this quiz by this student:\n";
    foreach ($otherAttempts as $otherAttempt) {
        echo "- Attempt ID: " . $otherAttempt->attempt_id;
        echo ", Status: " . $otherAttempt->status;
        echo ", Score: " . $otherAttempt->score;
        echo ", Date: " . $otherAttempt->created_at->format('Y-m-d H:i:s');
        echo "\n";
    }
} else {
    echo "\nNo other attempts for this quiz by this student.\n";
}

// Check if there might be a view with a quiz list
echo "\nSearching for views that might display quiz scores...\n";
$possibleViews = [
    'dashboard/index',
    'dashboard/course',
    'dashboard/profile',
    'quiz/history',
    'content/view',
];

foreach ($possibleViews as $view) {
    $viewPath = base_path("resources/views/student/{$view}.blade.php");
    if (file_exists($viewPath)) {
        echo "Found view: {$view}.blade.php\n";
        
        // Check if the file contains score-related strings
        $viewContent = file_get_contents($viewPath);
        if (strpos($viewContent, 'score') !== false || 
            strpos($viewContent, 'Score') !== false ||
            strpos($viewContent, 'attempt') !== false ||
            strpos($viewContent, '0.0%') !== false) {
            echo "  This view contains score-related content!\n";
        }
    }
}
