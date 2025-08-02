<?php
echo "🔧 SIMPLE ADMIN MODULES DEBUG\n";
echo "=============================\n\n";

// 1. Check if the blade file exists
$adminModulesFile = __DIR__ . '/resources/views/admin/admin-modules/admin-modules.blade.php';
echo "1. CHECKING ADMIN MODULES FILE:\n";
if (file_exists($adminModulesFile)) {
    echo "✅ Admin modules blade file exists\n";
    
    // Look for problematic JavaScript patterns
    $content = file_get_contents($adminModulesFile);
    
    // Check for specific issues
    $problematicPatterns = [
        '/editCourse\(\{\{[^}]*\}\}\)/' => 'editCourse with PHP blade syntax',
        '/deleteCourse\(\{\{[^}]*\}\}\)/' => 'deleteCourse with PHP blade syntax',
        '/\{\{[^}]*\$course[^}]*\}\}/' => 'PHP $course variable in JavaScript',
        '/onclick="[^"]*\{\{[^}]*\}\}[^"]*"/' => 'onclick with PHP blade syntax'
    ];
    
    foreach ($problematicPatterns as $pattern => $description) {
        if (preg_match($pattern, $content)) {
            echo "❌ Found: $description\n";
        } else {
            echo "✅ Clean: $description\n";
        }
    }
    
    // Look for specific lines with issues
    $lines = explode("\n", $content);
    $problemLines = [];
    
    foreach ($lines as $lineNum => $line) {
        if (preg_match('/editCourse\(\{\{.*?\}\}\)|deleteCourse\(\{\{.*?\}\}\)/', $line)) {
            $problemLines[] = ($lineNum + 1) . ": " . trim($line);
        }
    }
    
    if (!empty($problemLines)) {
        echo "\n❌ PROBLEMATIC LINES FOUND:\n";
        foreach ($problemLines as $problemLine) {
            echo "   $problemLine\n";
        }
    } else {
        echo "✅ No problematic lines found\n";
    }
    
} else {
    echo "❌ Admin modules blade file not found\n";
}

// 2. Check Laravel configuration
echo "\n2. CHECKING LARAVEL CONFIG:\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ .env file exists\n";
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'DB_CONNECTION') !== false) {
        echo "✅ Database configuration found in .env\n";
    } else {
        echo "⚠️  No database configuration in .env\n";
    }
} else {
    echo "❌ .env file not found\n";
}

// 3. Check routes file
echo "\n3. CHECKING ROUTES:\n";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    echo "✅ Routes file exists\n";
    $routesContent = file_get_contents($routesFile);
    
    if (strpos($routesContent, '/admin/courses/{id}') !== false) {
        echo "✅ Admin course routes found\n";
    } else {
        echo "❌ Admin course routes not found\n";
    }
    
    if (strpos($routesContent, 'admin.auth') !== false) {
        echo "✅ Admin auth middleware found\n";
    } else {
        echo "❌ Admin auth middleware not found\n";
    }
} else {
    echo "❌ Routes file not found\n";
}

// 4. Check controller
echo "\n4. CHECKING ADMIN COURSE CONTROLLER:\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/AdminCourseController.php';
if (file_exists($controllerFile)) {
    echo "✅ AdminCourseController exists\n";
    $controllerContent = file_get_contents($controllerFile);
    
    $methods = ['show', 'update', 'destroy'];
    foreach ($methods as $method) {
        if (strpos($controllerContent, "public function $method") !== false) {
            echo "✅ Method $method exists\n";
        } else {
            echo "❌ Method $method missing\n";
        }
    }
} else {
    echo "❌ AdminCourseController not found\n";
}

// 5. Check middleware
echo "\n5. CHECKING MIDDLEWARE:\n";
$middlewareFile = __DIR__ . '/app/Http/Middleware/CheckAdminAuth.php';
if (file_exists($middlewareFile)) {
    echo "✅ CheckAdminAuth middleware exists\n";
} else {
    echo "❌ CheckAdminAuth middleware not found\n";
}

echo "\n🏁 SIMPLE DEBUG COMPLETE\n";
