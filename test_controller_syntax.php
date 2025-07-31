<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING SYNTAX ERROR ===\n";

try {
    // Test if we can create the controller
    $controller = new App\Http\Controllers\AdminAnalyticsController();
    echo "✅ Controller instantiated successfully\n";
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

// Test syntax by including the file
echo "\n=== TESTING FILE SYNTAX ===\n";
try {
    $code = file_get_contents('app/Http/Controllers/AdminAnalyticsController.php');
    $result = token_get_all($code);
    echo "✅ PHP syntax is valid\n";
} catch (ParseError $e) {
    echo "❌ Syntax error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
