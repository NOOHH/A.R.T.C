<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$quizzes = \App\Models\ContentItem::where('content_type', 'quiz')->get();

echo "Total quizzes: " . $quizzes->count() . "\n";

foreach($quizzes->take(5) as $q) {
    echo "Quiz: {$q->content_title}, Due: " . ($q->due_date ?? 'NULL') . ", Course: {$q->course_id}\n";
}

// Let's add a due date to one quiz for testing
if ($quizzes->count() > 0) {
    $firstQuiz = $quizzes->first();
    echo "\nAdding due date to quiz: {$firstQuiz->content_title}\n";
    
    $firstQuiz->due_date = now()->addDays(7);
    $firstQuiz->save();
    
    echo "Updated quiz due date to: {$firstQuiz->due_date}\n";
} else {
    echo "No quizzes found to update\n";
}
