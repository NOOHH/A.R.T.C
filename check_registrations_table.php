<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Set up the application
$app->bind('request', function() {
    return Illuminate\Http\Request::capture();
});

try {
    // Test database connection
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=artc",
        "root",
        ""
    );
    
    echo "✅ Connected to database 'artc'\n\n";
    
    // Check for registration tables
    $stmt = $pdo->query("SHOW TABLES LIKE '%registration%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Registration-related tables found:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
        
        // Show table structure
        $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        echo "    Columns:\n";
        foreach ($columns as $col) {
            echo "      - {$col['Field']} ({$col['Type']})\n";
        }
        
        // Show record count
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "    Records: $count\n\n";
    }
    
    if (empty($tables)) {
        echo "No registration tables found. Let's check all tables:\n";
        $allTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($allTables as $table) {
            echo "  - $table\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
