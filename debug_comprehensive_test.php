<?php
echo "=== COMPREHENSIVE DEBUG TEST ===\n\n";

// 1. Check if we're in the right directory
echo "1. Current Directory: " . getcwd() . "\n";
echo "2. Laravel Project Root: " . (file_exists('artisan') ? 'YES' : 'NO') . "\n";

// 2. Check storage permissions
echo "3. Storage Directory Exists: " . (is_dir('storage') ? 'YES' : 'NO') . "\n";
echo "4. Public Storage Link: " . (is_dir('public/storage') ? 'YES' : 'NO') . "\n";
echo "5. Content Storage Dir: " . (is_dir('storage/app/public/content') ? 'YES' : 'NO') . "\n";

// Create content directory if it doesn't exist
if (!is_dir('storage/app/public/content')) {
    mkdir('storage/app/public/content', 0777, true);
    echo "6. Created content directory\n";
} else {
    echo "6. Content directory already exists\n";
}

// Check permissions
echo "7. Storage App Writable: " . (is_writable('storage/app') ? 'YES' : 'NO') . "\n";
echo "8. Public Storage Writable: " . (is_writable('storage/app/public') ? 'YES' : 'NO') . "\n";

// 3. Test database connection
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $pdo = DB::connection()->getPdo();
    echo "9. Database Connection: SUCCESS\n";
    
    // Check modules table
    $modulesExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'modules'");
    echo "10. Modules Table Exists: " . ($modulesExists[0]->count > 0 ? 'YES' : 'NO') . "\n";
    
    // Check programs table  
    $programsExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'programs'");
    echo "11. Programs Table Exists: " . ($programsExists[0]->count > 0 ? 'YES' : 'NO') . "\n";
    
    // Count existing data
    $moduleCount = DB::table('modules')->count();
    $programCount = DB::table('programs')->count();
    echo "12. Modules Count: " . $moduleCount . "\n";
    echo "13. Programs Count: " . $programCount . "\n";
    
    // Check file upload settings
    echo "14. PHP Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "15. PHP Post Max Size: " . ini_get('post_max_size') . "\n";
    echo "16. PHP Max File Uploads: " . ini_get('max_file_uploads') . "\n";
    echo "17. PHP Memory Limit: " . ini_get('memory_limit') . "\n";
    
    // Check Laravel upload settings
    echo "18. Laravel Max Upload: " . config('filesystems.default') . "\n";
    
    // Test file write
    $testFile = 'storage/app/public/content/test_write.txt';
    $writeSuccess = file_put_contents($testFile, 'Test write: ' . date('Y-m-d H:i:s'));
    echo "19. File Write Test: " . ($writeSuccess ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($writeSuccess) {
        unlink($testFile);
        echo "20. File Delete Test: SUCCESS\n";
    }
    
} catch (Exception $e) {
    echo "9. Database Connection: FAILED - " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG TEST COMPLETE ===\n";
?>
