<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Admin\BatchEnrollmentController;
use Illuminate\Http\Request;
use App\Models\StudentBatch;

echo "Testing Web Form Update Process\n";
echo "==============================\n\n";

// Simulate the request data that would come from the form
$requestData = [
    'batch_name' => 'Batch Mech 1',
    'program_id' => 35,
    'max_capacity' => 10,
    'registration_deadline' => '2025-07-16',
    'start_date' => '2025-07-23',
    'end_date' => '2025-07-31',
    'batch_status' => 'available',
    'description' => null,
    'professor_ids' => [8] // This is the key part
];

echo "Request data:\n";
print_r($requestData);
echo "\n";

// Create a mock request
$request = new Request();
$request->replace($requestData);

// Test the validation first
echo "Testing validation...\n";
try {
    $validatedData = $request->validate([
        'batch_name' => 'required|string|max:255',
        'program_id' => 'required|exists:programs,program_id',
        'max_capacity' => 'required|integer|min:1',
        'registration_deadline' => 'required|date',
        'start_date' => 'required|date|after:registration_deadline',
        'description' => 'nullable|string',
        'batch_status' => 'nullable|in:pending,available,ongoing,completed,closed',
        'end_date' => 'nullable|date|after:start_date',
        'professor_ids' => 'nullable|array',
        'professor_ids.*' => 'exists:professors,professor_id'
    ]);
    echo "Validation passed!\n";
    print_r($validatedData);
} catch (Exception $e) {
    echo "Validation failed: " . $e->getMessage() . "\n";
}

echo "\nTesting professor sync directly...\n";
$batch = StudentBatch::find(9);
echo "Before sync: " . $batch->professors->count() . " professors\n";

if ($request->has('professor_ids')) {
    echo "professor_ids found in request\n";
    $professorIds = $request->professor_ids ?? [];
    echo "Professor IDs to sync: ";
    print_r($professorIds);
    $batch->professors()->sync($professorIds);
} else {
    echo "professor_ids not found in request\n";
}

$batch->refresh();
echo "After sync: " . $batch->professors->count() . " professors\n";

echo "\nTest completed.\n";
