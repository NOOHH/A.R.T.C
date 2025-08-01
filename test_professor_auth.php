<?php
/**
 * Authentication Test - Check if professor session works
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckProfessorAuth;

echo "=== Professor Authentication Test ===\n";

// Start session
Session::start();

// Set professor session data
Session::put('professor_id', 8);
Session::put('logged_in', true);
Session::put('user_role', 'professor');
Session::put('user_type', 'professor');
Session::put('user_id', 8);

echo "✓ Professor session data set\n";
echo "  - professor_id: " . Session::get('professor_id') . "\n";
echo "  - logged_in: " . (Session::get('logged_in') ? 'true' : 'false') . "\n";
echo "  - user_role: " . Session::get('user_role') . "\n";

// Test the authentication middleware
$request = Request::create('/professor/quiz-generator', 'GET');

try {
    $professorAuth = new CheckProfessorAuth();
    
    $response = $professorAuth->handle($request, function ($req) {
        return response('Authentication passed', 200);
    });
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Professor authentication middleware passed\n";
    } else {
        echo "❌ Professor authentication middleware failed\n";
        echo "Response: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Authentication test failed: " . $e->getMessage() . "\n";
}

// Test professor model access
try {
    $professor = \App\Models\Professor::find(8);
    if ($professor) {
        echo "✅ Professor model found\n";
        echo "  - Name: " . $professor->professor_first_name . " " . $professor->professor_last_name . "\n";
        echo "  - Email: " . $professor->professor_email . "\n";
    } else {
        echo "❌ Professor model not found\n";
    }
} catch (Exception $e) {
    echo "❌ Professor model test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Authentication Test Completed ===\n";
