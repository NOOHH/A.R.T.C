<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // First, check current structure
    $columns = DB::select('SHOW COLUMNS FROM assignment_submissions WHERE Field = "student_id"');
    echo "Current student_id column: " . $columns[0]->Type . "\n";
    
    // Drop table and recreate with correct structure
    DB::statement('DROP TABLE IF EXISTS assignment_submissions');
    
    // Recreate table with correct structure
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
            PRIMARY KEY (id),
            KEY assignment_submissions_module_id_foreign (module_id),
            KEY assignment_submissions_program_id_foreign (program_id),
            CONSTRAINT assignment_submissions_module_id_foreign FOREIGN KEY (module_id) REFERENCES modules (modules_id),
            CONSTRAINT assignment_submissions_program_id_foreign FOREIGN KEY (program_id) REFERENCES programs (program_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "Table recreated successfully!\n";
    
    // Now insert sample data
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
                ]
            ]),
            'comments' => 'Project report with detailed analysis.',
            'submitted_at' => now()->subDays(2),
            'status' => 'graded',
            'grade' => 85.5,
            'feedback' => 'Excellent work! Your analysis is thorough and well-structured. Consider adding more examples in the conclusion section.',
            'graded_at' => now()->subHours(4),
            'graded_by' => 1,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subHours(4)
        ]
    ];
    
    DB::table('assignment_submissions')->insert($sampleData);
    echo "Sample data inserted successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
