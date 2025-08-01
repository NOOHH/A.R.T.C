<?php
// Simple database connection test
$host = '127.0.0.1';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!\n\n";
    
    // Check users table
    echo "=== USERS TABLE (recent entries) ===\n";
    $stmt = $pdo->query("SELECT user_id, user_firstname, user_lastname, email, role FROM users ORDER BY user_id DESC LIMIT 10");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['user_id']}, Name: {$row['user_firstname']} {$row['user_lastname']}, Email: {$row['email']}, Role: {$row['role']}\n";
    }
    
    // Check professors table
    echo "\n=== PROFESSORS TABLE ===\n";
    $stmt = $pdo->query("SELECT professor_id, professor_name, professor_first_name, professor_last_name, professor_email FROM professors WHERE professor_archived = 0");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['professor_id']}, Name: {$row['professor_name']}, First: {$row['professor_first_name']}, Last: {$row['professor_last_name']}, Email: {$row['professor_email']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
