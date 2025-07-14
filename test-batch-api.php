<?php
echo "Testing batch endpoint...\n";

try {
    require_once 'vendor/autoload.php';
    echo "Autoload included\n";
    
    $app = require_once 'bootstrap/app.php';
    echo "App bootstrapped\n";
    
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
    echo "Laravel bootstrapped\n";
    
    // Create a test request
    $request = new Illuminate\Http\Request(['program_id' => 1]);
    echo "Request created with program_id = 1\n";
    
    // Test the controller
    $controller = new App\Http\Controllers\StudentRegistrationController();
    echo "Controller instantiated\n";
    
    $response = $controller->getBatchesByProgram($request);
    echo "Response received\n";
    echo "Response content: " . $response->getContent() . "\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
