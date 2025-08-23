<?php
echo "🔍 BUTTON ROUTING ANALYSIS & FIX\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// First, let's identify where these buttons are located
echo "📍 Step 1: Locating button sources\n";

$searchPatterns = [
    'quiz-generator-btn' => 'quiz-generator-btn',
    'view-archived-btn' => 'view-archived-btn', 
    'batch-upload-btn' => 'batch-upload-btn',
    'admin/quiz-generator' => 'admin/quiz-generator',
    'admin/modules/archived' => 'admin/modules/archived'
];

$viewFiles = [];
$jsFiles = [];

// Search in view files
$viewDirs = [
    'resources/views/admin',
    'resources/views/admin/admin-dashboard',
    'resources/views/admin/quiz-generator',
    'resources/views/admin/modules'
];

foreach ($viewDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.blade.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            foreach ($searchPatterns as $name => $pattern) {
                if (strpos($content, $pattern) !== false) {
                    if (!isset($viewFiles[$file])) {
                        $viewFiles[$file] = [];
                    }
                    $viewFiles[$file][] = $name;
                }
            }
        }
    }
}

// Search in JS files
$jsPattern = 'resources/js/*.js';
if (glob($jsPattern)) {
    foreach (glob($jsPattern) as $file) {
        $content = file_get_contents($file);
        foreach ($searchPatterns as $name => $pattern) {
            if (strpos($content, $pattern) !== false) {
                if (!isset($jsFiles[$file])) {
                    $jsFiles[$file] = [];
                }
                $jsFiles[$file][] = $name;
            }
        }
    }
}

echo "📁 Files containing button references:\n";
if (!empty($viewFiles)) {
    foreach ($viewFiles as $file => $patterns) {
        echo "   View: $file (" . implode(', ', $patterns) . ")\n";
    }
} else {
    echo "   No view files found with these patterns\n";
}

if (!empty($jsFiles)) {
    foreach ($jsFiles as $file => $patterns) {
        echo "   JS: $file (" . implode(', ', $patterns) . ")\n";
    }
} else {
    echo "   No JS files found with these patterns\n";
}

echo "\n🔍 Step 2: Searching more broadly for hardcoded admin URLs\n";

// Use shell command to search more thoroughly
$grepResults = shell_exec('cd C:\xampp\htdocs\A.R.T.C && findstr /R /S /I "admin/quiz-generator\|admin/modules/archived\|quiz-generator-btn\|view-archived-btn" resources\views\admin\*.blade.php 2>nul');

if ($grepResults) {
    echo "✅ Found matches:\n";
    $lines = explode("\n", trim($grepResults));
    foreach ($lines as $line) {
        if (trim($line)) {
            echo "   $line\n";
        }
    }
} else {
    echo "⚠️  No matches found with findstr - checking manually...\n";
    
    // Manual search in common locations
    $commonFiles = [
        'resources/views/admin/admin-dashboard/admin-dashboard.blade.php',
        'resources/views/admin/admin-dashboard/admin-dashboard-layout.blade.php',
        'resources/views/admin/modules/index.blade.php',
        'resources/views/admin/quiz-generator/index.blade.php'
    ];
    
    foreach ($commonFiles as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'quiz-generator-btn') !== false || 
                strpos($content, 'view-archived-btn') !== false ||
                strpos($content, 'admin/quiz-generator') !== false) {
                echo "   Found in: $file\n";
            }
        }
    }
}

echo "\n🎯 Analysis complete - proceeding with search and fix...\n";
?>
