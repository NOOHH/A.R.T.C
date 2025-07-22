<?php
// Add rejection fields to payments table
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'artc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if columns already exist
    $checkColumns = $pdo->query("SHOW COLUMNS FROM payments LIKE 'rejected_at'");
    if ($checkColumns->rowCount() > 0) {
        echo "Rejection fields already exist in payments table.\n";
        exit;
    }
    
    // Add rejection fields to payments table
    $alterQueries = [
        "ALTER TABLE payments ADD COLUMN rejected_at TIMESTAMP NULL",
        "ALTER TABLE payments ADD COLUMN rejection_reason TEXT NULL", 
        "ALTER TABLE payments ADD COLUMN rejected_fields JSON NULL",
        "ALTER TABLE payments ADD COLUMN resubmitted_at TIMESTAMP NULL",
        "ALTER TABLE payments ADD COLUMN resubmission_count INT DEFAULT 0",
        "ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'paid', 'rejected', 'resubmitted') DEFAULT 'pending'"
    ];
    
    foreach ($alterQueries as $query) {
        try {
            $pdo->exec($query);
            echo "Executed: $query\n";
        } catch (PDOException $e) {
            echo "Error with query '$query': " . $e->getMessage() . "\n";
        }
    }
    
    // Also add to registrations table if it exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'registrations'");
    if ($checkTable->rowCount() > 0) {
        $regQueries = [
            "ALTER TABLE registrations ADD COLUMN rejected_at TIMESTAMP NULL",
            "ALTER TABLE registrations ADD COLUMN rejection_reason TEXT NULL",
            "ALTER TABLE registrations ADD COLUMN rejected_fields JSON NULL",
            "ALTER TABLE registrations ADD COLUMN resubmitted_at TIMESTAMP NULL",
            "ALTER TABLE registrations ADD COLUMN resubmission_count INT DEFAULT 0"
        ];
        
        foreach ($regQueries as $query) {
            try {
                $pdo->exec($query);
                echo "Executed: $query\n";
            } catch (PDOException $e) {
                echo "Error with query '$query': " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "Database schema updated successfully!\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
