<?php
/**
 * COMPREHENSIVE ROUTING AND ANNOUNCEMENT FIX VALIDATION
 * Tests hardcoded URLs, database integrity, routes, and error handling
 */

echo "🔧 COMPREHENSIVE ROUTING & ANNOUNCEMENT FIX VALIDATION\n";
echo "=====================================================\n\n";

// Test configuration
$tenant = 'test1';
$website = '15';
$baseUrl = 'http://127.0.0.1:8000';
$tenantUrl = "$baseUrl/t/draft/$tenant";
$params = "?website=$website&preview=true&t=" . time();

echo "📊 PHASE 1: DATABASE & MODEL VALIDATION\n";
echo "--------------------------------------\n";

// Check Announcement model and database
echo "🔍 Checking Announcement model and database...\n";

try {
    // Check if announcements table exists
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=artc', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    $stmt = $pdo->query("SHOW TABLES LIKE 'announcements'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Announcements table exists\n";
        
        // Check table columns
        $stmt = $pdo->query("DESCRIBE announcements");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        // Check existing records
        $stmt = $pdo->query("SELECT COUNT(*) FROM announcements");
        $count = $stmt->fetchColumn();
        echo "   Records: $count announcements found\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, title FROM announcements LIMIT 5");
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($announcements as $ann) {
                echo "   • ID {$ann['id']}: {$ann['title']}\n";
            }
        }
    } else {
        echo "❌ Announcements table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n📊 PHASE 2: ROUTE VALIDATION\n";
echo "---------------------------\n";

// Test routes that might cause announcement errors
$testRoutes = [
    'Regular Admin Directors' => "$baseUrl/admin/directors",
    'Tenant Admin Directors' => "$tenantUrl/admin/directors$params",
    'Regular Directors Archived' => "$baseUrl/admin/directors/archived", 
    'Tenant Directors Archived' => "$tenantUrl/admin/directors/archived$params",
    'Regular Student History' => "$baseUrl/admin-student-registration/history",
    'Tenant Student History' => "$tenantUrl/admin-student-registration/history$params",
    'Regular Payment Pending' => "$baseUrl/admin-student-registration/payment/pending",
    'Tenant Payment Pending' => "$tenantUrl/admin-student-registration/payment/pending$params"
];

foreach ($testRoutes as $name => $url) {
    echo "🧪 Testing: $name\n";
    echo "   URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "   ✅ HTTP 200 - Page loads successfully\n";
        
        // Check for announcement errors
        if (strpos($response, 'ModelNotFoundException') !== false && strpos($response, 'Announcement') !== false) {
            echo "   ❌ FOUND: Announcement ModelNotFoundException\n";
            
            // Extract more details
            preg_match('/No query results for model.*?Announcement.*?(\d+)/', $response, $matches);
            if ($matches) {
                echo "   📍 Looking for Announcement ID: {$matches[1]}\n";
            }
        } else {
            echo "   ✅ No announcement errors detected\n";
        }
        
        // Check for other errors
        if (strpos($response, 'Error') !== false || strpos($response, 'Exception') !== false) {
            echo "   ⚠️  Other errors may be present\n";
        }
        
    } elseif ($httpCode === 404) {
        echo "   ❌ HTTP 404 - Route not found\n";
    } elseif ($httpCode === 500) {
        echo "   ❌ HTTP 500 - Server error\n";
    } else {
        echo "   ❌ HTTP $httpCode - Unexpected response\n";
    }
    
    echo "\n";
}

echo "📊 PHASE 3: BLADE TEMPLATE VALIDATION\n";
echo "------------------------------------\n";

// Check blade files for hardcoded URLs
$bladeFiles = [
    'resources/views/admin/directors/index.blade.php',
    'resources/views/admin/directors/director.blade.php', 
    'resources/views/admin/admin-student-registration/admin-student-registration.blade.php'
];

foreach ($bladeFiles as $file) {
    $filePath = __DIR__ . '/' . $file;
    echo "🔍 Checking: $file\n";
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Check for hardcoded URLs
        $hardcodedPatterns = [
            'http://127.0.0.1:8000/admin/directors/archived',
            'http://127.0.0.1:8000/admin-student-registration/history',
            'http://127.0.0.1:8000/admin-student-registration/payment/pending'
        ];
        
        $hasHardcoded = false;
        foreach ($hardcodedPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                echo "   ❌ FOUND hardcoded URL: $pattern\n";
                $hasHardcoded = true;
            }
        }
        
        if (!$hasHardcoded) {
            echo "   ✅ No hardcoded URLs found - using route() helpers\n";
        }
        
        // Check for route usage
        $routePatterns = [
            'route(\'admin.directors.archived\')',
            'route(\'admin.student.registration.history\')', 
            'route(\'admin.student.registration.payment.pending\')'
        ];
        
        foreach ($routePatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                echo "   ✅ GOOD: Found $pattern\n";
            }
        }
        
    } else {
        echo "   ❌ File not found\n";
    }
    
    echo "\n";
}

echo "📊 PHASE 4: CONTROLLER VALIDATION\n";
echo "--------------------------------\n";

// Check controllers for announcement usage
$controllers = [
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/AdminDirectorController.php',
    'app/Http/Controllers/AdminStudentListController.php'
];

foreach ($controllers as $controller) {
    $filePath = __DIR__ . '/' . $controller;
    echo "🔍 Checking: $controller\n";
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Check for Announcement model usage
        if (strpos($content, 'Announcement::') !== false) {
            echo "   ⚠️  Uses Announcement model\n";
            
            // Check for findOrFail usage
            if (strpos($content, 'Announcement::findOrFail') !== false) {
                echo "   ❌ RISKY: Uses Announcement::findOrFail (can throw ModelNotFoundException)\n";
            }
            
            // Check for find usage
            if (strpos($content, 'Announcement::find') !== false) {
                echo "   ✅ SAFER: Uses Announcement::find\n";
            }
        } else {
            echo "   ✅ No Announcement model usage\n";
        }
        
        // Check preview methods
        if (strpos($content, 'function preview') !== false) {
            echo "   📝 Has preview methods\n";
        }
        
    } else {
        echo "   ❌ File not found\n";
    }
    
    echo "\n";
}

echo "📊 PHASE 5: ROUTE REGISTRATION CHECK\n";
echo "----------------------------------\n";

// Check if routes are properly registered
echo "🔍 Checking route registration...\n";

$routeCommands = [
    'admin.directors.archived',
    'admin.student.registration.history',
    'admin.student.registration.payment.pending'
];

foreach ($routeCommands as $routeName) {
    $output = shell_exec("cd " . __DIR__ . " && php artisan route:list --name=$routeName 2>&1");
    
    if (strpos($output, $routeName) !== false) {
        echo "   ✅ Route '$routeName' is registered\n";
    } else {
        echo "   ❌ Route '$routeName' is NOT registered\n";
        echo "      Output: " . trim($output) . "\n";
    }
}

echo "\n📊 FINAL SUMMARY\n";
echo "===============\n";

echo "🎯 ISSUES TO FIX:\n";
echo "1. Check blade templates for hardcoded URLs\n";
echo "2. Investigate Announcement ModelNotFoundException\n"; 
echo "3. Ensure all routes are properly registered\n";
echo "4. Verify tenant routing works correctly\n";

echo "\n✅ COMPREHENSIVE VALIDATION COMPLETE!\n";
