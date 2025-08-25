<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🔍 TESTING USER'S EXACT URL\n";
echo "===========================\n\n";

try {
    // Test the exact URL the user is accessing
    $userUrl = 'http://127.0.0.1:8000/draft/test2/admin/programs?website=16&preview=true&t=1756122045224';
    echo "Testing user's exact URL: {$userUrl}\n\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status Code: {$httpCode}\n";
    
    if ($httpCode === 200) {
        echo "✅ URL is accessible\n";
        
        // Check if the response contains tenant data
        if (strpos($response, 'Nursing') !== false || strpos($response, 'Culinary') !== false || strpos($response, 'Biodiversity') !== false) {
            echo "✅ Response contains tenant database data\n";
        } else {
            echo "❌ Response does not contain tenant database data\n";
        }
        
        if (strpos($response, 'Nursing Review') !== false || strpos($response, 'Medical Technology Review') !== false) {
            echo "❌ Response contains main database data (old data)\n";
        } else {
            echo "✅ Response does not contain main database data\n";
        }
        
    } else {
        echo "❌ URL is not accessible\n";
        
        // Try without query parameters
        $baseUrl = 'http://127.0.0.1:8000/draft/test2/admin/programs';
        echo "\nTrying base URL: {$baseUrl}\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Base URL HTTP Status Code: {$httpCode}\n";
        
        if ($httpCode === 200) {
            echo "✅ Base URL is accessible\n";
        } else {
            echo "❌ Base URL is also not accessible\n";
        }
    }
    
    echo "\n";
    
    // Test route registration
    echo "🔍 CHECKING ROUTE REGISTRATION\n";
    echo "==============================\n";
    
    $route = Route::getRoutes()->getByName('tenant.draft.admin.programs');
    if ($route) {
        echo "✅ Route 'tenant.draft.admin.programs' is registered\n";
        echo "   URI: {$route->uri()}\n";
        echo "   Methods: " . implode(', ', $route->methods()) . "\n";
        
        $controller = $route->getController();
        if ($controller) {
            echo "   Controller: " . get_class($controller) . "\n";
        } else {
            echo "   Controller: None (closure)\n";
        }
    } else {
        echo "❌ Route 'tenant.draft.admin.programs' is NOT registered\n";
    }
    
    echo "\n";
    
    // List all routes that contain 'draft' and 'admin'
    echo "🔍 LISTING ALL DRAFT ADMIN ROUTES\n";
    echo "=================================\n";
    
    $draftRoutes = [];
    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'draft') !== false && strpos($uri, 'admin') !== false) {
            $draftRoutes[] = [
                'uri' => $uri,
                'methods' => $route->methods(),
                'name' => $route->getName()
            ];
        }
    }
    
    foreach ($draftRoutes as $route) {
        echo "• {$route['uri']} (" . implode(', ', $route['methods']) . ") - {$route['name']}\n";
    }
    
    echo "\n";
    echo "🎉 URL TEST COMPLETE!\n";
    echo "=====================\n";

} catch (\Exception $e) {
    echo "❌ Error during URL test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
