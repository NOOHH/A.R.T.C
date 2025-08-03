<?php
/**
 * Comprehensive Admin Module System Test
 * This test should be run while logged in as an admin or director
 */

echo "=== COMPREHENSIVE ADMIN MODULE SYSTEM TEST ===\n\n";

// Test 1: Database Connection and Structure
echo "1. DATABASE CONNECTIVITY TEST\n";
echo "==============================\n";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=artcdb",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Database connection successful\n";
    
    // Check key tables
    $tables = ['modules', 'courses', 'content_items', 'admin_settings'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
            
            // Get row count
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "  - Records: $count\n";
        } else {
            echo "✗ Table '$table' missing\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Sample Data Analysis
echo "2. SAMPLE DATA ANALYSIS\n";
echo "=======================\n";

try {
    // Get sample modules
    $moduleStmt = $pdo->query("
        SELECT m.*, 
               COUNT(DISTINCT c.subject_id) as course_count,
               COUNT(DISTINCT ci.content_item_id) as content_count
        FROM modules m 
        LEFT JOIN courses c ON m.modules_id = c.module_id 
        LEFT JOIN content_items ci ON c.subject_id = ci.course_id 
        WHERE m.is_archived = 0
        GROUP BY m.modules_id 
        LIMIT 5
    ");
    
    $modules = $moduleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($modules)) {
        echo "✓ Found " . count($modules) . " active modules\n";
        
        foreach ($modules as $module) {
            echo "\nModule: {$module['module_name']} (ID: {$module['modules_id']})\n";
            echo "  - Courses: {$module['course_count']}\n";
            echo "  - Content Items: {$module['content_count']}\n";
            echo "  - Program: {$module['program_id']}\n";
            echo "  - Order: {$module['display_order']}\n";
        }
    } else {
        echo "✗ No active modules found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Data analysis error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Route Structure Validation
echo "3. ROUTE STRUCTURE TEST\n";
echo "=======================\n";

$routes_to_test = [
    '/admin/modules',
    '/admin/modules/archived',
    '/admin/modules/1/content',
    '/admin/modules/1/courses/1/content',
    '/admin/modules/1/toggle-archive'
];

foreach ($routes_to_test as $route) {
    echo "Route: $route\n";
    
    // For this test, we just validate the route pattern
    if (preg_match('/\/admin\/modules(\/\d+)?(\/content|\/toggle-archive|\/courses\/\d+\/content)?$/', $route)) {
        echo "✓ Route pattern valid\n";
    } else {
        echo "✗ Route pattern invalid\n";
    }
    
    echo "\n";
}

// Test 4: Authentication Settings Check
echo "4. AUTHENTICATION SETTINGS\n";
echo "===========================\n";

try {
    $settingsStmt = $pdo->query("
        SELECT setting_key, setting_value 
        FROM admin_settings 
        WHERE setting_key LIKE 'director_%'
        ORDER BY setting_key
    ");
    
    $settings = $settingsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($settings)) {
        echo "✓ Found " . count($settings) . " director permission settings\n";
        
        foreach ($settings as $setting) {
            $value = $setting['setting_value'] === '1' || $setting['setting_value'] === 'true' ? 'Enabled' : 'Disabled';
            echo "  - {$setting['setting_key']}: $value\n";
        }
    } else {
        echo "✗ No director permission settings found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Settings check error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Mobile Design Recommendations
echo "5. MOBILE DESIGN OPTIMIZATION STATUS\n";
echo "====================================\n";

$css_files = [
    'public/css/admin/admin-modules.css',
    'public/css/admin/admin-modules-archived.css'
];

foreach ($css_files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        echo "File: $file\n";
        
        // Check for mobile-responsive features
        $mobile_features = [
            '@media (max-width: 768px)' => 'Tablet breakpoint',
            '@media (max-width: 480px)' => 'Mobile breakpoint', 
            '@media (max-width: 360px)' => 'Small mobile breakpoint',
            'min-height: 44px' => 'Touch-friendly button sizes',
            '-webkit-tap-highlight-color' => 'Touch highlight optimization'
        ];
        
        foreach ($mobile_features as $pattern => $description) {
            if (strpos($content, $pattern) !== false) {
                echo "✓ $description implemented\n";
            } else {
                echo "✗ $description missing\n";
            }
        }
        
        echo "\n";
    } else {
        echo "✗ CSS file not found: $file\n\n";
    }
}

// Test 6: JavaScript Integration Test Plan
echo "6. JAVASCRIPT INTEGRATION TEST PLAN\n";
echo "====================================\n";

echo "To test JavaScript functionality:\n";
echo "1. Log in as admin or director\n";
echo "2. Navigate to /admin/modules\n";
echo "3. Open browser developer tools\n";
echo "4. Check for console errors\n";
echo "5. Test module content loading by clicking a module\n";
echo "6. Verify API calls return JSON (not HTML)\n";
echo "7. Test archive/unarchive functionality\n";
echo "8. Test on mobile device or responsive mode\n\n";

echo "Expected API Response Format:\n";
echo "{\n";
echo "  \"success\": true,\n";
echo "  \"module\": { \"modules_id\": 1, \"module_name\": \"...\" },\n";
echo "  \"courses\": [...],\n";
echo "  \"content_items\": [...]\n";
echo "}\n\n";

// Test 7: Mobile UX Checklist
echo "7. MOBILE UX VALIDATION CHECKLIST\n";
echo "==================================\n";

$mobile_checklist = [
    "Touch targets are minimum 44px x 44px",
    "Text is readable without zooming",
    "Buttons have adequate spacing (8px+ margin)",
    "Cards stack in single column on mobile",
    "No horizontal scrolling required",
    "Loading states are visible",
    "Error messages are touch-friendly",
    "Navigation is thumb-accessible",
    "Form inputs are properly sized",
    "Modal dialogs fit mobile screens"
];

foreach ($mobile_checklist as $item) {
    echo "☐ $item\n";
}

echo "\n";

// Test 8: Performance Recommendations
echo "8. PERFORMANCE OPTIMIZATION\n";
echo "============================\n";

echo "Recommendations for optimal mobile performance:\n";
echo "1. Minimize CSS file sizes\n";
echo "2. Use CSS Grid for responsive layouts\n";
echo "3. Implement lazy loading for module content\n";
echo "4. Cache API responses when appropriate\n";
echo "5. Use requestAnimationFrame for smooth animations\n";
echo "6. Optimize images for mobile bandwidth\n";
echo "7. Implement service worker for offline functionality\n";
echo "8. Use CSS containment for better rendering\n\n";

echo "=== TEST COMPLETE ===\n";
echo "Next Steps:\n";
echo "1. Run this test while logged in as admin/director\n";
echo "2. Test API endpoints in browser\n";
echo "3. Validate mobile design on actual devices\n";
echo "4. Check browser console for JavaScript errors\n";
echo "5. Verify responsive design at different breakpoints\n";

?>
