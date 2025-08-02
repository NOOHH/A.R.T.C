<?php
// Simple script to test archived content
require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING ARCHIVED CONTENT ===\n";

echo "Programs:\n";
foreach(\App\Models\Program::all() as $program) {
    echo "- ID: {$program->program_id}, Name: {$program->program_name}\n";
}

echo "\nArchived Content Items:\n";
foreach(\App\Models\ContentItem::where('is_archived', true)->with('course.module.program')->get() as $content) {
    $courseName = $content->course ? $content->course->subject_name : 'N/A';
    $programName = ($content->course && $content->course->module && $content->course->module->program) ? $content->course->module->program->program_name : 'N/A';
    echo "- ID: {$content->id}, Title: {$content->content_title}, Course: {$courseName}, Program: {$programName}\n";
}

echo "\nArchived Modules:\n";
foreach(\App\Models\Module::where('is_archived', true)->with('program')->get() as $module) {
    $programName = $module->program ? $module->program->program_name : 'N/A';
    echo "- ID: {$module->modules_id}, Name: {$module->module_name}, Program: {$programName}\n";
}
