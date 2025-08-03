<?php
// Debug CSV Export Issue
// This file helps debug why CSV exports might not be downloading properly

// Include Laravel bootstrap
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h1>CSV Export Debug</h1>";

// Test basic CSV headers
echo "<h2>Testing CSV Response Headers</h2>";
echo "<p>Testing if proper CSV headers are being sent...</p>";

// Test the student export route directly
echo "<h3>Student Export Route Test</h3>";
echo "<a href='/admin/students/export' target='_blank'>Test Student Export (Opens in new tab)</a><br>";
echo "<a href='/admin/students/export?status=approved' target='_blank'>Test Approved Students Export</a><br>";

// Check if the route exists
try {
    $routes = app('router')->getRoutes();
    $exportRouteExists = false;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'admin.students.export') {
            $exportRouteExists = true;
            echo "<p style='color: green;'>✓ Route 'admin.students.export' exists</p>";
            echo "<p>Route URI: " . $route->uri() . "</p>";
            echo "<p>Route Methods: " . implode(', ', $route->methods()) . "</p>";
            break;
        }
    }
    
    if (!$exportRouteExists) {
        echo "<p style='color: red;'>✗ Route 'admin.students.export' not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking routes: " . $e->getMessage() . "</p>";
}

// Test manual CSV generation
echo "<h3>Manual CSV Test</h3>";
echo "<p>Testing manual CSV generation:</p>";

try {
    // Simple CSV test
    $testData = [
        ['Name', 'Email', 'Status'],
        ['John Doe', 'john@example.com', 'Active'],
        ['Jane Smith', 'jane@example.com', 'Pending']
    ];
    
    $csvContent = '';
    foreach ($testData as $row) {
        $csvContent .= '"' . implode('","', $row) . '"' . "\n";
    }
    
    echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo "<pre>" . htmlspecialchars($csvContent) . "</pre>";
    echo "</div>";
    
    // Create downloadable link
    $base64 = base64_encode($csvContent);
    echo "<a href='data:text/csv;base64,{$base64}' download='test.csv'>Download Test CSV</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error generating test CSV: " . $e->getMessage() . "</p>";
}

// Check database connection
echo "<h3>Database Connection Test</h3>";
try {
    DB::connection()->getPdo();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if students table exists and has data
    $studentCount = DB::table('students')->count();
    echo "<p>Students in database: {$studentCount}</p>";
    
    if ($studentCount > 0) {
        echo "<p style='color: green;'>✓ Students table has data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Students table is empty</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test browser download capability
echo "<h3>Browser Download Test</h3>";
echo "<script>
function testDownload() {
    console.log('Testing download...');
    
    // Test 1: Simple blob download
    const testData = 'Name,Email,Status\\nJohn Doe,john@example.com,Active\\nJane Smith,jane@example.com,Pending';
    const blob = new Blob([testData], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'browser_test.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    console.log('Browser test download completed');
}

function testServerDownload() {
    console.log('Testing server download...');
    
    // Test the actual export URL
    const link = document.createElement('a');
    link.href = '/admin/students/export';
    link.download = 'server_test.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    console.log('Server test download triggered');
}
</script>";

echo "<button onclick='testDownload()'>Test Browser CSV Download</button> ";
echo "<button onclick='testServerDownload()'>Test Server CSV Download</button>";

echo "<h3>Recommendations</h3>";
echo "<ul>";
echo "<li>Check browser network tab when clicking export</li>";
echo "<li>Look for any JavaScript errors in browser console</li>";
echo "<li>Verify the export route is accessible</li>";
echo "<li>Check if headers are properly set for CSV download</li>";
echo "<li>Ensure no output before CSV headers</li>";
echo "</ul>";

// Add some debugging JavaScript
echo "<script>
console.log('CSV Export Debug Script Loaded');
console.log('Current URL:', window.location.href);
console.log('User Agent:', navigator.userAgent);

// Monitor download events
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' && e.target.href.includes('export')) {
        console.log('Export link clicked:', e.target.href);
    }
});
</script>";
?>
