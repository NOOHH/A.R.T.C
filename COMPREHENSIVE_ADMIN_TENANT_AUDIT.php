<?php
echo "🔍 COMPREHENSIVE ADMIN PAGES TENANT AUDIT\n";
echo "=" . str_repeat("=", 50) . "\n\n";

/**
 * This script audits ALL admin pages to find hardcoded URLs that need tenant-awareness
 * As requested: "check all the pages on the admin check if they have the tenant part"
 */

echo "📋 Step 1: Scanning All Admin Blade Templates\n";
echo "=" . str_repeat("-", 45) . "\n";

$adminViewDirs = [
    'resources/views/admin',
    'resources/views/admin/admin-dashboard',
    'resources/views/admin/admin-programs',
    'resources/views/admin/admin-modules',
    'resources/views/admin/professors',
    'resources/views/admin/students',
    'resources/views/admin/submissions',
    'resources/views/admin/certificates'
];

$hardcodedPatterns = [
    '/admin/students/archived' => 'Student Archived',
    '/admin/professors/archived' => 'Professor Archived',
    '/admin/programs/archived' => 'Program Archived',
    '/admin/modules/archived' => 'Module Archived',
    '/admin/submissions' => 'Submissions',
    '/admin/certificates' => 'Certificates',
    'route\(\'admin\.' => 'Laravel Route Helper',
    'href="http://127\.0\.0\.1:8000/admin' => 'Hardcoded Admin URL'
];

$foundIssues = [];

foreach ($adminViewDirs as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $filePath = $file->getPathname();
                $content = file_get_contents($filePath);
                $relativePath = str_replace('\\', '/', str_replace(getcwd() . '\\', '', $filePath));
                
                foreach ($hardcodedPatterns as $pattern => $description) {
                    if (preg_match('/' . str_replace('/', '\/', $pattern) . '/', $content)) {
                        if (!isset($foundIssues[$relativePath])) {
                            $foundIssues[$relativePath] = [];
                        }
                        $foundIssues[$relativePath][] = $description;
                    }
                }
            }
        }
    }
}

echo "🚨 FOUND HARDCODED ADMIN URLs:\n";
foreach ($foundIssues as $file => $issues) {
    echo "📄 $file:\n";
    foreach ($issues as $issue) {
        echo "   ❌ $issue\n";
    }
    echo "\n";
}

echo "\n📋 Step 2: Testing Specific Problem URLs\n";
echo "=" . str_repeat("-", 45) . "\n";

// Test the specific URLs mentioned in the error
$problemUrls = [
    'http://127.0.0.1:8000/admin/students/archived' => 'Students Archived',
    'http://127.0.0.1:8000/admin/professors/archived' => 'Professors Archived'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

foreach ($problemUrls as $url => $description) {
    echo "🧪 Testing $description:\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        if (strpos($response, 'ModelNotFoundException') !== false) {
            echo "   ❌ ERROR: ModelNotFoundException detected\n";
        } elseif (strpos($response, 'No query results for model') !== false) {
            echo "   ❌ ERROR: No query results error detected\n";
        } elseif (strpos($response, 'Professor.*archived') !== false) {
            echo "   ❌ ERROR: Professor archived error detected\n";
        } else {
            echo "   ✅ ACCESSIBLE: Page loads without model errors\n";
        }
    } else {
        echo "   ❌ ERROR: Cannot access URL\n";
    }
}

echo "\n📋 Step 3: Checking Tenant Routes Availability\n";
echo "=" . str_repeat("-", 45) . "\n";

// Check what tenant routes are available
$tenantUrls = [
    'http://127.0.0.1:8000/t/draft/test1/admin/students?website=1' => 'Tenant Students',
    'http://127.0.0.1:8000/t/draft/test1/admin/professors?website=1' => 'Tenant Professors',
    'http://127.0.0.1:8000/t/draft/test1/admin/students/archived?website=1' => 'Tenant Students Archived',
    'http://127.0.0.1:8000/t/draft/test1/admin/professors/archived?website=1' => 'Tenant Professors Archived'
];

foreach ($tenantUrls as $url => $description) {
    echo "🧪 Testing $description:\n";
    echo "   URL: $url\n";
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "   ✅ ACCESSIBLE: Tenant route exists\n";
    } else {
        echo "   ❌ NOT FOUND: Tenant route missing\n";
    }
}

echo "\n📋 Step 4: Identifying Files That Need Fixing\n";
echo "=" . str_repeat("-", 45) . "\n";

$filesToFix = [
    'resources/views/admin/students/index.blade.php' => 'Student management page',
    'resources/views/admin/professors/index.blade.php' => 'Professor management page',
    'resources/views/admin/students/archived.blade.php' => 'Archived students page',
    'resources/views/admin/professors/archived.blade.php' => 'Archived professors page'
];

foreach ($filesToFix as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        echo "📄 $description ($file):\n";
        
        // Check for hardcoded URLs
        if (strpos($content, '/admin/students/archived') !== false ||
            strpos($content, '/admin/professors/archived') !== false) {
            echo "   ❌ NEEDS FIX: Contains hardcoded admin URLs\n";
        }
        
        // Check for tenant awareness
        if (strpos($content, 'session(\'preview_tenant\')') !== false ||
            strpos($content, '@if') !== false) {
            echo "   ✅ HAS LOGIC: Contains conditional logic\n";
        } else {
            echo "   ❌ NEEDS LOGIC: Missing tenant-aware conditional logic\n";
        }
    } else {
        echo "📄 $description: ⚠️  File not found\n";
    }
}

echo "\n🎯 PRIORITY FIXES NEEDED:\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "1. ❌ Update student archived button URLs\n";
echo "2. ❌ Update professor archived button URLs\n";
echo "3. ❌ Add tenant-aware conditional logic to all admin pages\n";
echo "4. ❌ Fix ModelNotFoundException errors in archived pages\n";
echo "5. ❌ Ensure all admin buttons use tenant URLs in preview mode\n";

echo "\n🔧 Next: Apply fixes to identified files...\n";
?>
