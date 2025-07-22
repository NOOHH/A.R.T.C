<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Courses Table Structure Check ===\n";

// Get table structure
$columns = DB::select("DESCRIBE courses");
echo "Courses table columns:\n";
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

// Get first few records
echo "\nFirst 3 course records:\n";
$courses = DB::table('courses')->limit(3)->get();
foreach ($courses as $course) {
    echo "Course data: " . json_encode($course, JSON_PRETTY_PRINT) . "\n\n";
}
?>
