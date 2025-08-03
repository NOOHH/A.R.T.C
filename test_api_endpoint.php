<?php
// Quick test of the API endpoint
require_once(__DIR__ . '/vendor/autoload.php');

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to simulate the API call
$request = Illuminate\Http\Request::create('/test/registration/4/details', 'GET');

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
?>
