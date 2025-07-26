<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Courses Table Structure ===\n";
$columns = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM courses');
foreach($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\n=== Enrollment_Courses Table Structure ===\n";
$columns = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM enrollment_courses');
foreach($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\n=== Sample Data Check ===\n";
echo "Sample course record:\n";
$course = Illuminate\Support\Facades\DB::select('SELECT * FROM courses LIMIT 1');
if ($course) {
    print_r($course[0]);
}

echo "\nSample enrollment_course record:\n";
$enrollmentCourse = Illuminate\Support\Facades\DB::select('SELECT * FROM enrollment_courses LIMIT 1');
if ($enrollmentCourse) {
    print_r($enrollmentCourse[0]);
}
