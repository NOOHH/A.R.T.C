<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== Student Batches Status Summary ===\n";
$batches = \App\Models\StudentBatch::with('program')->get();
echo "Total batches: " . $batches->count() . "\n\n";

foreach(['pending', 'available', 'ongoing', 'completed'] as $status) {
    $count = $batches->where('batch_status', $status)->count();
    echo "$status: $count batches\n";
}

echo "\n=== Recent Batches by Program ===\n";
$programs = \App\Models\Program::with(['batches' => function($query) {
    $query->whereIn('batch_status', ['available', 'ongoing'])->orderBy('start_date', 'desc');
}])->get();

foreach($programs as $program) {
    if($program->batches->count() > 0) {
        echo "\n" . $program->program_name . ":\n";
        foreach($program->batches as $batch) {
            echo "  - {$batch->batch_name} ({$batch->batch_status}) - Start: {$batch->start_date} - Capacity: {$batch->current_capacity}/{$batch->max_capacity}\n";
        }
    }
}

echo "\n=== Testing getBatchesByProgram Endpoint ===\n";
// Test the endpoint for a sample program
$sampleProgram = \App\Models\Program::first();
if ($sampleProgram) {
    echo "Testing with program: {$sampleProgram->program_name} (ID: {$sampleProgram->program_id})\n";
    
    $controller = new \App\Http\Controllers\StudentRegistrationController();
    $request = new \Illuminate\Http\Request(['program_id' => $sampleProgram->program_id]);
    
    $response = $controller->getBatchesByProgram($request);
    $data = json_decode($response->getContent(), true);
    
    echo "API Response batches count: " . count($data) . "\n";
    foreach($data as $batch) {
        echo "  - {$batch['batch_name']} ({$batch['batch_status']}) - Available slots: {$batch['available_slots']}\n";
    }
}
?>
