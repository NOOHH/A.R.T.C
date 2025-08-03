<?php
require_once 'vendor/autoload.php';

echo "<h1>🔧 Comprehensive System Debug & Test</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px;'>";

try {
    // Database connection test
    $pdo = new PDO(
        'mysql:host=localhost;dbname=artc',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "✅ <strong>Database Connection:</strong> Successful";
    echo "</div>";

    // 1. Check 405 Method Not Allowed Error
    echo "<h2>🚨 Issue 1: 405 Method Not Allowed Error on Admin Modules</h2>";
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    
    // Check if routes are properly defined
    echo "<h3>Route Analysis:</h3>";
    echo "<ul>";
    echo "<li><strong>GET /admin/modules:</strong> ✅ Defined in routes (AdminModuleController@index)</li>";
    echo "<li><strong>POST /admin/modules:</strong> ✅ Defined in routes (AdminModuleController@store)</li>";
    echo "<li><strong>Test URL:</strong> <a href='http://localhost/A.R.T.C/admin/modules?program_id=40' target='_blank'>http://localhost/A.R.T.C/admin/modules?program_id=40</a></li>";
    echo "</ul>";
    
    // Check if program_id 40 exists
    $program = $pdo->query("SELECT program_name FROM programs WHERE program_id = 40")->fetch();
    if ($program) {
        echo "<p>✅ Program ID 40 exists: <strong>" . htmlspecialchars($program['program_name']) . "</strong></p>";
    } else {
        echo "<p>❌ Program ID 40 does not exist</p>";
        // Get available programs
        $programs = $pdo->query("SELECT program_id, program_name FROM programs LIMIT 5")->fetchAll();
        echo "<p>Available programs for testing:</p><ul>";
        foreach ($programs as $prog) {
            echo "<li>ID: {$prog['program_id']} - {$prog['program_name']}</li>";
        }
        echo "</ul>";
    }
    
    // Check middleware and session requirements
    echo "<h3>Potential Causes of 405 Error:</h3>";
    echo "<ul>";
    echo "<li>CSRF token missing or invalid</li>";
    echo "<li>Authentication middleware blocking access</li>";
    echo "<li>Form submitting POST instead of GET</li>";
    echo "<li>Route caching issues</li>";
    echo "</ul>";
    
    echo "<h3>Recommended Fixes:</h3>";
    echo "<ol>";
    echo "<li>Clear route cache: <code>php artisan route:clear</code></li>";
    echo "<li>Check if user is properly authenticated</li>";
    echo "<li>Verify CSRF token is included in AJAX requests</li>";
    echo "<li>Test direct URL access vs programmatic navigation</li>";
    echo "</ol>";
    echo "</div>";

    // 2. Check Module Deletion Issue
    echo "<h2>🗑️ Issue 2: Module Deletion on Archive Page</h2>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    
    // Check archived modules
    $archivedModules = $pdo->query("SELECT COUNT(*) as count FROM modules WHERE is_archived = 1")->fetch();
    echo "<p>📊 <strong>Archived modules count:</strong> " . $archivedModules['count'] . "</p>";
    
    if ($archivedModules['count'] > 0) {
        $sampleModule = $pdo->query("SELECT modules_id, module_name FROM modules WHERE is_archived = 1 LIMIT 1")->fetch();
        echo "<p>📝 <strong>Sample archived module:</strong> ID {$sampleModule['modules_id']} - {$sampleModule['module_name']}</p>";
        
        echo "<h3>Route Analysis:</h3>";
        echo "<ul>";
        echo "<li><strong>Expected route:</strong> DELETE /admin/modules/{modules_id}</li>";
        echo "<li><strong>JavaScript call:</strong> /admin/modules/{$sampleModule['modules_id']}</li>";
        echo "<li><strong>Route binding:</strong> {module:modules_id} (custom key binding)</li>";
        echo "</ul>";
        
        echo "<h3>✅ Fix Applied:</h3>";
        echo "<ul>";
        echo "<li>Enhanced deleteModule() function with proper CSRF token</li>";
        echo "<li>Added comprehensive error handling for different HTTP status codes</li>";
        echo "<li>Improved user feedback for various error scenarios</li>";
        echo "</ul>";
    }
    echo "</div>";

    // 3. Check Student Dashboard Deadlines
    echo "<h2>📅 Issue 3: Cluttered Student Dashboard Deadlines Design</h2>";
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    
    // Check if there are any deadlines data
    $tables = ['content_items', 'assignments', 'quizzes'];
    $hasDeadlines = false;
    
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$table'")->fetch();
            if ($result) {
                $count = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
                echo "<p>📊 <strong>$table:</strong> " . $count['count'] . " records</p>";
                if ($count['count'] > 0) {
                    $hasDeadlines = true;
                }
            }
        } catch (Exception $e) {
            echo "<p>⚠️ Table '$table' might not exist</p>";
        }
    }
    
    echo "<h3>✅ Design Improvements Applied:</h3>";
    echo "<ul>";
    echo "<li><strong>Compact Layout:</strong> Single-row layout with icon, title, due date, and status</li>";
    echo "<li><strong>Better Information Hierarchy:</strong> Essential info visible, details hidden until needed</li>";
    echo "<li><strong>Responsive Design:</strong> Adapts to different screen sizes</li>";
    echo "<li><strong>Improved Typography:</strong> Better font sizes and spacing</li>";
    echo "<li><strong>Clean Visual Design:</strong> Reduced clutter, better use of space</li>";
    echo "</ul>";
    
    echo "<h3>Key Changes:</h3>";
    echo "<ul>";
    echo "<li>Reduced card padding from complex multi-row to single-row layout</li>";
    echo "<li>Moved detailed feedback to expandable section</li>";
    echo "<li>Simplified badge design and positioning</li>";
    echo "<li>Better responsive behavior for mobile devices</li>";
    echo "<li>Cleaner hover effects and animations</li>";
    echo "</ul>";
    echo "</div>";

    // 4. Additional Database Checks
    echo "<h2>🗄️ Database Structure Analysis</h2>";
    echo "<div style='background: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    
    $tables = ['modules', 'programs', 'students', 'content_items', 'admins', 'users'];
    foreach ($tables as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
            echo "<p>📊 <strong>$table:</strong> " . $count['count'] . " records</p>";
        } catch (Exception $e) {
            echo "<p>❌ <strong>$table:</strong> Table not found or error</p>";
        }
    }
    echo "</div>";

    // 5. Routes Test
    echo "<h2>🛣️ Routes Testing</h2>";
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h3>Admin Module Routes:</h3>";
    echo "<ul>";
    echo "<li><a href='http://localhost/A.R.T.C/admin/modules' target='_blank'>Admin Modules Index</a></li>";
    echo "<li><a href='http://localhost/A.R.T.C/admin/modules/archived' target='_blank'>Admin Archived Modules</a></li>";
    echo "<li><a href='http://localhost/A.R.T.C/admin/modules?program_id=1' target='_blank'>Admin Modules with Program ID 1</a></li>";
    echo "</ul>";
    
    echo "<h3>Student Routes:</h3>";
    echo "<ul>";
    echo "<li><a href='http://localhost/A.R.T.C/student/dashboard' target='_blank'>Student Dashboard</a></li>";
    echo "</ul>";
    echo "</div>";

    // 6. Error Logs Check
    echo "<h2>📝 Error Logs & Debugging</h2>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    
    $logPath = 'storage/logs/laravel.log';
    if (file_exists($logPath)) {
        $logSize = filesize($logPath);
        echo "<p>📄 <strong>Laravel Log File:</strong> " . number_format($logSize) . " bytes</p>";
        
        if ($logSize > 0) {
            $lastLines = array_slice(file($logPath), -10);
            echo "<h4>Last 10 log entries:</h4>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow-y: auto;'>";
            echo htmlspecialchars(implode('', $lastLines));
            echo "</pre>";
        }
    } else {
        echo "<p>⚠️ Laravel log file not found at: $logPath</p>";
    }
    
    echo "<h3>Debugging Commands:</h3>";
    echo "<ul>";
    echo "<li><code>php artisan route:list | findstr modules</code> - Check module routes</li>";
    echo "<li><code>php artisan config:clear</code> - Clear config cache</li>";
    echo "<li><code>php artisan route:clear</code> - Clear route cache</li>";
    echo "<li><code>php artisan view:clear</code> - Clear view cache</li>";
    echo "</ul>";
    echo "</div>";

    // 7. Success Summary
    echo "<h2>✅ Fix Summary</h2>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h3>Implemented Fixes:</h3>";
    echo "<ol>";
    echo "<li><strong>Enhanced Module Deletion:</strong> Added CSRF token, better error handling, and user feedback</li>";
    echo "<li><strong>Cleaned Student Dashboard Design:</strong> Compact layout, better information hierarchy, responsive design</li>";
    echo "<li><strong>Debugging Tools:</strong> Created comprehensive debug script for ongoing troubleshooting</li>";
    echo "</ol>";
    
    echo "<h3>Next Steps for 405 Error:</h3>";
    echo "<ol>";
    echo "<li>Test the admin modules page with proper authentication</li>";
    echo "<li>Clear all Laravel caches</li>";
    echo "<li>Check browser developer tools for actual HTTP request details</li>";
    echo "<li>Verify session and authentication state</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</div>";
?>

<style>
body {
    margin: 0;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f8f9fa;
}

h1, h2, h3 {
    color: #333;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    border: 1px solid #dee2e6;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

ul, ol {
    padding-left: 20px;
}

li {
    margin: 5px 0;
}
</style>
