<?php
// Simple test for modular enrollment submission
require_once __DIR__ . '/../vendor/autoload.php';

// Start the Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set up the test environment
$app->loadEnvironmentFrom('.env');
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Test data
$testData = [
    'enrollment_type' => 'Modular',
    'package_id' => 12, // level 1 package
    'program_id' => 32, // Engineer program
    'selected_modules' => json_encode([
        ['id' => 40, 'name' => 'Modules 1'],
        ['id' => 41, 'name' => 'Modules 2']
    ]),
    'learning_mode' => 'synchronous',
    'education_level' => 'Undergraduate',
    'user_firstname' => 'Test',
    'user_lastname' => 'User',
    'user_email' => 'test' . time() . '@example.com',
    'email' => 'test' . time() . '@example.com',
    'password' => 'testpassword123',
    'password_confirmation' => 'testpassword123'
];

echo "<h1>Modular Enrollment Test</h1>\n";
echo "<h2>Test Data:</h2>\n";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>\n";

try {
    // Create a request
    $request = new Request();
    $request->replace($testData);
    $request->setMethod('POST');

    // Test the controller
    $controller = new \App\Http\Controllers\StudentRegistrationController();
    
    echo "<h2>Testing submitModularEnrollment...</h2>\n";
    
    $response = $controller->submitModularEnrollment($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "<h2>Response:</h2>\n";
    echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>\n";
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "<h2 style='color: green;'>✅ SUCCESS: Registration completed!</h2>\n";
        
        // Check if user was created
        $user = \App\Models\User::where('email', $testData['email'])->first();
        if ($user) {
            echo "<p>✅ User created with ID: {$user->user_id}</p>\n";
        }
        
        // Check if student was created
        $student = \App\Models\Student::where('user_id', $user->user_id ?? 0)->first();
        if ($student) {
            echo "<p>✅ Student created with ID: {$student->student_id}</p>\n";
        }
        
        // Check if registration was created
        $registration = \App\Models\Registration::where('user_id', $user->user_id ?? 0)->first();
        if ($registration) {
            echo "<p>✅ Registration created with ID: {$registration->registration_id}</p>\n";
        }
        
    } else {
        echo "<h2 style='color: red;'>❌ FAILED: Registration failed</h2>\n";
        if (isset($responseData['errors'])) {
            echo "<h3>Validation Errors:</h3>\n";
            echo "<pre>" . json_encode($responseData['errors'], JSON_PRETTY_PRINT) . "</pre>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERROR: " . $e->getMessage() . "</h2>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "\n<h2>Database Check:</h2>\n";
echo "<p>Users count: " . \App\Models\User::count() . "</p>\n";
echo "<p>Students count: " . \App\Models\Student::count() . "</p>\n";
echo "<p>Registrations count: " . \App\Models\Registration::count() . "</p>\n";
?>
