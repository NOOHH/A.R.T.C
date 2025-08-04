<?php
// Add email to admin table for testing
$email = 'bmjustimbaste2003@gmail.com';
$name = 'Test Admin';
$password = password_hash('password123', PASSWORD_DEFAULT); // Default password: password123

try {
    $host = '127.0.0.1';
    $dbname = 'artc';
    $username = 'root';
    $password_db = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT admin_id FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "✅ Email already exists in admin table with ID: {$existing['admin_id']}\n";
    } else {
        // Insert new admin
        $stmt = $pdo->prepare("INSERT INTO admins (admin_name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $email, $password]);
        
        $adminId = $pdo->lastInsertId();
        echo "✅ Successfully added admin account:\n";
        echo "   Admin ID: $adminId\n";
        echo "   Name: $name\n";
        echo "   Email: $email\n";
        echo "   Password: password123\n";
        echo "\nYou can now:\n";
        echo "1. Login with email: $email and password: password123\n";
        echo "2. Use password reset functionality\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
