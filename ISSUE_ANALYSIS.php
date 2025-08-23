<?php
echo "🔍 COMPREHENSIVE ISSUE ANALYSIS & FIX\n";
echo "=" . str_repeat("=", 45) . "\n\n";

// Issue 1: Check the archived modules preview method
echo "📝 Step 1: Investigating archived modules preview error\n";

// Find the previewArchivedModules method
$adminControllerContent = file_get_contents('app/Http/Controllers/AdminController.php');
if (strpos($adminControllerContent, 'previewArchivedModules') !== false) {
    echo "✅ Found previewArchivedModules method in AdminController\n";
} else {
    echo "❌ previewArchivedModules method not found\n";
}

// Issue 2: Check the course content upload preview method
echo "\n📝 Step 2: Investigating course content upload preview error\n";

if (strpos($adminControllerContent, 'previewCourseContentUpload') !== false) {
    echo "✅ Found previewCourseContentUpload method in AdminController\n";
} else {
    echo "❌ previewCourseContentUpload method not found\n";
}

// Issue 3: Check the batch upload button behavior
echo "\n📝 Step 3: Investigating batch upload button behavior\n";

// Search for JavaScript that handles the showBatchModal button
$moduleViewContent = file_get_contents('resources/views/admin/admin-modules/admin-modules.blade.php');

if (strpos($moduleViewContent, 'showBatchModal') !== false) {
    echo "✅ Found showBatchModal button in modules view\n";
    
    // Check if there's JavaScript handling this modal
    if (strpos($moduleViewContent, 'showBatchModal') !== false) {
        // Look for modal or JavaScript handling
        $jsPattern = '/\$\(.*showBatchModal.*\)/';
        if (preg_match($jsPattern, $moduleViewContent)) {
            echo "✅ Found JavaScript handling for showBatchModal\n";
        } else {
            echo "⚠️  No obvious JavaScript handling found for showBatchModal\n";
        }
    }
} else {
    echo "❌ showBatchModal button not found in modules view\n";
}

echo "\n📝 Step 4: Checking view files for property issues\n";

// Check archived modules view
if (file_exists('resources/views/admin/admin-modules/admin-modules-archived.blade.php')) {
    $archivedContent = file_get_contents('resources/views/admin/admin-modules/admin-modules-archived.blade.php');
    $programIdCount = substr_count($archivedContent, '->program_id');
    echo "📄 admin-modules-archived.blade.php: $programIdCount references to ->program_id\n";
} else {
    echo "❌ admin-modules-archived.blade.php not found\n";
}

// Check course content upload view
if (file_exists('resources/views/admin/admin-modules/course-content-upload.blade.php')) {
    $uploadContent = file_get_contents('resources/views/admin/admin-modules/course-content-upload.blade.php');
    $programIdCount = substr_count($uploadContent, '->program_id');
    echo "📄 course-content-upload.blade.php: $programIdCount references to ->program_id\n";
} else {
    echo "❌ course-content-upload.blade.php not found\n";
}

echo "\n🎯 Analysis complete. Proceeding with detailed investigation...\n";
?>
