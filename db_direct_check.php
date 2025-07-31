<?php

require_once 'vendor/autoload.php';

// Get database config
$config = require 'config/database.php';
$dbConfig = $config['connections']['mysql'];

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password']
    );
    
    echo "=== DATABASE STRUCTURE ANALYSIS ===\n\n";
    
    // Get all tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "=== RELEVANT TABLES ===\n";
    $relevantTables = [];
    
    foreach ($tables as $tableName) {
        if (str_contains($tableName, 'completion') || 
            str_contains($tableName, 'progress') || 
            str_contains($tableName, 'enrollment') ||
            str_contains($tableName, 'module') ||
            str_contains($tableName, 'course') ||
            str_contains($tableName, 'student') ||
            str_contains($tableName, 'board')) {
            $relevantTables[] = $tableName;
            echo "Found: {$tableName}\n";
        }
    }
    
    echo "\n=== TABLE ANALYSIS ===\n";
    
    foreach ($relevantTables as $tableName) {
        try {
            echo "\n--- {$tableName} ---\n";
            
            // Get table structure
            $stmt = $pdo->query("DESCRIBE {$tableName}");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Columns: ";
            foreach ($columns as $column) {
                echo $column['Field'] . " ";
            }
            echo "\n";
            
            // Get record count
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$tableName}");
            $count = $stmt->fetchColumn();
            echo "Records: {$count}\n";
            
            // If has data, show sample
            if ($count > 0) {
                $stmt = $pdo->query("SELECT * FROM {$tableName} LIMIT 1");
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "Sample record: " . json_encode($sample) . "\n";
            }
            
        } catch (Exception $e) {
            echo "Error accessing {$tableName}: " . $e->getMessage() . "\n";
        }
    }
    
    // Specific checks for your completion issue
    echo "\n=== COMPLETION TRACKING ANALYSIS ===\n";
    
    // Check students table
    if (in_array('students', $relevantTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM students");
        $studentCount = $stmt->fetchColumn();
        echo "Total students: {$studentCount}\n";
    }
    
    // Check enrollments table  
    if (in_array('enrollments', $relevantTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments");
        $enrollmentCount = $stmt->fetchColumn();
        echo "Total enrollments: {$enrollmentCount}\n";
        
        if ($enrollmentCount > 0) {
            // Check completion indicators
            $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_status = 'completed'");
            $completedStatus = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE progress_percentage >= 90");
            $highProgress = $stmt->fetchColumn();
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE certificate_issued = 1");
            $certificateIssued = $stmt->fetchColumn();
            
            echo "Completed status: {$completedStatus}\n";
            echo "High progress (90%+): {$highProgress}\n";
            echo "Certificate issued: {$certificateIssued}\n";
            
            // Show sample high-progress enrollment
            $stmt = $pdo->query("SELECT * FROM enrollments ORDER BY progress_percentage DESC LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Highest progress enrollment: " . json_encode($sample) . "\n";
        }
    }
    
    // Check board passers
    if (in_array('board_passers', $relevantTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM board_passers");
        $passerCount = $stmt->fetchColumn();
        echo "\nBoard passers: {$passerCount}\n";
        
        if ($passerCount > 0) {
            $stmt = $pdo->query("SELECT * FROM board_passers LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Sample board passer: " . json_encode($sample) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

?>
