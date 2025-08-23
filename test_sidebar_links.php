<?php

require_once 'vendor/autoload.php';

// Test to verify sidebar links are generating correct tenant URLs
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
    echo "Testing sidebar link generation...\n\n";
    
    // Look for tenant-specific URLs in the sidebar
    $expectedLinks = [
        '/t/draft/test1/professor/dashboard' => 'Dashboard',
        '/t/draft/test1/professor/meetings' => 'Meetings', 
        '/t/draft/test1/professor/announcements' => 'Announcements',
        '/t/draft/test1/professor/students' => 'Students',
        '/t/draft/test1/professor/programs' => 'Programs',
        '/t/draft/test1/professor/profile' => 'Profile',
    ];
    
    foreach ($expectedLinks as $expectedUrl => $linkName) {
        if (strpos($content, $expectedUrl) !== false) {
            echo "✅ {$linkName}: Found tenant URL {$expectedUrl}\n";
        } else {
            echo "❌ {$linkName}: Tenant URL {$expectedUrl} NOT found\n";
            
            // Check if it's using old regular URLs instead
            $regularUrl = str_replace('/t/draft/test1', '', $expectedUrl);
            if (strpos($content, $regularUrl) !== false) {
                echo "   ⚠️  Found regular URL {$regularUrl} instead\n";
            }
        }
    }
    
    // Also check for any remaining non-tenant professor URLs that shouldn't be there
    echo "\nChecking for incorrect regular URLs...\n";
    $incorrectPatterns = [
        'href="/professor/dashboard"',
        'href="/professor/meetings"', 
        'href="/professor/announcements"',
        'route(\'professor.dashboard\')',
        'route(\'professor.meetings\')'
    ];
    
    foreach ($incorrectPatterns as $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "⚠️  Found incorrect pattern: {$pattern}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

$kernel->terminate($request, $response ?? null);
