<?php

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=artc;charset=utf8mb4",
        "root",
        ""
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
    
    echo "\n=== KEY TABLE ANALYSIS ===\n";
    
    // Check students
    if (in_array('students', $relevantTables)) {
        echo "\n--- STUDENTS ---\n";
        $stmt = $pdo->query("SELECT COUNT(*) FROM students");
        $count = $stmt->fetchColumn();
        echo "Records: {$count}\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM students LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Sample: " . json_encode($sample) . "\n";
        }
    }
    
    // Check enrollments
    if (in_array('enrollments', $relevantTables)) {
        echo "\n--- ENROLLMENTS ---\n";
        $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments");
        $count = $stmt->fetchColumn();
        echo "Records: {$count}\n";
        
        if ($count > 0) {
            // Check table structure
            $stmt = $pdo->query("DESCRIBE enrollments");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Columns: " . implode(', ', $columns) . "\n";
            
            $stmt = $pdo->query("SELECT * FROM enrollments ORDER BY created_at DESC LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Latest enrollment: " . json_encode($sample) . "\n";
            
            // Check completion status
            $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE enrollment_status = 'completed'");
            $completed = $stmt->fetchColumn();
            echo "Completed enrollments: {$completed}\n";
            
            $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments WHERE progress_percentage >= 90");
            $highProgress = $stmt->fetchColumn();
            echo "High progress (90%+): {$highProgress}\n";
        }
    }
    
    // Check board passers
    if (in_array('board_passers', $relevantTables)) {
        echo "\n--- BOARD PASSERS ---\n";
        $stmt = $pdo->query("SELECT COUNT(*) FROM board_passers");
        $count = $stmt->fetchColumn();
        echo "Records: {$count}\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("DESCRIBE board_passers");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Columns: " . implode(', ', $columns) . "\n";
            
            $stmt = $pdo->query("SELECT * FROM board_passers LIMIT 1");
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Sample: " . json_encode($sample) . "\n";
        }
    }
    
    // Look for any completion-related tables
    foreach ($relevantTables as $table) {
        if (str_contains($table, 'completion') || str_contains($table, 'progress')) {
            echo "\n--- {$table} ---\n";
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "Records: {$count}\n";
            
            if ($count > 0) {
                $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 1");
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "Sample: " . json_encode($sample) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";

?>
