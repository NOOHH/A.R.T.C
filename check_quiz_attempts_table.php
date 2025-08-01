<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Quiz Attempts table structure:\n";
try {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE quiz_attempts');
    foreach($columns as $col) {
        echo $col->Field . ' (' . $col->Type . ')' . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\nChecking if quiz_attempts table exists:\n";
try {
    $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE 'quiz_attempts'");
    if (count($tables) > 0) {
        echo "quiz_attempts table EXISTS\n";
    } else {
        echo "quiz_attempts table DOES NOT EXIST\n";
    }
} catch (Exception $e) {
    echo 'Error checking table: ' . $e->getMessage() . "\n";
}
