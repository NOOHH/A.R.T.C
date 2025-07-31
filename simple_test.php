<?php
echo "Testing announcement fix...\n";

// Simple database connection test
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc', 'root', '');
    
    echo "Connected to database successfully\n";
    
    // Check directors table
    $stmt = $pdo->query("SELECT directors_id, directors_name, admin_id FROM directors LIMIT 3");
    echo "\nDirectors:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['directors_id']}, Name: {$row['directors_name']}, Admin ID: {$row['admin_id']}\n";
    }
    
    // Check recent announcements
    $stmt = $pdo->query("SELECT announcement_id, title, admin_id, professor_id FROM announcements ORDER BY created_at DESC LIMIT 3");
    echo "\nRecent announcements:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['announcement_id']}, Title: {$row['title']}, Admin ID: {$row['admin_id']}, Professor ID: {$row['professor_id']}\n";
        
        // Check if admin_id exists in admins table
        $adminStmt = $pdo->prepare("SELECT admin_name FROM admins WHERE admin_id = ?");
        $adminStmt->execute([$row['admin_id']]);
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "  Creator (Admin): {$admin['admin_name']}\n";
        } else {
            // Check if it's a director
            $directorStmt = $pdo->prepare("SELECT directors_name FROM directors WHERE directors_id = ?");
            $directorStmt->execute([$row['admin_id']]);
            $director = $directorStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($director) {
                echo "  Creator (Director): {$director['directors_name']}\n";
            } else {
                echo "  Creator: Unknown (ID {$row['admin_id']} not found)\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
