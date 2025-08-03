<?php
/**
 * COMPREHENSIVE ADMIN REGISTRATION SYSTEM TEST
 * Testing all components: Database, Routes, Controller, JavaScript, Authentication
 */

echo "<h1>COMPREHENSIVE ADMIN REGISTRATION SYSTEM TEST</h1>\n";
echo "<h2>Test Started: " . date('Y-m-d H:i:s') . "</h2>\n\n";

// Include Laravel bootstrap
require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "✅ LARAVEL BOOTSTRAP: Success\n\n";
} catch (Exception $e) {
    echo "❌ LARAVEL BOOTSTRAP: Failed - " . $e->getMessage() . "\n\n";
}

// Test Database Connection
echo "<h3>1. DATABASE CONNECTION TEST</h3>\n";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=" . (getenv('DB_DATABASE') ?: 'artc_db'),
        getenv('DB_USERNAME') ?: 'root',
        getenv('DB_PASSWORD') ?: ''
    );
    echo "✅ Database Connection: Success\n";
    
    // Test tables existence
    $tables = [
        'users',
        'students', 
        'student_registrations',
        'programs',
        'packages',
        'courses'
    ];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table': Exists\n";
        } else {
            echo "❌ Table '$table': Missing\n";
        }
    }
    
    // Test registration data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM student_registrations");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Total Registrations: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Pending Registrations: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Approved Registrations: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'rejected'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Rejected Registrations: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Database Test Failed: " . $e->getMessage() . "\n";
}

echo "\n<h3>2. ROUTES VERIFICATION</h3>\n";

// Check if routes file exists and test routes
if (file_exists('routes/web.php')) {
    $routeContent = file_get_contents('routes/web.php');
    echo "✅ Routes file exists\n";
    
    $requiredRoutes = [
        'admin.student.registration' => 'admin/student-registration',
        'admin.student.registration.approve' => 'approve',
        'admin.student.registration.reject' => 'reject',
        'admin.student.registration.details' => 'details'
    ];
    
    foreach ($requiredRoutes as $routeName => $pattern) {
        if (strpos($routeContent, $pattern) !== false) {
            echo "✅ Route pattern '$pattern': Found\n";
        } else {
            echo "❌ Route pattern '$pattern': Missing\n";
        }
    }
} else {
    echo "❌ Routes file missing\n";
}

echo "\n<h3>3. CONTROLLER VERIFICATION</h3>\n";

// Check AdminController
$controllerPath = 'app/Http/Controllers/AdminController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    echo "✅ AdminController exists\n";
    
    $requiredMethods = [
        'studentRegistration',
        'approveRegistration', 
        'rejectRegistration',
        'getRegistrationDetailsJson'
    ];
    
    foreach ($requiredMethods as $method) {
        if (strpos($controllerContent, "function $method") !== false || 
            strpos($controllerContent, "public function $method") !== false) {
            echo "✅ Method '$method': Found\n";
        } else {
            echo "❌ Method '$method': Missing\n";
        }
    }
} else {
    echo "❌ AdminController missing\n";
}

echo "\n<h3>4. VIEW FILE VERIFICATION</h3>\n";

$viewPath = 'resources/views/admin/admin-student-registration/admin-student-registration.blade.php';
if (file_exists($viewPath)) {
    $viewContent = file_get_contents($viewPath);
    echo "✅ Admin registration view exists\n";
    
    // Check for JavaScript functions
    $requiredFunctions = [
        'viewRegistrationDetails',
        'approveRegistration',
        'rejectRegistration'
    ];
    
    foreach ($requiredFunctions as $function) {
        if (strpos($viewContent, "function $function") !== false) {
            echo "✅ JavaScript function '$function': Found\n";
        } else {
            echo "❌ JavaScript function '$function': Missing\n";
        }
    }
    
    // Check for action buttons
    if (strpos($viewContent, 'onclick="viewRegistrationDetails') !== false) {
        echo "✅ View button onclick: Found\n";
    } else {
        echo "❌ View button onclick: Missing\n";
    }
    
    if (strpos($viewContent, 'onclick="approveRegistration') !== false) {
        echo "✅ Approve button onclick: Found\n";
    } else {
        echo "❌ Approve button onclick: Missing\n";
    }
    
    if (strpos($viewContent, 'onclick="rejectRegistration') !== false) {
        echo "✅ Reject button onclick: Found\n";
    } else {
        echo "❌ Reject button onclick: Missing\n";
    }
    
    // Check for modal
    if (strpos($viewContent, 'id="registrationModal"') !== false) {
        echo "✅ Registration modal: Found\n";
    } else {
        echo "❌ Registration modal: Missing\n";
    }
    
    // Check for CSRF token
    if (strpos($viewContent, '@csrf') !== false || strpos($viewContent, 'csrf_token') !== false) {
        echo "✅ CSRF protection: Found\n";
    } else {
        echo "❌ CSRF protection: Missing\n";
    }
    
} else {
    echo "❌ Admin registration view missing\n";
}

echo "\n<h3>5. AUTHENTICATION CHECK</h3>\n";

// Check middleware and authentication
$middlewarePath = 'app/Http/Middleware';
if (is_dir($middlewarePath)) {
    echo "✅ Middleware directory exists\n";
    
    $files = scandir($middlewarePath);
    foreach ($files as $file) {
        if (strpos($file, 'Admin') !== false || strpos($file, 'Auth') !== false) {
            echo "✅ Auth middleware found: $file\n";
        }
    }
} else {
    echo "❌ Middleware directory missing\n";
}

echo "\n<h3>6. URL TESTING</h3>\n";

// Test if the application is accessible
$baseUrl = 'http://localhost:8000';

function testUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $httpCode, 'response' => $response];
}

$testUrls = [
    'Home' => $baseUrl,
    'Admin Login' => $baseUrl . '/admin/login',
    'Admin Registration' => $baseUrl . '/admin/student-registration'
];

foreach ($testUrls as $name => $url) {
    $result = testUrl($url);
    if ($result['code'] == 200) {
        echo "✅ $name ($url): Accessible (HTTP {$result['code']})\n";
    } else {
        echo "❌ $name ($url): Not accessible (HTTP {$result['code']})\n";
    }
}

echo "\n<h3>7. ERROR LOG CHECK</h3>\n";

$logPath = 'storage/logs/laravel.log';
if (file_exists($logPath)) {
    echo "✅ Laravel log file exists\n";
    
    // Get recent errors (last 50 lines)
    $lines = file($logPath);
    $recentLines = array_slice($lines, -50);
    $errorCount = 0;
    
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
            $errorCount++;
        }
    }
    
    if ($errorCount > 0) {
        echo "⚠️ Recent errors found: $errorCount\n";
        echo "Last few error lines:\n";
        foreach ($recentLines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                echo "  " . trim($line) . "\n";
                break; // Show only the most recent error
            }
        }
    } else {
        echo "✅ No recent errors found\n";
    }
} else {
    echo "⚠️ Laravel log file not found\n";
}

echo "\n<h3>8. CONFIGURATION CHECK</h3>\n";

// Check .env file
if (file_exists('.env')) {
    echo "✅ .env file exists\n";
    
    $envContent = file_get_contents('.env');
    $requiredEnvVars = ['DB_DATABASE', 'DB_USERNAME', 'APP_KEY', 'APP_URL'];
    
    foreach ($requiredEnvVars as $var) {
        if (strpos($envContent, $var . '=') !== false) {
            echo "✅ Environment variable '$var': Set\n";
        } else {
            echo "❌ Environment variable '$var': Missing\n";
        }
    }
} else {
    echo "❌ .env file missing\n";
}

echo "\n<h2>TEST SUMMARY</h2>\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo "\n";
echo "Next steps:\n";
echo "1. Visit http://localhost:8000/admin/student-registration\n";
echo "2. Check browser console for JavaScript errors\n";
echo "3. Test the action buttons functionality\n";
echo "4. Verify modal popups work correctly\n";
echo "\n";
echo "If any issues are found, check the specific areas marked with ❌\n";
?>
