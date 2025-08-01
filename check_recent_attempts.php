<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$attempts = \App\Models\QuizAttempt::where('quiz_id', 47)
    ->orderBy('attempt_id', 'desc')
    ->take(5)
    ->get(['attempt_id', 'student_id', 'answers', 'score']);

echo "Recent Quiz Attempts for Quiz ID 47:\n";
echo "=====================================\n";

foreach($attempts as $attempt) {
    echo "Attempt {$attempt->attempt_id}: Student {$attempt->student_id}, Score: {$attempt->score}%, Answers: {$attempt->answers}\n";
}
