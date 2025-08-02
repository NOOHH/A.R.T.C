<?php
// Debug the admin quiz generator route
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request for the admin quiz generator
$request = Request::create('/admin/quiz-generator', 'GET');

try {
    // Set up admin authentication in session
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['logged_in'] = true; 
    $_SESSION['user_type'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_name'] = 'Test Admin';
    
    $response = $kernel->handle($request);
    
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Content Length: " . strlen($response->getContent()) . "\n";
    
    if ($response->getStatusCode() == 302) {
        echo "Redirect Location: " . $response->headers->get('Location') . "\n";
    }
    
    // Check if content contains view content
    $content = $response->getContent();
    if (empty(trim($content))) {
        echo "ERROR: Response content is empty!\n";
    } else {
        echo "Content Preview (first 500 chars):\n";
        echo substr($content, 0, 500) . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
?>
