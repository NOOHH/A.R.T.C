<?php
// Simple database relationship check
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$database = $_ENV['DB_DATABASE'] ?? 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE STRUCTURE VERIFICATION ===\n\n";
    
    // 1. Check assignment_submissions structure
    echo "1. ASSIGNMENT_SUBMISSIONS STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE assignment_submissions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    echo "\n2. CONTENT_ITEMS STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE content_items");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
    
    echo "\n3. RELATIONSHIP VALIDATION:\n";
    echo "Checking assignment_submissions -> content_items relationship:\n";
    
    $stmt = $pdo->query("
        SELECT 
            s.submission_id,
            s.content_id,
            c.id as content_item_id,
            c.content_title,
            CASE 
                WHEN c.id IS NULL THEN 'BROKEN RELATIONSHIP'
                ELSE 'OK'
            END as relationship_status
        FROM assignment_submissions s
        LEFT JOIN content_items c ON s.content_id = c.id
        LIMIT 5
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("  Submission %d -> Content %d (%s) [%s]\n",
            $row['submission_id'],
            $row['content_id'],
            $row['content_title'] ?? 'NULL',
            $row['relationship_status']
        );
    }
    
    echo "\n4. PROFESSOR-PROGRAM RELATIONSHIPS:\n";
    $stmt = $pdo->query("
        SELECT 
            p.professor_id,
            p.first_name,
            p.last_name,
            pr.program_name
        FROM professors p
        JOIN professor_program pp ON p.professor_id = pp.professor_id
        JOIN programs pr ON pp.program_id = pr.program_id
        WHERE p.professor_id = 8
    ");
    
    echo "Professor ID 8 program assignments:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("  %s %s -> %s\n",
            $row['first_name'],
            $row['last_name'], 
            $row['program_name']
        );
    }
    
    echo "\n=== RELATIONSHIPS VERIFIED ===\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
