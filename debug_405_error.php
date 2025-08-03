<?php
require_once 'vendor/autoload.php';
use Illuminate\Support\Facades\DB;

echo "<h2>🔍 Debugging 405 Method Not Allowed Error</h2>";

try {
    // Test database connection
    $config = require 'config/database.php';
    $pdo = new PDO(
        'mysql:host=localhost;dbname=artc',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Database connection successful<br>";

    // Check if the admin modules route is properly accessible
    echo "<h3>Testing Route Access:</h3>";
    
    // Test base route
    $base_url = 'http://localhost/A.R.T.C/admin/modules';
    echo "Base URL: <a href='$base_url' target='_blank'>$base_url</a><br>";
    
    // Test with program_id parameter
    $test_url = 'http://localhost/A.R.T.C/admin/modules?program_id=40';
    echo "Test URL with program_id: <a href='$test_url' target='_blank'>$test_url</a><br>";
    
    // Check if there are any modules with program_id 40
    $modules = $pdo->query("SELECT COUNT(*) as count FROM modules WHERE program_id = 40")->fetch();
    echo "Modules with program_id 40: " . $modules['count'] . "<br>";
    
    // Check if program_id 40 exists
    $program = $pdo->query("SELECT program_name FROM programs WHERE program_id = 40")->fetch();
    if ($program) {
        echo "Program ID 40 exists: " . $program['program_name'] . "<br>";
    } else {
        echo "❌ Program ID 40 does not exist<br>";
    }
    
    // Check for any middleware or session issues
    echo "<h3>Session Check:</h3>";
    session_start();
    echo "Session ID: " . session_id() . "<br>";
    echo "Logged in: " . (isset($_SESSION['logged_in']) ? 'Yes' : 'No') . "<br>";
    echo "User Role: " . ($_SESSION['user_role'] ?? 'Not set') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
