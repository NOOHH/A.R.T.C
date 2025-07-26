<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $program = new App\Models\Program();
    $program->program_name = 'Test Program';
    $program->program_description = 'Test Description';
    $program->created_by_admin_id = 1; // Set a default admin ID
    $program->is_active = 1; // Set default active status
    $program->save();
    echo 'Program created successfully' . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}

// Let's also check the current data counts
echo 'Current data in database:' . PHP_EOL;
echo 'Announcements: ' . DB::table('announcements')->count() . PHP_EOL;
echo 'Assignments: ' . DB::table('assignments')->count() . PHP_EOL;
echo 'Programs: ' . DB::table('programs')->count() . PHP_EOL;
