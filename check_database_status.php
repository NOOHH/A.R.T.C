<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Database Status:\n";
echo "Registrations: " . DB::table('registrations')->count() . "\n";
echo "Students: " . DB::table('students')->count() . "\n";
echo "Enrollments: " . DB::table('enrollments')->count() . "\n";
echo "Users: " . DB::table('users')->count() . "\n";
echo "Registration Modules: " . DB::table('registration_modules')->count() . "\n";

// Show recent registrations
echo "\nRecent Registrations:\n";
$recent = DB::table('registrations')->orderBy('created_at', 'desc')->limit(5)->get(['registration_id', 'user_id', 'enrollment_type', 'status', 'created_at']);
foreach($recent as $reg) {
    echo "ID: {$reg->registration_id}, User: {$reg->user_id}, Type: {$reg->enrollment_type}, Status: {$reg->status}, Created: {$reg->created_at}\n";
}
