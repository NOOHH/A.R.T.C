<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Module;
use App\Models\Course;
use App\Models\Program;

echo "Database Status Check:\n";
echo "======================\n";
echo "Programs: " . Program::count() . "\n";
echo "Modules: " . Module::count() . "\n";
echo "Courses: " . Course::count() . "\n";

echo "\nSample Modules:\n";
$modules = Module::with('courses')->take(3)->get();
foreach ($modules as $module) {
    echo "- Module ID: {$module->modules_id}, Name: {$module->module_name}, Program ID: {$module->program_id}, Courses: {$module->courses->count()}\n";
}

echo "\nSample Courses:\n";
$courses = Course::take(3)->get();
foreach ($courses as $course) {
    echo "- Course ID: {$course->subject_id}, Name: {$course->subject_name}\n";
}
