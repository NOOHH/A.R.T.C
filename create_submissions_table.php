<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Drop table if exists
    DB::statement('DROP TABLE IF EXISTS assignment_submissions');
    
    // Create table without foreign keys
    DB::statement("
        CREATE TABLE assignment_submissions (
            id bigint unsigned NOT NULL AUTO_INCREMENT,
            student_id varchar(255) NOT NULL,
            module_id bigint unsigned NOT NULL,
            program_id bigint unsigned NOT NULL,
            file_path varchar(255) DEFAULT NULL,
            original_filename varchar(255) DEFAULT NULL,
            files json DEFAULT NULL,
            comments text DEFAULT NULL,
            submitted_at timestamp NOT NULL,
            status enum('pending','submitted','graded','reviewed','returned') NOT NULL DEFAULT 'submitted',
            grade decimal(5,2) DEFAULT NULL,
            feedback text DEFAULT NULL,
            graded_at timestamp NULL DEFAULT NULL,
            graded_by bigint unsigned DEFAULT NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "Table created successfully!\n";
    
    // Insert sample data
    $sampleData = [
        [
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
        ],
        [
            'student_id' => '2025-07-00005',
            'module_id' => 41,
            'program_id' => 32,
            'files' => json_encode([
                [
                    'original_name' => 'project_report.docx',
                    'path' => 'assignments/sample2.docx',
                    'size' => 2048000,
                    'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                [
                    'original_name' => 'source_code.zip',
                    'path' => 'assignments/sample2_code.zip',
                    'size' => 512000,
                    'type' => 'application/zip'
                ]
            ]),
            'comments' => 'Project report with source code files attached.',
            'submitted_at' => now()->subDays(2),
            'status' => 'graded',
            'grade' => 85.5,
            'feedback' => 'Excellent work! Your analysis is thorough and well-structured. The code implementation shows good understanding of the concepts. Consider adding more error handling in future projects.',
            'graded_at' => now()->subHours(4),
            'graded_by' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subHours(4)
        ],
        [
            'student_id' => '2025-07-00005',
            'module_id' => 42,
            'program_id' => 32,
            'files' => json_encode([
                [
                    'original_name' => 'essay.pdf',
                    'path' => 'assignments/sample3.pdf',
                    'size' => 756000,
                    'type' => 'application/pdf'
                ]
            ]),
            'comments' => 'My essay on the assigned topic.',
            'submitted_at' => now()->subDays(1),
            'status' => 'reviewed',
            'feedback' => 'Good effort, but your analysis needs more depth. Please expand on the second section with additional research and resubmit.',
            'graded_at' => now()->subHours(12),
            'graded_by' => 1,
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subHours(12)
        ]
    ];
    
    foreach ($sampleData as $data) {
        DB::table('assignment_submissions')->insert($data);
    }
    
    echo "Sample data inserted successfully!\n";
    echo "Created " . count($sampleData) . " assignment submissions.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
