<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing foreign key relationships...\n";
echo "Programs count: " . \App\Models\Program::count() . "\n";
echo "Modules count: " . \App\Models\Module::count() . "\n";
echo "Courses count: " . \App\Models\Course::count() . "\n";

echo "\nFirst Program: ";
$program = \App\Models\Program::first();
if ($program) {
    echo "ID=" . $program->program_id . ", Name=" . $program->program_name . "\n";
} else {
    echo "None found\n";
}

echo "First Module: ";
$module = \App\Models\Module::first();
if ($module) {
    echo "ID=" . $module->modules_id . ", Name=" . $module->module_name . "\n";
} else {
    echo "None found\n";
}

echo "First Course: ";
$course = \App\Models\Course::first();
if ($course) {
    echo "ID=" . $course->subject_id . ", Name=" . $course->subject_name . "\n";
} else {
    echo "None found\n";
}
?>
