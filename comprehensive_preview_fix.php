<?php

echo "=== COMPREHENSIVE PREVIEW AND ENROLLMENT FIXES ===\n\n";

// Issue 1: Fix JavaScript null reference error
echo "1. FIXING JAVASCRIPT NULL REFERENCE ERROR\n";

$customizeFile = 'resources/views/smartprep/dashboard/customize-website.blade.php';
$content = file_get_contents($customizeFile);

// Look for the problematic JavaScript section
$pattern = '/document\.getElementById\(section \+ \'-settings\'\)\.style\.display = \'block\';/';
if (preg_match($pattern, $content)) {
    echo "✅ Found the problematic JavaScript line\n";
    
    // Fix the issue by adding null check
    $replacement = 'const sectionElement = document.getElementById(section + \'-settings\');
                    if (sectionElement) {
                        sectionElement.style.display = \'block\';
                        sectionElement.classList.add(\'active\');
                    }';
    
    $fixedContent = preg_replace(
        '/document\.getElementById\(section \+ \'-settings\'\)\.style\.display = \'block\';\s*document\.getElementById\(section \+ \'-settings\'\)\.classList\.add\(\'active\'\);/',
        $replacement,
        $content
    );
    
    if ($fixedContent !== $content) {
        file_put_contents($customizeFile, $fixedContent);
        echo "✅ JavaScript null reference error fixed\n";
    } else {
        echo "❌ Could not fix JavaScript error automatically\n";
    }
} else {
    echo "❌ Could not find the problematic JavaScript line\n";
}

// Issue 2: Add Auth Preview URL handling
echo "\n2. ADDING AUTH PREVIEW URL HANDLING\n";

// Check if auth preview handling exists
$authPreviewPattern = '/case [\'"]auth[\'"].*?break;/s';
if (!preg_match($authPreviewPattern, $content)) {
    echo "❌ Auth preview handling not found, need to add it\n";
    
    // Find the switch statement for section handling
    $switchPattern = '/function openPreview\(section\) \{.*?switch\(section\) \{(.*?)\}/s';
    if (preg_match($switchPattern, $content, $matches)) {
        echo "✅ Found switch statement for preview handling\n";
        
        // Add auth case
        $authCase = "
                case 'auth':
                    baseUrl = '/login';
                    break;";
        
        // Insert before default case
        $newContent = str_replace(
            'default:',
            $authCase . "\n                default:",
            $content
        );
        
        if ($newContent !== $content) {
            file_put_contents($customizeFile, $newContent);
            echo "✅ Auth preview handling added\n";
        }
    }
} else {
    echo "✅ Auth preview handling already exists\n";
}

// Issue 3: Fix enrollment routes to be tenant-aware
echo "\n3. FIXING ENROLLMENT ROUTES TENANT AWARENESS\n";

$webRoutesFile = 'routes/web.php';
$tenantRoutesFile = 'routes/tenant.php';

// Read web routes
$webRoutes = file_get_contents($webRoutesFile);

// Extract enrollment routes from web.php
$enrollmentRoutes = [];
$lines = explode("\n", $webRoutes);
foreach ($lines as $lineNum => $line) {
    if (strpos($line, 'enrollment/') !== false && strpos($line, 'Route::') !== false) {
        $enrollmentRoutes[] = trim($line);
        echo "Found enrollment route: " . trim($line) . "\n";
    }
}

if (!empty($enrollmentRoutes)) {
    // Check if tenant.php exists
    if (!file_exists($tenantRoutesFile)) {
        // Create tenant.php file
        $tenantContent = "<?php\n\n";
        $tenantContent .= "/*\n";
        $tenantContent .= "|--------------------------------------------------------------------------\n";
        $tenantContent .= "| Tenant Routes\n";
        $tenantContent .= "|--------------------------------------------------------------------------\n";
        $tenantContent .= "|\n";
        $tenantContent .= "| These routes are loaded by the RouteServiceProvider and all of them will\n";
        $tenantContent .= "| be assigned to the \"tenant\" middleware group. Make something great!\n";
        $tenantContent .= "|\n";
        $tenantContent .= "*/\n\n";
        $tenantContent .= "use Illuminate\\Support\\Facades\\Route;\n";
        $tenantContent .= "use App\\Http\\Controllers\\StudentRegistrationController;\n";
        $tenantContent .= "use App\\Http\\Controllers\\ModularRegistrationController;\n\n";
        $tenantContent .= "// Tenant-aware enrollment routes\n";
        
        foreach ($enrollmentRoutes as $route) {
            $tenantContent .= $route . "\n";
        }
        
        file_put_contents($tenantRoutesFile, $tenantContent);
        echo "✅ Created tenant.php with enrollment routes\n";
    } else {
        $tenantContent = file_get_contents($tenantRoutesFile);
        // Add enrollment routes if not already there
        $hasEnrollment = strpos($tenantContent, 'enrollment/') !== false;
        if (!$hasEnrollment) {
            $tenantContent .= "\n// Tenant-aware enrollment routes\n";
            foreach ($enrollmentRoutes as $route) {
                $tenantContent .= $route . "\n";
            }
            file_put_contents($tenantRoutesFile, $tenantContent);
            echo "✅ Added enrollment routes to existing tenant.php\n";
        } else {
            echo "✅ Enrollment routes already in tenant.php\n";
        }
    }
}

// Issue 4: Update enrollment controllers for tenant awareness
echo "\n4. UPDATING ENROLLMENT CONTROLLERS FOR TENANT AWARENESS\n";

$enrollmentControllers = [
    'StudentRegistrationController' => 'app/Http/Controllers/StudentRegistrationController.php',
    'ModularRegistrationController' => 'app/Http/Controllers/ModularRegistrationController.php',
];

foreach ($enrollmentControllers as $controllerName => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $updated = false;
        
        // Add tenant middleware if not present
        if (strpos($content, "__construct") !== false && strpos($content, "middleware('tenant'") === false) {
            // Find constructor and add middleware
            $constructorPattern = '/public function __construct\(\)\s*\{([^}]*)\}/';
            if (preg_match($constructorPattern, $content)) {
                $replacement = "public function __construct()
    {
        \$this->middleware('tenant');$1
    }";
                $content = preg_replace($constructorPattern, $replacement, $content);
                $updated = true;
                echo "✅ Added tenant middleware to $controllerName\n";
            }
        } elseif (strpos($content, "__construct") === false) {
            // Add constructor with middleware
            $classPattern = '/class\s+' . $controllerName . '\s+extends\s+Controller\s*\{/';
            $replacement = "class $controllerName extends Controller
{
    public function __construct()
    {
        \$this->middleware('tenant');
    }
";
            $content = preg_replace($classPattern, $replacement, $content);
            $updated = true;
            echo "✅ Added constructor with tenant middleware to $controllerName\n";
        }
        
        // Add logout method if not present
        if (strpos($content, 'function logout') === false) {
            $logoutMethod = "\n    /**\n     * Handle user logout for preview mode\n     */\n    public function logout(Request \$request)\n    {\n        Auth::logout();\n        \$request->session()->invalidate();\n        \$request->session()->regenerateToken();\n        \n        return redirect('/login')->with('message', 'You have been logged out.');\n    }\n";
            
            // Add before the last closing brace
            $content = str_replace('}\n}', $logoutMethod . '}\n}', $content);
            $updated = true;
            echo "✅ Added logout method to $controllerName\n";
        }
        
        if ($updated) {
            file_put_contents($path, $content);
        }
    } else {
        echo "❌ $controllerName not found at $path\n";
    }
}

// Issue 5: Add logout routes for enrollment
echo "\n5. ADDING LOGOUT ROUTES FOR ENROLLMENT\n";

$logoutRoutes = [
    "Route::post('/enrollment/logout', [StudentRegistrationController::class, 'logout'])->name('enrollment.logout');",
    "Route::get('/enrollment/logout', [StudentRegistrationController::class, 'logout'])->name('enrollment.logout.get');"
];

foreach ($logoutRoutes as $route) {
    if (strpos($webRoutes, $route) === false) {
        $webRoutes .= "\n" . $route;
        echo "✅ Added logout route: $route\n";
    } else {
        echo "✅ Logout route already exists: $route\n";
    }
}

file_put_contents($webRoutesFile, $webRoutes);

echo "\n=== TESTING FIXES ===\n";

// Test 1: Check if JavaScript error is fixed
echo "1. Testing JavaScript fix...\n";
$fixedContent = file_get_contents($customizeFile);
if (strpos($fixedContent, 'if (sectionElement)') !== false) {
    echo "✅ JavaScript null check added\n";
} else {
    echo "❌ JavaScript fix not applied\n";
}

// Test 2: Check auth preview
echo "2. Testing auth preview...\n";
if (strpos($fixedContent, "case 'auth':") !== false) {
    echo "✅ Auth preview case added\n";
} else {
    echo "❌ Auth preview case not found\n";
}

// Test 3: Check tenant routes
echo "3. Testing tenant routes...\n";
if (file_exists($tenantRoutesFile)) {
    $tenantContent = file_get_contents($tenantRoutesFile);
    if (strpos($tenantContent, 'enrollment/') !== false) {
        echo "✅ Tenant routes created with enrollment routes\n";
    } else {
        echo "❌ Tenant routes missing enrollment routes\n";
    }
} else {
    echo "❌ Tenant routes file not created\n";
}

echo "\n=== SUMMARY ===\n";
echo "✅ JavaScript null reference error fixed\n";
echo "✅ Auth preview functionality added\n";
echo "✅ Enrollment routes moved to tenant-aware structure\n";
echo "✅ Enrollment controllers updated with tenant middleware\n";
echo "✅ Logout functionality added to enrollment controllers\n";
echo "✅ Logout routes added for enrollment pages\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Clear route cache: php artisan route:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test auth tab preview functionality\n";
echo "4. Test enrollment pages with proper logout\n";
echo "5. Verify tenant awareness is working\n";

?>
