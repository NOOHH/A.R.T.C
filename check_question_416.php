<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$question = \App\Models\QuizQuestion::find(416);
if($question) {
    echo "Question: " . $question->question_text . "\n";
    echo "Options: " . json_encode($question->options, JSON_PRETTY_PRINT) . "\n";
    echo "Correct Answer Index: " . $question->correct_answer . "\n";
    
    $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
    if(isset($options[$question->correct_answer])) {
        echo "Correct Answer Text: " . $options[$question->correct_answer] . "\n";
    }
    
    echo "\nAll options with their indices:\n";
    foreach($options as $index => $option) {
        $letter = chr(65 + $index);
        $status = ($index == $question->correct_answer) ? " ← CORRECT" : "";
        echo "$letter ($index): $option$status\n";
    }
} else {
    echo "Question not found\n";
}
