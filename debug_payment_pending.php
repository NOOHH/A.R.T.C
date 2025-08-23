<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/t/draft/test1/admin-student-registration/payment/pending?website=15&preview=true', 'GET');
$app->instance('request', $request);

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() !== 200) {
        $content = $response->getContent();
        echo "Error Content (first 500 chars):\n";
        echo substr($content, 0, 500) . "\n";
        
        // Look for specific Laravel error
        if (preg_match('/([A-Za-z\\\\]+Exception[^:]*: [^<\n]+)/', $content, $matches)) {
            echo "\nError: " . $matches[1] . "\n";
        }
    } else {
        echo "✅ Payment Pending page loaded successfully!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
