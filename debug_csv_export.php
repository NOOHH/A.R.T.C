<?php
require_once 'vendor/autoload.php';

// Debug CSV export functionality
use App\Models\Student;
use App\Models\Program;

echo "=== Debug CSV Export Issues ===\n\n";

// Check if we can instantiate the models
try {
    $studentCount = Student::count();
    echo "✓ Student model working, found {$studentCount} students\n";
} catch (Exception $e) {
    echo "✗ Student model error: " . $e->getMessage() . "\n";
}

try {
    $programCount = Program::count();
    echo "✓ Program model working, found {$programCount} programs\n";
} catch (Exception $e) {
    echo "✗ Program model error: " . $e->getMessage() . "\n";
}

// Test the relationship queries
try {
    $studentsWithRelations = Student::with([
        'user', 
        'program', 
        'enrollments' => function($query) {
            $query->with(['program', 'package', 'batch']);
        }
    ])->take(1)->get();
    
    echo "✓ Student relationships query working\n";
    
    if ($studentsWithRelations->count() > 0) {
        $student = $studentsWithRelations->first();
        echo "✓ Found test student: " . ($student->firstname ?? 'Unknown') . "\n";
        echo "✓ Enrollments count: " . $student->enrollments->count() . "\n";
    }
} catch (Exception $e) {
    echo "✗ Student relationships error: " . $e->getMessage() . "\n";
}

// Test CSV writing
try {
    $filename = 'test_export.csv';
    $file = fopen($filename, 'w');
    fputcsv($file, ['Test', 'CSV', 'Headers']);
    fputcsv($file, ['Test', 'Data', 'Row']);
    fclose($file);
    
    if (file_exists($filename)) {
        echo "✓ CSV file creation working\n";
        unlink($filename); // Clean up
    }
} catch (Exception $e) {
    echo "✗ CSV creation error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
