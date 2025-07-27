<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging announcement targeting logic...\n";

try {
    // Get a test student
    $student = App\Models\Student::with('enrollments')->first();
    $enrolledProgramIds = $student->enrollments()
        ->where('enrollment_status', 'approved')
        ->pluck('program_id')
        ->unique()
        ->toArray();
    
    echo "Student details:\n";
    echo "- ID: {$student->student_id}\n";
    echo "- Programs: " . implode(', ', $enrolledProgramIds) . "\n";
    
    $enrollments = $student->enrollments()->where('enrollment_status', 'approved')->get();
    echo "- Batches: " . implode(', ', $enrollments->whereNotNull('batch_id')->pluck('batch_id')->unique()->toArray()) . "\n";
    echo "- Plans: " . implode(', ', $enrollments->pluck('enrollment_type')->unique()->toArray()) . "\n";
    
    echo "\n=== All specific announcements ===\n";
    $specificAnnouncements = App\Models\Announcement::where('target_scope', 'specific')
        ->where('is_active', true)
        ->where('is_published', true)
        ->get();
    
    foreach ($specificAnnouncements as $ann) {
        echo "Announcement: {$ann->title}\n";
        echo "- target_users: " . json_encode($ann->target_users) . " (type: " . gettype($ann->target_users) . ")\n";
        echo "- target_programs: " . json_encode($ann->target_programs) . " (type: " . gettype($ann->target_programs) . ")\n";
        echo "- target_batches: " . json_encode($ann->target_batches) . " (type: " . gettype($ann->target_batches) . ")\n";
        echo "- target_plans: " . json_encode($ann->target_plans) . " (type: " . gettype($ann->target_plans) . ")\n";
        
        // Test individual conditions
        $userMatches = is_null($ann->target_users) || (is_array($ann->target_users) && in_array('students', $ann->target_users));
        $programMatches = is_null($ann->target_programs) || (!empty(array_intersect($enrolledProgramIds, (array)$ann->target_programs)));
        
        echo "- User matches: " . ($userMatches ? 'YES' : 'NO') . "\n";
        echo "- Program matches: " . ($programMatches ? 'YES' : 'NO') . "\n";
        echo "- Would show to student: " . ($userMatches && $programMatches ? 'YES' : 'NO') . "\n";
        echo "\n";
    }
    
    echo "=== Testing simplified query ===\n";
    
    // Test a very simple query first
    $simpleQuery = App\Models\Announcement::where('target_scope', 'specific')
        ->where('is_active', true)
        ->where('is_published', true)
        ->whereJsonContains('target_users', 'students')
        ->get();
    
    echo "Simple JSON query found: {$simpleQuery->count()} announcements\n";
    
    // Test with specific program targeting
    if (!empty($enrolledProgramIds)) {
        $programQuery = App\Models\Announcement::where('target_scope', 'specific')
            ->where('is_active', true)
            ->where('is_published', true)
            ->whereJsonContains('target_users', 'students')
            ->where(function($q) use ($enrolledProgramIds) {
                $q->whereNull('target_programs');
                foreach ($enrolledProgramIds as $programId) {
                    $q->orWhereJsonContains('target_programs', $programId);
                }
            })
            ->get();
        
        echo "With program filtering: {$programQuery->count()} announcements\n";
        
        foreach ($programQuery as $ann) {
            echo "- {$ann->title} targets programs: " . json_encode($ann->target_programs) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
