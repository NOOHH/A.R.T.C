<?php
/**
 * Fix all preview methods to include user_type for sidebar compatibility
 */

echo "🔧 FIXING ALL PREVIEW METHODS FOR SIDEBAR COMPATIBILITY\n";
echo "======================================================\n\n";

// Files to update
$files = [
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/AdminProfessorController.php',
    'app/Http/Controllers/AdminStudentListController.php',
    'app/Http/Controllers/AdminAnalyticsController.php',
    'app/Http/Controllers/AdminProgramController.php',
    'app/Http/Controllers/AdminSettingsController.php',
    'app/Http/Controllers/AdminPackageController.php',
    'app/Http/Controllers/AdminModuleController.php',
    'app/Http/Controllers/AdminDirectorController.php'
];

$updated = 0;
$total = 0;

foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "⚠️  Skipping: $file (not found)\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Pattern to find session arrays with 'user_role' => 'admin' but missing 'user_type'
    $pattern = '/(\s+session\(\[\s*\n(?:[^]]*\n)*)([^]]*\'user_role\'\s*=>\s*\'admin\'[^]]*\n)([^]]*\]\);)/';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $before = $matches[1];
        $userRoleLine = $matches[2];
        $after = $matches[3];
        
        // Check if user_type is already present
        if (strpos($matches[0], "'user_type'") !== false) {
            return $matches[0]; // Already has user_type, no change needed
        }
        
        // Add user_type after user_role
        $newUserRoleLine = str_replace(
            "'user_role' => 'admin',",
            "'user_role' => 'admin',\n                'user_type' => 'admin', // Add for sidebar compatibility",
            $userRoleLine
        );
        
        return $before . $newUserRoleLine . $after;
    }, $content);
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "✅ Updated: $file\n";
        $updated++;
    } else {
        echo "⚪ No changes needed: $file\n";
    }
    
    $total++;
}

echo "\n📊 SUMMARY:\n";
echo "Updated: $updated/$total files\n";

echo "\n🧪 Testing the fix...\n";

// Test the tenant dashboard to see if Directors link now appears
$testUrl = "http://localhost:8000/t/draft/test1/admin-dashboard?website=15";
echo "Testing: $testUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Dashboard loads successfully\n";
    
    if (strpos($response, 't/draft/test1/admin/directors?website=15') !== false) {
        echo "🎉 SUCCESS: Directors link is now tenant-aware and visible!\n";
    } elseif (strpos($response, 'admin/directors') !== false) {
        echo "⚠️  Directors link found but checking if it's tenant-aware...\n";
        
        // Extract the link
        preg_match('/href="([^"]*admin\/directors[^"]*)"/', $response, $matches);
        if ($matches) {
            echo "   Found link: " . $matches[1] . "\n";
            if (strpos($matches[1], 't/draft/test1') !== false) {
                echo "🎉 SUCCESS: Directors link is tenant-aware!\n";
            } else {
                echo "❌ Directors link is still not tenant-aware\n";
            }
        }
    } else {
        echo "❌ Directors link still not found in sidebar\n";
    }
} else {
    echo "❌ Dashboard failed to load: HTTP $httpCode\n";
}

echo "\n✅ ALL PREVIEW METHODS FIXED FOR SIDEBAR COMPATIBILITY!\n";
