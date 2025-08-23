<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/t/draft/test1/admin/students?website=15&preview=true', 'GET');

// Set up necessary app state
$app->instance('request', $request);

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() !== 200) {
        $content = $response->getContent();
        
        // Look for specific error patterns
        if (strpos($content, 'Object of class stdClass') !== false) {
            echo "❌ stdClass conversion error found!\n";
            
            // Extract the specific error
            if (preg_match('/Object of class stdClass could not be converted to string/', $content)) {
                echo "Route generation error with stdClass objects\n";
            }
        }
        
        if (strpos($content, 'ViewException') !== false) {
            echo "❌ View exception found!\n";
        }
        
        if (strpos($content, 'route') !== false && strpos($content, 'admin.students') !== false) {
            echo "❌ Route error with admin.students found!\n";
        }
        
        // Get first error line
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, 'Exception') !== false || strpos($line, 'Error:') !== false) {
                echo "Error: " . trim(strip_tags($line)) . "\n";
                break;
            }
        }
    } else {
        echo "✅ Students page loaded successfully!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
