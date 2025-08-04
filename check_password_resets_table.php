<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking password_resets table...\n";
    
    $stmt = $pdo->query('DESCRIBE password_resets');
    echo "Table exists! Structure:\n";
    while($row = $stmt->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} catch(Exception $e) {
    echo 'Table does not exist or error: ' . $e->getMessage() . "\n";
    echo "Creating password_resets table...\n";
    
    try {
        $pdo->exec("
            CREATE TABLE password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                user_type VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(email),
                INDEX(token)
            )
        ");
        echo "✅ password_resets table created successfully!\n";
    } catch(Exception $e2) {
        echo "❌ Failed to create table: " . $e2->getMessage() . "\n";
    }
}
?>
