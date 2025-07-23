<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\DB;

// Insert sample data
try {
    $sampleData = [
        'student_id' => '2025-07-00005',
        'module_id' => 40,
        'program_id' => 32,
        'files' => json_encode([
            [
                'original_name' => 'assignment1.pdf',
                'path' => 'assignments/sample1.pdf',
                'size' => 1024000,
                'type' => 'application/pdf'
            ]
        ]),
        'comments' => 'Here is my completed assignment for this module.',
        'submitted_at' => now(),
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now()
    ];

    DB::table('assignment_submissions')->insert($sampleData);

    // Insert a graded submission
    $gradedData = [
        'student_id' => '2025-07-00005',
        'module_id' => 41,
        'program_id' => 32,
        'files' => json_encode([
            [
                'original_name' => 'project_report.docx',
                'path' => 'assignments/sample2.docx',
                'size' => 2048000,
                'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]
        ]),
        'comments' => 'Project report with detailed analysis.',
        'submitted_at' => now()->subDays(2),
        'status' => 'graded',
        'grade' => 85.5,
        'feedback' => 'Excellent work! Your analysis is thorough and well-structured.',
        'graded_at' => now()->subHours(4),
        'graded_by' => 1,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subHours(4)
    ];

    DB::table('assignment_submissions')->insert($gradedData);

    echo "Sample assignment submissions created successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
