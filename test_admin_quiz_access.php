<?php

// Test direct access to admin quiz generator
echo "Testing Admin Quiz Generator Access...\n";

// Test 1: Check if route exists
try {
    $output = shell_exec('php artisan route:list --name=admin.quiz-generator 2>&1');
    echo "✓ Route exists: " . trim($output) . "\n";
} catch (Exception $e) {
    echo "✗ Route test failed: " . $e->getMessage() . "\n";
}

// Test 2: Check controller existence
if (class_exists('App\Http\Controllers\Admin\QuizGeneratorController')) {
    echo "✓ Controller exists\n";
} else {
    echo "✗ Controller does not exist\n";
}

// Test 3: Check view existence
$viewPath = base_path('resources/views/Quiz Generator/admin/quiz-generator-overhauled.blade.php');
if (file_exists($viewPath)) {
    echo "✓ View file exists\n";
} else {
    echo "✗ View file does not exist: $viewPath\n";
}

// Test 4: Check middleware
if (file_exists(app_path('Http/Middleware/AdminDirectorAuth.php'))) {
    echo "✓ Middleware file exists\n";
} else {
    echo "✗ Middleware file does not exist\n";
}

// Test 5: Test HTTP request
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/admin/quiz-generator");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: TestBot/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "HTTP Response Code: $httpCode\n";
    if ($redirectUrl) {
        echo "Redirected to: $redirectUrl\n";
    }
    
    if ($httpCode == 200) {
        echo "✓ Page accessible\n";
    } elseif ($httpCode == 302) {
        echo "⚠ Page redirects (likely auth required)\n";
    } else {
        echo "✗ Page not accessible\n";
    }
} catch (Exception $e) {
    echo "✗ HTTP test failed: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
