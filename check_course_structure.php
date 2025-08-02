<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Course Table Structure\n";
echo "===============================\n";

// Check the course table structure
$courses = \Illuminate\Support\Facades\DB::select("DESCRIBE courses");
echo "Course table columns:\n";
foreach($courses as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}

echo "\nChecking actual Chemistry course data:\n";
$course = \App\Models\Course::find(48);
if ($course) {
    echo "Course ID: " . $course->id . "\n";
    echo "Course Name: " . $course->course_name . "\n";
    echo "Course Code: " . ($course->course_code ?? 'N/A') . "\n";
    echo "Description: " . ($course->course_description ?? 'N/A') . "\n";
    echo "Program: " . ($course->program ?? 'N/A') . "\n";
    echo "Created: " . $course->created_at . "\n";
    echo "Updated: " . $course->updated_at . "\n";
    
    // Check all attributes
    echo "\nAll course attributes:\n";
    foreach($course->getAttributes() as $key => $value) {
        echo "- $key: $value\n";
    }
}

echo "\nChecking how courses are related to programs:\n";

// Check the modules table to see the relationship
$modules = \Illuminate\Support\Facades\DB::select("DESCRIBE modules");
echo "Module table columns:\n";
foreach($modules as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}

echo "\nChecking course-program relationship through modules:\n";
$moduleData = \Illuminate\Support\Facades\DB::select("
    SELECT m.id, m.module_name, m.program_id, m.course_id, p.program_name, c.course_name 
    FROM modules m 
    LEFT JOIN programs p ON m.program_id = p.id 
    LEFT JOIN courses c ON m.course_id = c.id 
    WHERE m.course_id = 48
");

foreach($moduleData as $module) {
    echo "Module: " . $module->module_name . "\n";
    echo "Program: " . $module->program_name . " (ID: " . $module->program_id . ")\n";
    echo "Course: " . $module->course_name . " (ID: " . $module->course_id . ")\n";
    echo "---\n";
}

echo "\nAnalysis complete!\n";
