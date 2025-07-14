<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Manually set session for testing
$session = app('session');
$session->put('user_id', 112);
$session->put('user_firstname', 'bryan');
$session->put('user_lastname', 'justimbaste');

echo "=== Testing Program Filtering with User Session ===\n";
echo "Session user_id: " . $session->get('user_id') . "\n";

// Test program filtering logic (copy from controller)
$enrolledProgramIds = [];
if ($session->get('user_id')) {
    $enrolledProgramIds = \App\Models\Enrollment::where('user_id', $session->get('user_id'))
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

echo "Enrolled program IDs: " . implode(', ', $enrolledProgramIds) . "\n";

$availablePrograms = \App\Models\Program::where('is_archived', false)
    ->whereNotIn('program_id', $enrolledProgramIds)
    ->get();

echo "Available programs for enrollment (" . $availablePrograms->count() . "):\n";
foreach($availablePrograms as $program) {
    echo "  - {$program->program_name} (ID: {$program->program_id})\n";
}

echo "\n=== All Programs in System ===\n";
$allPrograms = \App\Models\Program::where('is_archived', false)->get();
foreach($allPrograms as $program) {
    $isEnrolled = in_array($program->program_id, $enrolledProgramIds);
    echo "  - {$program->program_name} (ID: {$program->program_id}) - " . 
         ($isEnrolled ? "ENROLLED" : "Available") . "\n";
}
?>
