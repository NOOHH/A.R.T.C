<?php
// Basic PHP test - if this works, PHP is functioning
echo "<h1>✅ PHP Test Successful</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Test file access
if (file_exists(__DIR__ . '/.env')) {
    echo "<p>✅ .env file accessible</p>";
} else {
    echo "<p>❌ .env file not found</p>";
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<p>✅ Vendor autoload accessible</p>";
} else {
    echo "<p>❌ Vendor autoload not found</p>";
}

if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    echo "<p>✅ Bootstrap app.php accessible</p>";
} else {
    echo "<p>❌ Bootstrap app.php not found</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<p><a href='/diagnose_500_error.php'>Run Full Diagnostic</a></p>";
echo "<p><a href='/test-simple'>Test Laravel Simple Route</a></p>";
echo "<p><a href='/login'>Try Login Again</a></p>";
?>
