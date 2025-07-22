<?php
// Add only the missing rejection fields to payments table
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if rejected_fields column exists
    $checkRejectedFields = $pdo->query("SHOW COLUMNS FROM payments LIKE 'rejected_fields'");
    if ($checkRejectedFields->rowCount() == 0) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN rejected_fields JSON NULL AFTER rejected_at");
        echo "Added rejected_fields column to payments table.\n";
    } else {
        echo "rejected_fields column already exists in payments table.\n";
    }
    
    // Check if resubmission_count column exists
    $checkResubmissionCount = $pdo->query("SHOW COLUMNS FROM payments LIKE 'resubmission_count'");
    if ($checkResubmissionCount->rowCount() == 0) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN resubmission_count INT DEFAULT 0 AFTER resubmitted_at");
        echo "Added resubmission_count column to payments table.\n";
    } else {
        echo "resubmission_count column already exists in payments table.\n";
    }
    
    echo "Database schema updated successfully!\n";
    
    // Show current table structure
    echo "\nCurrent payments table structure:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM payments");
    while ($row = $columns->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Default']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
