<?php
// Database structure analysis
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE STRUCTURE ANALYSIS ===\n";
    
    // Check enrollments table structure
    echo "\nENROLLMENTS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE enrollments');
    while ($row = $stmt->fetch()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
    
    // Check programs table structure  
    echo "\nPROGRAMS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE programs');
    while ($row = $stmt->fetch()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
    
    // Check board_passers table structure
    echo "\nBOARD_PASSERS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE board_passers');
    while ($row = $stmt->fetch()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
    
    // Check students table structure
    echo "\nSTUDENTS TABLE:\n";
    $stmt = $pdo->query('DESCRIBE students');
    while ($row = $stmt->fetch()) {
        echo "  {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }
    
    // Sample data analysis
    echo "\n=== SAMPLE DATA ANALYSIS ===\n";
    
    // Check program types
    echo "\nPROGRAM TYPES:\n";
    $stmt = $pdo->query('SELECT DISTINCT program_name FROM programs LIMIT 10');
    while ($row = $stmt->fetch()) {
        echo "  {$row['program_name']}\n";
    }
    
    // Check enrollment types
    echo "\nENROLLMENT DATA SAMPLE:\n";
    $stmt = $pdo->query('SELECT e.*, p.program_name FROM enrollments e LEFT JOIN programs p ON e.program_id = p.program_id LIMIT 5');
    while ($row = $stmt->fetch()) {
        echo "  Student: {$row['student_id']}, Program: {$row['program_name']}, Created: {$row['created_at']}\n";
    }
    
    // Check board passers with student info
    echo "\nBOARD PASSERS WITH STUDENT INFO:\n";
    $stmt = $pdo->query('
        SELECT bp.*, s.student_id, u.user_firstname, u.user_lastname, p.program_name 
        FROM board_passers bp 
        LEFT JOIN students s ON bp.student_id = s.student_id 
        LEFT JOIN users u ON s.user_id = u.user_id 
        LEFT JOIN enrollments e ON s.student_id = e.student_id 
        LEFT JOIN programs p ON e.program_id = p.program_id 
        LIMIT 5
    ');
    while ($row = $stmt->fetch()) {
        echo "  {$row['student_name']} - {$row['board_exam']} - {$row['result']} - Program: {$row['program_name']}\n";
    }
    
} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage();
}
?>
