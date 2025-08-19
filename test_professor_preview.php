<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Http\Controllers\ProfessorDashboardController;
use Illuminate\Http\Request;

try {
    // Create a test request with preview parameter
    $request = Request::create('/professor/dashboard?preview=true', 'GET');
    
    $controller = new ProfessorDashboardController();
    
    echo "Testing showPreviewDashboard method...\n";
    
    $response = $controller->showPreviewDashboard();
    
    echo "Success! Method executed without errors.\n";
    echo "Response type: " . get_class($response) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
