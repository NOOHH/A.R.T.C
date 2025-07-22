<?php
/**
 * Test file upload process step by step
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Simulate Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FILE UPLOAD PROCESS TEST ===\n\n";

// Test recent uploads by checking Laravel logs
echo "1. Checking recent Laravel logs for file uploads...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    
    // Get last 50 lines that contain upload-related keywords
    $uploadLogs = [];
    foreach (array_reverse($lines) as $line) {
        if (stripos($line, 'course content store') !== false || 
            stripos($line, 'attachment') !== false || 
            stripos($line, 'file upload') !== false ||
            stripos($line, 'courseContentStore') !== false) {
            $uploadLogs[] = $line;
            if (count($uploadLogs) >= 10) break;
        }
    }
    
    if (!empty($uploadLogs)) {
        echo "Recent upload-related log entries:\n";
        foreach ($uploadLogs as $log) {
            echo "- " . trim($log) . "\n";
        }
    } else {
        echo "No recent upload-related log entries found\n";
    }
} else {
    echo "Laravel log file not found at: $logFile\n";
}

// Check storage structure
echo "\n2. Checking storage structure...\n";
$storagePath = storage_path('app/public');
echo "Storage path: $storagePath\n";

if (is_dir($storagePath)) {
    $items = scandir($storagePath);
    echo "Storage contents:\n";
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            $fullPath = $storagePath . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                echo "- [DIR] $item/\n";
                // List contents of subdirectories
                $subItems = scandir($fullPath);
                foreach ($subItems as $subItem) {
                    if ($subItem !== '.' && $subItem !== '..' && count(array_slice($subItems, 2)) <= 10) { // Limit output
                        $subPath = $fullPath . DIRECTORY_SEPARATOR . $subItem;
                        $size = is_file($subPath) ? filesize($subPath) : 0;
                        $type = is_dir($subPath) ? '[DIR]' : '[FILE]';
                        echo "  - $type $subItem" . ($size > 0 ? " ({$size} bytes)" : "") . "\n";
                    }
                }
            } else {
                $size = filesize($fullPath);
                echo "- [FILE] $item ({$size} bytes)\n";
            }
        }
    }
}

// Check public storage structure
echo "\n3. Checking public storage structure...\n";
$publicStoragePath = public_path('storage');
echo "Public storage path: $publicStoragePath\n";

if (is_dir($publicStoragePath)) {
    $items = scandir($publicStoragePath);
    echo "Public storage contents:\n";
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            $fullPath = $publicStoragePath . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                echo "- [DIR] $item/\n";
                // List contents of subdirectories
                $subItems = scandir($fullPath);
                foreach ($subItems as $subItem) {
                    if ($subItem !== '.' && $subItem !== '..' && count(array_slice($subItems, 2)) <= 10) { // Limit output
                        $subPath = $fullPath . DIRECTORY_SEPARATOR . $subItem;
                        $size = is_file($subPath) ? filesize($subPath) : 0;
                        $type = is_dir($subPath) ? '[DIR]' : '[FILE]';
                        echo "  - $type $subItem" . ($size > 0 ? " ({$size} bytes)" : "") . "\n";
                    }
                }
            } else {
                $size = filesize($fullPath);
                echo "- [FILE] $item ({$size} bytes)\n";
            }
        }
    }
} else {
    echo "Public storage directory does not exist\n";
}

// Check database for content items created in the last hour
echo "\n4. Checking recent content items in database...\n";
$recentItems = DB::table('content_items')
    ->where('created_at', '>=', now()->subHour())
    ->orderBy('created_at', 'desc')
    ->get();

if ($recentItems->count() > 0) {
    echo "Content items created in the last hour:\n";
    foreach ($recentItems as $item) {
        echo "- ID: {$item->id}\n";
        echo "  Title: {$item->content_title}\n";
        echo "  Type: {$item->content_type}\n";
        echo "  Course ID: {$item->course_id}\n";
        echo "  Attachment: " . ($item->attachment_path ?: 'NULL') . "\n";
        echo "  Created: {$item->created_at}\n";
        
        if ($item->attachment_path) {
            $storagePath = storage_path("app/public/{$item->attachment_path}");
            $publicPath = public_path("storage/{$item->attachment_path}");
            echo "  Storage exists: " . (file_exists($storagePath) ? '✅ Yes' : '❌ No') . "\n";
            echo "  Public exists: " . (file_exists($publicPath) ? '✅ Yes' : '❌ No') . "\n";
        }
        echo "\n";
    }
} else {
    echo "No content items created in the last hour\n";
}

// Test the route availability
echo "5. Testing route availability...\n";
$routes = app('router')->getRoutes();
$courseContentStoreRoute = null;

foreach ($routes as $route) {
    if (strpos($route->uri(), 'course-content-store') !== false) {
        $courseContentStoreRoute = $route;
        break;
    }
}

if ($courseContentStoreRoute) {
    echo "✅ course-content-store route found:\n";
    echo "- URI: {$courseContentStoreRoute->uri()}\n";
    echo "- Methods: " . implode(', ', $courseContentStoreRoute->methods()) . "\n";
    echo "- Action: {$courseContentStoreRoute->getActionName()}\n";
} else {
    echo "❌ course-content-store route not found\n";
}

// Test direct controller method availability
echo "\n6. Testing controller method...\n";
try {
    $controller = new \App\Http\Controllers\AdminModuleController();
    if (method_exists($controller, 'courseContentStore')) {
        echo "✅ courseContentStore method exists in AdminModuleController\n";
    } else {
        echo "❌ courseContentStore method not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing controller: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS ===\n";
echo "To debug file upload issues:\n";
echo "1. Check the browser network tab when uploading\n";
echo "2. Look for JavaScript console errors\n";
echo "3. Check if files are being sent in the request\n";
echo "4. Verify the courseContentStore method is receiving files\n";
echo "5. Check Laravel logs during upload process\n";
echo "\n";
echo "To test manually:\n";
echo "1. Upload a file through the web interface\n";
echo "2. Check if it appears in storage/app/public/\n";
echo "3. Check if it appears in public/storage/\n";
echo "4. Check the database content_items table\n";
echo "5. Try accessing the file via http://localhost:8000/storage/filename\n";

?>
