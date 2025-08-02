<?php
// Debug authentication test
echo "=== Authentication Debug Test ===\n\n";

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "1. PHP Session Data:\n";
foreach ($_SESSION as $key => $value) {
    echo "   {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
}

echo "\n2. Laravel Session (requires Laravel context):\n";
// This would need Laravel context
