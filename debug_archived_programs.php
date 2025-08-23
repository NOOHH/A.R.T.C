<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/t/draft/test1/admin/programs/archived?website=15&preview=true', 'GET');
$app->instance('request', $request);

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    $content = $response->getContent();
    
    // Check why it's marked as PARTIAL
    $hasDoctype = strpos($content, '<!DOCTYPE html') !== false;
    $hasTitle = strpos($content, '<title>') !== false;
    $hasHtml = strpos($content, '<html') !== false;
    
    echo "Has DOCTYPE: " . ($hasDoctype ? 'Yes' : 'No') . "\n";
    echo "Has Title: " . ($hasTitle ? 'Yes' : 'No') . "\n";
    echo "Has HTML: " . ($hasHtml ? 'Yes' : 'No') . "\n";
    
    if ($hasTitle) {
        preg_match('/<title>([^<]+)<\/title>/', $content, $matches);
        echo "Title: " . ($matches[1] ?? 'Not found') . "\n";
    }
    
    echo "Content length: " . strlen($content) . " chars\n";
    echo "First 200 chars:\n" . substr($content, 0, 200) . "\n";
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
