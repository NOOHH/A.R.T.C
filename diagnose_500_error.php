<?php
/**
 * 500 Server Error Diagnostic Tool
 * This will identify the exact cause of the 500 error
 */

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🚨 500 Server Error Diagnostic</h1>";
echo "<pre>";

echo "🔍 DIAGNOSING 500 SERVER ERROR...\n\n";

// Basic PHP info
echo "=== PHP ENVIRONMENT ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";
echo "Error Reporting: " . ini_get('error_reporting') . "\n";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
echo "Log Errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "\n";
echo "Error Log: " . ini_get('error_log') . "\n\n";

// Check critical directories and files
echo "=== FILE SYSTEM CHECK ===\n";
$criticalPaths = [
    'Current Directory' => __DIR__,
    'Storage Directory' => __DIR__ . '/storage',
    'Bootstrap Cache' => __DIR__ . '/bootstrap/cache',
    'Config Cache' => __DIR__ . '/bootstrap/cache/config.php',
    'Routes Cache' => __DIR__ . '/bootstrap/cache/routes-v7.php',
    'Views Cache' => __DIR__ . '/storage/framework/views',
    'Sessions Directory' => __DIR__ . '/storage/framework/sessions',
    'Logs Directory' => __DIR__ . '/storage/logs',
    'Vendor Directory' => __DIR__ . '/vendor',
    'Autoload File' => __DIR__ . '/vendor/autoload.php',
    'Artisan File' => __DIR__ . '/artisan',
    '.env File' => __DIR__ . '/.env'
];

foreach ($criticalPaths as $name => $path) {
    if (file_exists($path)) {
        $perms = is_dir($path) ? 'Dir' : 'File';
        $writable = is_writable($path) ? 'Writable' : 'Read-only';
        echo "✅ $name: $perms, $writable\n";
    } else {
        echo "❌ $name: Missing\n";
    }
}
echo "\n";

// Check for recent error logs
echo "=== ERROR LOGS CHECK ===\n";
$logFiles = [
    __DIR__ . '/storage/logs/laravel.log',
    __DIR__ . '/storage/logs/laravel-' . date('Y-m-d') . '.log',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log'
];

foreach ($logFiles as $logFile) {
    if (file_exists($logFile) && is_readable($logFile)) {
        echo "📄 Found log: $logFile\n";
        
        // Get last few lines of the log
        $lines = file($logFile);
        if ($lines) {
            $recentLines = array_slice($lines, -10);
            echo "Recent entries:\n";
            foreach ($recentLines as $line) {
                if (stripos($line, 'error') !== false || stripos($line, 'fatal') !== false) {
                    echo "  🚨 " . trim($line) . "\n";
                }
            }
            echo "\n";
        }
    }
}

// Test basic Laravel bootstrap
echo "=== LARAVEL BOOTSTRAP TEST ===\n";
try {
    // Try to include the autoloader
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        echo "✅ Autoloader exists\n";
        require_once __DIR__ . '/vendor/autoload.php';
        echo "✅ Autoloader loaded successfully\n";
    } else {
        echo "❌ Vendor autoloader missing!\n";
    }
    
    // Try to load Laravel app
    if (file_exists(__DIR__ . '/bootstrap/app.php')) {
        echo "✅ Bootstrap app.php exists\n";
        $app = require_once __DIR__ . '/bootstrap/app.php';
        echo "✅ Laravel app bootstrapped successfully\n";
        
        // Test basic config
        if (method_exists($app, 'make')) {
            echo "✅ Service container working\n";
        }
    } else {
        echo "❌ Bootstrap app.php missing!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel Bootstrap Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ PHP Fatal Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
echo "\n";

// Test database connection
echo "=== DATABASE CONNECTION TEST ===\n";
try {
    if (file_exists(__DIR__ . '/.env')) {
        $envContent = file_get_contents(__DIR__ . '/.env');
        
        // Parse basic env vars
        preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        preg_match('/DB_PORT=(.*)/', $envContent, $portMatch);
        
        $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '';
        $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : '';
        $username = isset($userMatch[1]) ? trim($userMatch[1]) : '';
        $password = isset($passMatch[1]) ? trim($passMatch[1]) : '';
        $port = isset($portMatch[1]) ? trim($portMatch[1]) : '3306';
        
        echo "DB Host: $host\n";
        echo "DB Name: $database\n";
        echo "DB User: $username\n";
        echo "DB Port: $port\n";
        
        if ($host && $database && $username) {
            // Test connection
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$database";
                $pdo = new PDO($dsn, $username, $password);
                echo "✅ Database connection successful\n";
            } catch (PDOException $e) {
                echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "⚠️  Missing database credentials\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Database test error: " . $e->getMessage() . "\n";
}
echo "\n";

// Check PHP extensions
echo "=== PHP EXTENSIONS CHECK ===\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
foreach ($requiredExtensions as $ext) {
    echo ($extension_loaded($ext) ? '✅' : '❌') . " $ext\n";
}
echo "\n";

// Emergency fixes
echo "=== EMERGENCY FIXES ===\n";

// Fix 1: Clear all cache files manually
echo "🔧 Clearing cache files...\n";
$cacheFiles = glob(__DIR__ . '/bootstrap/cache/*');
foreach ($cacheFiles as $file) {
    if (is_file($file) && basename($file) !== '.gitignore') {
        unlink($file);
        echo "  Deleted: " . basename($file) . "\n";
    }
}

// Fix 2: Set proper permissions
$dirsToFix = [
    __DIR__ . '/storage',
    __DIR__ . '/bootstrap/cache'
];

foreach ($dirsToFix as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "🔧 Set permissions for: $dir\n";
        
        // Recursively set permissions
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item, 0755);
            } else {
                chmod($item, 0644);
            }
        }
    }
}

echo "\n=== RECOMMENDED ACTIONS ===\n";
echo "1. 🔍 Check the specific error in Laravel logs\n";
echo "2. 🔄 Run: php artisan config:clear\n";
echo "3. 🔄 Run: php artisan cache:clear\n";
echo "4. 🔄 Run: php artisan optimize:clear\n";
echo "5. 🔄 Run: composer dump-autoload\n";

echo "\n🎯 500 Error diagnostic completed!\n";

// Create a simple test route
echo "\n=== SIMPLE TEST ===\n";
echo "<a href='/test-simple'>Test Simple Route</a>\n";
echo "</pre>";
?>
