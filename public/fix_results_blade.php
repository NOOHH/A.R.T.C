<?php
// fix_results_blade.php
// This file fixes specific issues in the results.blade.php file

$filePath = __DIR__ . '/../resources/views/student/quiz/results.blade.php';
$content = file_get_contents($filePath);

// Fix the action URL in retakeQuiz function
$content = str_replace(
    'form.action = `/student/quiz//start`;',
    'form.action = `/student/quiz/${quizId}/start`;',
    $content
);

// Write the changes back to the file
file_put_contents($filePath, $content);
echo "Fixed the action URL in the retakeQuiz function";
