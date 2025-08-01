<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContentItem;

$content = ContentItem::find(89);
if ($content) {
    echo "Content ID: " . $content->id . PHP_EOL;
    echo "Content Type: " . $content->content_type . PHP_EOL;
    echo "Content Title: " . $content->content_title . PHP_EOL;
    echo "Content Data: " . json_encode($content->content_data) . PHP_EOL;
    
    // Check if it's a quiz type
    if ($content->content_type === 'quiz') {
        echo "This is a quiz content!" . PHP_EOL;
        
        // Try to find associated quiz
        if (isset($content->content_data['quiz_id'])) {
            $quizId = $content->content_data['quiz_id'];
            echo "Associated Quiz ID: " . $quizId . PHP_EOL;
            
            $quiz = \App\Models\Quiz::find($quizId);
            if ($quiz) {
                echo "Quiz Title: " . $quiz->quiz_title . PHP_EOL;
                echo "Quiz Status: " . $quiz->status . PHP_EOL;
                echo "Total Questions: " . $quiz->total_questions . PHP_EOL;
                echo "Time Limit: " . $quiz->time_limit . " minutes" . PHP_EOL;
            }
        }
    }
} else {
    echo "Content with ID 89 not found" . PHP_EOL;
}
