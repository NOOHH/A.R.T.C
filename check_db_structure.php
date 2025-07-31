<?php
// Check database structure for the ambiguous column error
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE STRUCTURE CHECK ===\n\n";
    
    // Check programs table structure
    echo "1. PROGRAMS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE programs");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    // Check professor_program table structure  
    echo "\n2. PROFESSOR_PROGRAM TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE professor_program");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    // Check assignment_submissions table structure
    echo "\n3. ASSIGNMENT_SUBMISSIONS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE assignment_submissions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    // Check content_items table structure
    echo "\n4. CONTENT_ITEMS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE content_items");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    // Test the problematic query that's causing the error
    echo "\n5. TESTING PROBLEMATIC QUERY:\n";
    echo "Original query (causing error):\n";
    echo "SELECT program_id FROM programs INNER JOIN professor_program ON programs.program_id = professor_program.program_id WHERE professor_program.professor_id = 8\n\n";
    
    echo "Fixed query with table aliases:\n";
    try {
        $stmt = $pdo->prepare("
            SELECT programs.program_id 
            FROM programs 
            INNER JOIN professor_program ON programs.program_id = professor_program.program_id 
            WHERE professor_program.professor_id = ?
        ");
        $stmt->execute([8]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✓ Query executed successfully\n";
        echo "Found " . count($results) . " programs for professor ID 8:\n";
        foreach ($results as $result) {
            echo "  - Program ID: {$result['program_id']}\n";
        }
    } catch (Exception $e) {
        echo "✗ Query failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== CHECK COMPLETE ===\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
