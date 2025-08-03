<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing registration ID 4 specifically...\n";

try {
    // Test basic registration fetch
    echo "1. Testing basic Registration::find(4):\n";
    $registration = \App\Models\Registration::find(4);
    if ($registration) {
        echo "✅ Found registration: " . $registration->registration_id . " | Status: " . $registration->status . "\n";
    } else {
        echo "❌ Registration not found\n";
    }
    
    echo "\n2. Testing Registration::findOrFail(4):\n";
    $registration = \App\Models\Registration::findOrFail(4);
    echo "✅ Found registration: " . $registration->registration_id . " | Status: " . $registration->status . "\n";
    
    echo "\n3. Testing with relationships:\n";
    $registration = \App\Models\Registration::with(['user', 'program', 'package', 'plan'])->findOrFail(4);
    echo "✅ Found registration with relationships\n";
    echo "- User: " . ($registration->user ? "Found" : "NULL") . "\n";
    echo "- Program: " . ($registration->program ? "Found" : "NULL") . "\n";
    echo "- Package: " . ($registration->package ? "Found" : "NULL") . "\n";
    echo "- Plan: " . ($registration->plan ? "Found" : "NULL") . "\n";
    
    echo "\n4. Testing the exact same call as AdminController:\n";
    $registration = \App\Models\Registration::with(['user', 'program', 'package', 'plan', 'enrollments'])->findOrFail(4);
    echo "✅ AdminController method should work!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
