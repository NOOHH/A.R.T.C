<?php
/**
 * Direct Controller Test
 * Testing the controller method without HTTP layer
 */

echo "🧪 DIRECT CONTROLLER METHOD TEST\n";
echo "==============================\n\n";

// Set up Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

try {
    echo "1️⃣  Creating controller instance...\n";
    $controller = new App\Http\Controllers\AdminController();
    echo "✅ Controller created successfully\n\n";
    
    echo "2️⃣  Calling previewArchivedContent method...\n";
    $result = $controller->previewArchivedContent('test11');
    echo "✅ Method executed successfully\n";
    echo "Result type: " . get_class($result) . "\n";
    
    if ($result instanceof Illuminate\Http\Response) {
        $content = $result->getContent();
        echo "Response content length: " . strlen($content) . " characters\n";
        
        // Check if it's the actual template or login page
        if (strpos($content, 'login') !== false) {
            echo "❌ PROBLEM: Controller method is returning login page\n";
            echo "First 200 chars: " . substr($content, 0, 200) . "\n";
        } else if (strpos($content, 'Archived Content') !== false) {
            echo "✅ SUCCESS: Controller method is returning archived content\n";
            echo "Found 'Archived Content' in response\n";
        } else {
            echo "⚠️  UNKNOWN: Response doesn't match expected patterns\n";
            echo "First 200 chars: " . substr($content, 0, 200) . "\n";
        }
        
        // Check for specific data
        if (strpos($content, 'TEST11') !== false) {
            echo "✅ Found TEST11 branding\n";
        } else {
            echo "❌ No TEST11 branding found\n";
        }
        
        if (strpos($content, 'archivedPrograms') !== false) {
            echo "✅ Found archivedPrograms data\n";
        } else {
            echo "❌ No archivedPrograms data found\n";
        }
        
    } else {
        echo "⚠️  Unexpected result type\n";
        echo "Result: " . print_r($result, true) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n📋 SUMMARY:\n";
echo "This test calls the controller method directly to see if the issue is:\n";
echo "- In the controller method itself\n";
echo "- In the HTTP routing/middleware\n";
echo "- In the template rendering\n";
?>
