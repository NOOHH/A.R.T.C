<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuizQuestion;

$question = QuizQuestion::where('quiz_id', 38)->first();
if ($question) {
    echo "Question ID: " . $question->id . PHP_EOL;
    echo "Question Type: " . $question->question_type . PHP_EOL;
    echo "Options Type: " . gettype($question->options) . PHP_EOL;
    echo "Options Raw: " . var_export($question->options, true) . PHP_EOL;
    echo "Options JSON: " . json_encode($question->options) . PHP_EOL;
    
    if (is_array($question->options)) {
        echo "Options Array Count: " . count($question->options) . PHP_EOL;
        foreach ($question->options as $index => $option) {
            echo "Option $index Type: " . gettype($option) . PHP_EOL;
            echo "Option $index Value: " . var_export($option, true) . PHP_EOL;
        }
    }
} else {
    echo "No question found for quiz_id 38" . PHP_EOL;
}
