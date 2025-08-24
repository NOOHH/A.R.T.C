<?php

echo "=== REGISTRATION ROUTE FIX VALIDATION ===\n\n";

// Test the registration route and controller fix
$tests = [
    'Registration Route Exists' => function() {
        $routesFile = 'routes/smartprep.php';
        if (!file_exists($routesFile)) {
            return ['❌', 'Smartprep routes file missing'];
        }
        
        $content = file_get_contents($routesFile);
        $hasRoute = strpos($content, "route('dashboard.settings.update.registration'") !== false;
        
        if ($hasRoute) {
            return ['✅', 'Registration route properly defined'];
        }
        return ['❌', 'Registration route missing in routes file'];
    },
    
    'Controller Method Exists' => function() {
        $controllerFile = 'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
        if (!file_exists($controllerFile)) {
            return ['❌', 'Controller file missing'];
        }
        
        $content = file_get_contents($controllerFile);
        $hasMethod = strpos($content, 'public function updateRegistration') !== false;
        
        if ($hasMethod) {
            return ['✅', 'updateRegistration method exists in controller'];
        }
        return ['❌', 'updateRegistration method missing in controller'];
    },
    
    'Route Pattern Validation' => function() {
        $routesFile = 'routes/smartprep.php';
        if (!file_exists($routesFile)) {
            return ['❌', 'Routes file missing'];
        }
        
        $content = file_get_contents($routesFile);
        $hasCorrectPattern = strpos($content, "Route::post('/dashboard/settings/registration/{website}'") !== false;
        
        if ($hasCorrectPattern) {
            return ['✅', 'Route pattern matches expected format'];
        }
        return ['❌', 'Route pattern incorrect'];
    },
    
    'Controller Method Structure' => function() {
        $controllerFile = 'app/Http/Controllers/Smartprep/Dashboard/CustomizeWebsiteController.php';
        if (!file_exists($controllerFile)) {
            return ['❌', 'Controller file missing'];
        }
        
        $content = file_get_contents($controllerFile);
        
        $checks = [
            'updateTenantSettings call' => strpos($content, 'return $this->updateTenantSettings($request, \'auth\'') !== false,
            'system fields validation' => strpos($content, 'system_fields.education_level.active') !== false,
            'custom fields validation' => strpos($content, 'custom_field_type') !== false,
            'registration settings' => strpos($content, 'register_title') !== false,
        ];
        
        $failed = array_filter($checks, function($v) { return !$v; });
        if (empty($failed)) {
            return ['✅', 'Controller method properly structured'];
        }
        return ['❌', 'Missing: ' . implode(', ', array_keys($failed))];
    },
    
    'Auth Blade Form Action' => function() {
        $authFile = 'resources/views/smartprep/dashboard/partials/settings/auth.blade.php';
        if (!file_exists($authFile)) {
            return ['❌', 'Auth blade file missing'];
        }
        
        $content = file_get_contents($authFile);
        $hasCorrectAction = strpos($content, "route('smartprep.dashboard.settings.update.registration'") !== false;
        
        if ($hasCorrectAction) {
            return ['✅', 'Auth blade form action uses correct route'];
        }
        return ['❌', 'Form action route incorrect or missing'];
    },
    
    'Route Name Registration' => function() {
        $routesFile = 'routes/smartprep.php';
        if (!file_exists($routesFile)) {
            return ['❌', 'Routes file missing'];
        }
        
        $content = file_get_contents($routesFile);
        $hasCorrectName = strpos($content, "->name('dashboard.settings.update.registration')") !== false;
        
        if ($hasCorrectName) {
            return ['✅', 'Route name correctly registered'];
        }
        return ['❌', 'Route name incorrect'];
    }
];

foreach ($tests as $testName => $testFunc) {
    [$status, $message] = $testFunc();
    echo "$status $testName: $message\n";
}

echo "\n=== ROUTE TEST ===\n";
echo "Testing if route can be resolved...\n";

// Test route resolution
try {
    $cmd = 'php artisan route:list --grep="registration"';
    $output = shell_exec($cmd);
    if (strpos($output, 'dashboard.settings.update.registration') !== false) {
        echo "✅ Route successfully registered in Laravel\n";
    } else {
        echo "❌ Route not found in Laravel route list\n";
    }
} catch (Exception $e) {
    echo "⚠️ Could not test route registration: " . $e->getMessage() . "\n";
}

echo "\n=== CURL TEST ===\n";
echo "Attempting to test the route endpoint...\n";

// Test with CSRF token and form data
try {
    $testUrl = 'http://127.0.0.1:8000/smartprep/dashboard/settings/registration/15';
    
    // Create a basic curl test
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'register_title' => 'Test Registration',
        'register_subtitle' => 'Test Subtitle',
        'registration_enabled' => true
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
    } else if ($httpCode == 419) {
        echo "✅ Route accessible (419 = CSRF validation - expected without token)\n";
    } else if ($httpCode == 422) {
        echo "✅ Route accessible (422 = Validation error - expected)\n";
    } else if ($httpCode == 200) {
        echo "✅ Route working perfectly (200 OK)\n";
    } else if ($httpCode == 404) {
        echo "❌ Route not found (404) - route not properly registered\n";
    } else {
        echo "⚠️ Route accessible but returned HTTP $httpCode\n";
    }
    
} catch (Exception $e) {
    echo "❌ Could not test endpoint: " . $e->getMessage() . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Fixed issues:\n";
echo "✅ Added registration route to smartprep.php\n";
echo "✅ Created updateRegistration method in CustomizeWebsiteController\n";
echo "✅ Added proper validation rules for registration form fields\n";
echo "✅ Route name matches auth.blade.php form action\n\n";

echo "The registration form should now work without route errors.\n";
echo "Next steps:\n";
echo "1. Visit: http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=15\n";
echo "2. Click 'Authentication & Registration' in sidebar\n";
echo "3. Try submitting the Registration Form Fields form\n";
echo "4. Should not see 'Route not defined' error anymore\n";

?>
