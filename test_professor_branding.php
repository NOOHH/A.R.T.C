<?php

require_once 'vendor/autoload.php';

// Test the professor dashboard to check branding
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request for the professor dashboard with tenant context
$request = Illuminate\Http\Request::create(
    '/t/draft/test1/professor/dashboard',
    'GET',
    ['website' => '15', 'preview' => 'true', 't' => '1755931172060']
);

try {
    $response = $kernel->handle($request);
    $content = $response->getContent();
    
    echo "Status: " . $response->getStatusCode() . "\n";
    
    // Check if the branding is applied correctly
    if (strpos($content, 'test2') !== false) {
        echo "✓ Branding customization detected (test2 found in response)\n";
    } else {
        echo "⚠ Default branding might be used (test2 not found)\n";
    }
    
    // Check for specific elements that should contain customized branding
    if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $content, $matches)) {
        echo "Page title: " . trim($matches[1]) . "\n";
    }
    
    if (preg_match('/brand[_-]?name["\']?\s*:\s*["\']([^"\']+)/i', $content, $matches)) {
        echo "Brand name setting: " . $matches[1] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

$kernel->terminate($request, $response ?? null);
