<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking student data:\n";
try {
    $student = \App\Models\Student::where('user_id', 1)->first();
    if ($student) {
        echo "Student found:\n";
        echo "user_id: " . $student->user_id . "\n";
        echo "student_id: " . $student->student_id . "\n";
        echo "ID (numeric): " . $student->id . "\n";
        echo "Data type of student_id: " . gettype($student->student_id) . "\n";
    } else {
        echo "No student found with user_id 1\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\nChecking students table structure:\n";
try {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE students');
    foreach($columns as $col) {
        if (strpos($col->Field, 'id') !== false) {
            echo $col->Field . ' (' . $col->Type . ')' . "\n";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
