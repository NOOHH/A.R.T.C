<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Understanding the Real Database Structure\n";
echo "========================================\n";

echo "1. Chemistry 'course' is actually a subject with ID 48:\n";
$subject = \Illuminate\Support\Facades\DB::select("SELECT * FROM courses WHERE subject_id = 48")[0];
echo "Subject ID: " . $subject->subject_id . "\n";
echo "Subject Name: " . $subject->subject_name . "\n";
echo "Module ID: " . $subject->module_id . "\n";

echo "\n2. Finding the module for this subject:\n";
$module = \Illuminate\Support\Facades\DB::select("SELECT * FROM modules WHERE modules_id = ?", [$subject->module_id]);
if (!empty($module)) {
    $module = $module[0];
    echo "Module ID: " . $module->modules_id . "\n";
    echo "Module Name: " . $module->module_name . "\n";
    echo "Program ID: " . $module->program_id . "\n";
} else {
    echo "No module found with ID: " . $subject->module_id . "\n";
}

echo "\n3. Finding the program:\n";
if (!empty($module)) {
    $program = \Illuminate\Support\Facades\DB::select("SELECT * FROM programs WHERE id = ?", [$module->program_id]);
    if (!empty($program)) {
        $program = $program[0];
        echo "Program ID: " . $program->id . "\n";
        echo "Program Name: " . $program->program_name . "\n";
    }
}

echo "\n4. Checking professor access to this program:\n";
$professorPrograms = \Illuminate\Support\Facades\DB::select("
    SELECT pp.*, p.program_name 
    FROM professor_program pp 
    LEFT JOIN programs p ON pp.program_id = p.id 
    WHERE pp.professor_id = 8
");

echo "Professor 8 has access to programs:\n";
foreach($professorPrograms as $pp) {
    echo "- Program ID: " . $pp->program_id . " | Name: " . $pp->program_name . "\n";
    if (!empty($module) && $pp->program_id == $module->program_id) {
        echo "  ✓ This matches Chemistry's program!\n";
    }
}

echo "\n5. Content for this subject:\n";
$content = \Illuminate\Support\Facades\DB::select("SELECT * FROM content_items WHERE course_id = 48");
echo "Content items found: " . count($content) . "\n";
foreach($content as $item) {
    echo "- ID: " . $item->id . " | Title: " . $item->content_title . " | Type: " . $item->content_type . " | Active: " . ($item->is_active ? 'Yes' : 'No') . " | Archived: " . ($item->is_archived ? 'Yes' : 'No') . "\n";
}

echo "\nNow I understand the structure!\n";
echo "- courses table contains subjects\n";
echo "- subject_id is the primary key\n";
echo "- module_id links to modules.modules_id\n";
echo "- modules.program_id links to programs.id\n";
echo "- content_items.course_id links to courses.subject_id\n";
