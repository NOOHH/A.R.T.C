<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user_type column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM password_resets LIKE 'user_type'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec('ALTER TABLE password_resets ADD COLUMN user_type VARCHAR(50) DEFAULT NULL');
        echo "✅ user_type column added successfully\n";
    } else {
        echo "✅ user_type column already exists\n";
    }
    
    // Show final table structure
    echo "\nFinal table structure:\n";
    $stmt = $pdo->query('DESCRIBE password_resets');
    while($row = $stmt->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
