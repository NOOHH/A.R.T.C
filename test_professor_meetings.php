<?php

require_once 'vendor/autoload.php';

// Test the professor meetings route directly
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request for the professor meetings route
$request = Illuminate\Http\Request::create(
    '/t/draft/test1/professor/meetings',
    'GET',
    ['website' => '15', 'preview' => 'true', 't' => '1755931172060']
);

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
