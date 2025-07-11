<?php
// Simple test to check module data
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Module;
use Illuminate\Support\Facades\DB;

// Mock the Laravel app for testing
if (!app()->bound('path')) {
    app()->bind('path', function() {
        return __DIR__;
    });
}

try {
    // Check if we can connect to the database
    $modules = Module::all();
    
    echo "<h2>Module Database Test</h2>";
    echo "<p>Found " . count($modules) . " modules</p>";
    
    foreach ($modules as $module) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<h3>" . $module->module_name . "</h3>";
        echo "<p><strong>Type:</strong> " . ($module->content_type ?? 'not set') . "</p>";
        echo "<p><strong>Attachment:</strong> " . ($module->attachment ?? 'none') . "</p>";
        
        if ($module->additional_content) {
            echo "<p><strong>Additional Content:</strong></p>";
            echo "<pre>" . htmlspecialchars($module->additional_content) . "</pre>";
        }
        
        if ($module->content_data) {
            echo "<p><strong>Content Data:</strong></p>";
            echo "<pre>" . htmlspecialchars($module->content_data) . "</pre>";
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
