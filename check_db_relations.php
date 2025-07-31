<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE RELATIONS CHECK ===\n\n";

// Check assignment_submissions table structure
echo "1. ASSIGNMENT_SUBMISSIONS TABLE STRUCTURE:\n";
echo "-------------------------------------------\n";
$columns = DB::select("DESCRIBE assignment_submissions");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n2. CONTENT_ITEMS TABLE STRUCTURE:\n";
echo "----------------------------------\n";
$columns = DB::select("DESCRIBE content_items");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n3. PROFESSORS TABLE STRUCTURE:\n";
echo "-------------------------------\n";
$columns = DB::select("DESCRIBE professors");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n4. PROGRAMS TABLE STRUCTURE:\n";
echo "-----------------------------\n";
$columns = DB::select("DESCRIBE programs");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n5. MODULES TABLE STRUCTURE:\n";
echo "---------------------------\n";
$columns = DB::select("DESCRIBE modules");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n6. STUDENTS TABLE STRUCTURE:\n";
echo "----------------------------\n";
$columns = DB::select("DESCRIBE students");
foreach ($columns as $column) {
    echo sprintf("%-20s %-30s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n7. CHECKING FOREIGN KEY RELATIONSHIPS:\n";
echo "======================================\n";

// Check assignment_submissions data with relationships
echo "\nAssignment submissions with content relationships:\n";
$submissions = DB::select("
    SELECT 
        s.submission_id,
        s.content_id,
        c.content_title,
        s.student_id,
        s.status,
        s.grade,
        s.graded_by_professor_id
    FROM assignment_submissions s
    LEFT JOIN content_items c ON s.content_id = c.id
    LIMIT 5
");

foreach ($submissions as $sub) {
    echo sprintf("ID: %d, Content: %s (%d), Student: %s, Status: %s, Grade: %s, Graded by: %s\n",
        $sub->submission_id,
        $sub->content_title ?? 'NULL',
        $sub->content_id,
        $sub->student_id,
        $sub->status,
        $sub->grade ?? 'NULL',
        $sub->graded_by_professor_id ?? 'NULL'
    );
}

echo "\n8. PROFESSOR-PROGRAM RELATIONSHIPS:\n";
echo "===================================\n";
$professorPrograms = DB::select("
    SELECT 
        p.professor_id,
        p.first_name,
        p.last_name,
        pr.program_name
    FROM professors p
    JOIN professor_program pp ON p.professor_id = pp.professor_id
    JOIN programs pr ON pp.program_id = pr.program_id
    LIMIT 10
");

foreach ($professorPrograms as $pp) {
    echo sprintf("Prof ID: %d, Name: %s %s, Program: %s\n",
        $pp->professor_id,
        $pp->first_name,
        $pp->last_name,
        $pp->program_name
    );
}

echo "\n=== DATABASE CHECK COMPLETE ===\n";
?>
