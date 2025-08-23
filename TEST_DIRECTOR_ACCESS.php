<?php
/**
 * Comprehensive Director Access Test
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧪 TESTING DIRECTOR ACCESS\n";
echo "==========================\n\n";

// Test 1: Database setup
echo "1️⃣ DATABASE TESTS:\n";
try {
    $directorAdmin = DB::table('admins')->where('email', 'director@smartprep.com')->first();
    echo "   " . ($directorAdmin ? "✅ Director admin exists (ID: {$directorAdmin->id})" : "❌ Director admin missing") . "\n";
    
    $directorSettings = DB::table('admin_settings')
        ->where('setting_key', 'like', 'director_%')
        ->where('setting_value', 'true')
        ->where('is_active', 1)
        ->count();
    echo "   ✅ Director settings: $directorSettings enabled\n";
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 2: Route accessibility  
echo "\n2️⃣ ROUTE TESTS:\n";
$testUrls = [
    'http://localhost:8000/director/dashboard',
    'http://localhost:8000/admin-dashboard'
];

foreach ($testUrls as $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = $httpCode == 200 || $httpCode == 302 ? '✅' : '❌';
    echo "   $status " . basename($url) . ": HTTP $httpCode\n";
}

// Test 3: File existence
echo "\n3️⃣ FILE TESTS:\n";
$files = [
    'app/Http/Middleware/DirectorAccess.php' => 'Director middleware',
    'resources/views/director/dashboard.blade.php' => 'Director dashboard view'
];

foreach ($files as $file => $description) {
    $exists = file_exists(__DIR__ . '/'. $file);
    echo "   " . ($exists ? "✅" : "❌") . " $description\n";
}

echo "\n🎯 DIRECTOR ACCESS TEST COMPLETE!\n";
echo "==================================\n";
echo "To test director access:\n";
echo "1. Visit: http://localhost:8000/admin/login\n";
echo "2. Login with: director@smartprep.com / director123\n";
echo "3. Navigate to: http://localhost:8000/director/dashboard\n";
echo "4. Should see director dashboard with admin privileges\n";
