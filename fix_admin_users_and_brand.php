<?php
echo "=== FIXING ADMIN USERS AND NAVBAR BRAND ===\n\n";

try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'smartprep';
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "✅ Connected to SmartPrep database\n\n";
    
    // Step 1: Check current admins table
    echo "=== CURRENT ADMINS TABLE ===\n";
    $stmt = $pdo->prepare("SELECT * FROM admins");
    $stmt->execute();
    $currentAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($currentAdmins)) {
        echo "Admins table is empty\n\n";
    } else {
        foreach ($currentAdmins as $admin) {
            echo "ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}\n";
        }
        echo "\n";
    }
    
    // Step 2: Get admin users from users table
    echo "=== ADMIN USERS IN USERS TABLE ===\n";
    $stmt = $pdo->prepare("SELECT id, name, email, password, created_at, updated_at FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($adminUsers as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n";
    }
    echo "\n";
    
    // Step 3: Move admin users to admins table
    echo "=== MIGRATING ADMIN USERS ===\n";
    foreach ($adminUsers as $user) {
        // Check if admin already exists in admins table
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$user['email']]);
        $existingAdmin = $stmt->fetch();
        
        if (!$existingAdmin) {
            // Insert into admins table
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $user['name'],
                $user['email'], 
                $user['password'],
                $user['created_at'],
                $user['updated_at']
            ]);
            echo "✅ Migrated: {$user['name']} ({$user['email']})\n";
        } else {
            echo "⚠️ Already exists: {$user['name']} ({$user['email']})\n";
        }
    }
    
    // Step 4: Remove admin users from users table
    echo "\n=== REMOVING ADMIN USERS FROM USERS TABLE ===\n";
    $stmt = $pdo->prepare("DELETE FROM users WHERE role = 'admin'");
    $stmt->execute();
    $deletedCount = $stmt->rowCount();
    echo "✅ Removed $deletedCount admin users from users table\n\n";
    
    // Step 5: Check current navbar brand name
    echo "=== NAVBAR BRAND NAME STATUS ===\n";
    $stmt = $pdo->prepare("SELECT * FROM ui_settings WHERE section = 'navbar' AND setting_key = 'brand_name'");
    $stmt->execute();
    $brandSetting = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($brandSetting) {
        echo "Current brand name in database: '{$brandSetting['setting_value']}'\n";
    } else {
        echo "No brand name setting found\n";
    }
    
    // Step 6: Force update navbar brand name to something obvious
    echo "\n=== FORCING NAVBAR BRAND UPDATE ===\n";
    $newBrandName = "🚀 ADMIN FIXED & WORKING! 🚀";
    
    $stmt = $pdo->prepare("INSERT INTO ui_settings (section, setting_key, setting_value, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
    $stmt->execute(['navbar', 'brand_name', $newBrandName, $newBrandName]);
    
    echo "✅ Set brand name to: '$newBrandName'\n\n";
    
    // Step 7: Create a test admin account with known password
    echo "=== CREATING TEST ADMIN ACCOUNT ===\n";
    $testEmail = 'test@admin.com';
    $testPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$testEmail]);
    $existingTestAdmin = $stmt->fetch();
    
    if (!$existingTestAdmin) {
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Test Admin', $testEmail, $testPassword]);
        echo "✅ Created test admin: $testEmail (password: admin123)\n";
    } else {
        // Update password
        $stmt = $pdo->prepare("UPDATE admins SET password = ?, updated_at = NOW() WHERE email = ?");
        $stmt->execute([$testPassword, $testEmail]);
        echo "✅ Updated test admin password: $testEmail (password: admin123)\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "1. ✅ Admin users moved to admins table\n";
    echo "2. ✅ Admin users removed from users table\n";
    echo "3. ✅ Navbar brand name updated\n";
    echo "4. ✅ Test admin account ready\n\n";
    
    echo "=== NEXT STEPS ===\n";
    echo "1. Go to: http://localhost:8000/smartprep/login\n";
    echo "2. Login with: test@admin.com / admin123\n";
    echo "3. Check if you see the new brand name in navbar\n";
    echo "4. Check if authentication shows your name instead of 'Guest'\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
