<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate user session
session_start();
$_SESSION['user_id'] = 112; // bryan justimbaste's user ID
$_SESSION['user_firstname'] = 'bryan';
$_SESSION['user_lastname'] = 'justimbaste';

// Create request with session
$request = \Illuminate\Http\Request::capture();
$request->setLaravelSession(app('session'));

// Set session data for Laravel
app('session')->put('user_id', 112);
app('session')->put('user_firstname', 'bryan');
app('session')->put('user_lastname', 'justimbaste');

$response = $kernel->handle($request);

echo "=== Testing Full Enrollment Form with User Session ===\n";
echo "Session user_id: " . session('user_id') . "\n";

// Test the enrollment form controller
$controller = new \App\Http\Controllers\StudentRegistrationController();

// This should exclude enrolled programs
try {
    $enrollmentType = 'Full';
    $response = $controller->showFullEnrollment($enrollmentType);
    
    echo "Full enrollment form loaded successfully\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    
} catch (Exception $e) {
    echo "Error loading enrollment form: " . $e->getMessage() . "\n";
}

// Test program filtering separately
echo "\n=== Testing Program Filtering Logic ===\n";

$enrolledProgramIds = [];
if (session('user_id')) {
    $enrolledProgramIds = \App\Models\Enrollment::where('user_id', session('user_id'))
        ->where(function($query) {
            $query->whereIn('enrollment_status', ['pending', 'approved', 'completed'])
                  ->orWhere(function($subQuery) {
                      $subQuery->where('payment_status', 'paid');
                  });
        })
        ->pluck('program_id')
        ->unique()
        ->toArray();
}

echo "Enrolled program IDs for user " . session('user_id') . ": " . implode(', ', $enrolledProgramIds) . "\n";

$availablePrograms = \App\Models\Program::where('is_archived', false)
    ->whereNotIn('program_id', $enrolledProgramIds)
    ->get();

echo "Available programs for enrollment:\n";
foreach($availablePrograms as $program) {
    echo "  - {$program->program_name} (ID: {$program->program_id})\n";
}

// Test batch API for Engineer program (should show batches)
echo "\n=== Testing Batch API for Engineer Program ===\n";
$batchRequest = new \Illuminate\Http\Request(['program_id' => 32]);
$batchResponse = $controller->getBatchesByProgram($batchRequest);
$batches = json_decode($batchResponse->getContent(), true);

echo "Engineer batches found: " . count($batches) . "\n";
foreach($batches as $batch) {
    echo "  - {$batch['batch_name']} ({$batch['batch_status']}) - {$batch['available_slots']} slots\n";
}
?>
