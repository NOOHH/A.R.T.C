<?php
// Simple test that directly calls the controller method

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create request
$request = Illuminate\Http\Request::create('/t/draft/test1/admin/submissions', 'GET', [
    'preview' => 'true',
    'website' => '15',
    't' => time()
]);

try {
    // Set up basic app context
    $app->instance('request', $request);
    
    // Create controller instance
    $controller = new \App\Http\Controllers\AdminController();
    
    echo "Testing previewSubmissions method directly...\n";
    echo "==============================================\n\n";
    
    // Call the method directly
    $result = $controller->previewSubmissions('test1');
    
    if ($result instanceof \Illuminate\View\View) {
        echo "✅ Method returned a View object\n";
        echo "View name: " . $result->name() . "\n";
        
        $data = $result->getData();
        echo "View data keys: " . implode(', ', array_keys($data)) . "\n";
        
        if (isset($data['submissions'])) {
            echo "Submissions count: " . $data['submissions']->count() . "\n";
        }
        
        if (isset($data['programs'])) {
            echo "Programs count: " . $data['programs']->count() . "\n";
        }
        
        if (isset($data['modules'])) {
            echo "Modules count: " . $data['modules']->count() . "\n";
        }
        
    } else {
        echo "✅ Method returned: " . get_class($result) . "\n";
        if (method_exists($result, 'getContent')) {
            $content = $result->getContent();
            echo "Content length: " . strlen($content) . " bytes\n";
            if (strpos($content, 'TEST11') !== false) {
                echo "✅ Contains TEST11 branding\n";
            }
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTest completed at " . date('Y-m-d H:i:s') . "\n";
?>
