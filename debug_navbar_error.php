<?php
echo "=== DEBUGGING NAVBAR ERROR ===\n\n";

try {
    // Check the database connection and structure
    $pdo = new PDO("mysql:host=localhost;dbname=smartprep", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected to smartprep database\n\n";
    
    // Check users table structure
    echo "=== USERS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Key']}\n";
    }
    
    // Check if user ID 7 exists
    echo "\n=== CHECKING USER ID 7 ===\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = 7");
    $stmt->execute();
    $user7 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user7) {
        echo "✅ User ID 7 exists: {$user7['name']} ({$user7['email']})\n";
    } else {
        echo "❌ User ID 7 does not exist\n";
    }
    
    // Check all users
    echo "\n=== ALL USERS ===\n";
    $stmt = $pdo->query("SELECT id, name, email, role FROM users LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} - {$row['name']} ({$row['email']}) - Role: {$row['role']}\n";
    }
    
    // Check tenant database
    echo "\n=== CHECKING TENANT DATABASE ===\n";
    $tenantDb = 'artc'; // Assuming tenant database name
    try {
        $tenantPdo = new PDO("mysql:host=localhost;dbname=$tenantDb", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "✅ Connected to tenant database: $tenantDb\n";
        
        // Check if users table exists in tenant database
        $stmt = $tenantPdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->fetch()) {
            echo "✅ Users table exists in tenant database\n";
            
            // Check structure
            $stmt = $tenantPdo->query("DESCRIBE users");
            echo "Tenant users table structure:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "  {$row['Field']} - {$row['Type']}\n";
            }
        } else {
            echo "❌ Users table does not exist in tenant database\n";
        }
    } catch (Exception $e) {
        echo "❌ Could not connect to tenant database: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
