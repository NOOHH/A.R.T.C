<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Quiz table structure:\n";
$columns = \Illuminate\Support\Facades\DB::select('DESCRIBE quizzes');
foreach($columns as $col) {
    echo $col->Field . ' (' . $col->Type . ')' . "\n";
}

echo "\nQuiz Questions table structure:\n";
$columns = \Illuminate\Support\Facades\DB::select('DESCRIBE quiz_questions');
foreach($columns as $col) {
    echo $col->Field . ' (' . $col->Type . ')' . "\n";
}
