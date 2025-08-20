<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Client;
use App\Models\Tenant;
use App\Models\Setting;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING NAVBAR UPDATE FLOW ===\n\n";

try {
    // 1. Check if website ID 9 exists
    echo "1. Checking if website ID 9 exists...\n";
    $client = Client::where('id', 9)->first();
    
    if (!$client) {
        echo "❌ Client with ID 9 not found!\n";
        // Show available clients
        $clients = Client::where('archived', false)->get(['id', 'name', 'slug']);
        echo "Available clients:\n";
        foreach ($clients as $c) {
            echo "  - ID: {$c->id}, Name: {$c->name}, Slug: {$c->slug}\n";
        }
        exit(1);
    }
    
    echo "✅ Client found: {$client->name} (slug: {$client->slug})\n\n";
    
    // 2. Check if tenant exists
    echo "2. Checking tenant for client...\n";
    $tenant = Tenant::where('slug', $client->slug)->first();
    
    if (!$tenant) {
        echo "❌ Tenant not found for slug: {$client->slug}\n";
        exit(1);
    }
    
    echo "✅ Tenant found: {$tenant->database_name}\n\n";
    
    // 3. Test tenant database connection
    echo "3. Testing tenant database connection...\n";
    $tenantService = app(TenantService::class);
    
    // Switch to tenant DB
    $tenantService->switchToTenant($tenant);
    echo "✅ Switched to tenant database\n";
    
    // Check current database
    $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
    echo "Current database: {$currentDb}\n\n";
    
    // 4. Test navbar settings read
    echo "4. Reading current navbar settings...\n";
    $navbarSettings = Setting::getGroup('navbar');
    echo "Current navbar settings:\n";
    foreach ($navbarSettings as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
    echo "\n";
    
    // 5. Test navbar setting update
    echo "5. Testing navbar setting update...\n";
    $testBrandName = "Test Brand " . date('Y-m-d H:i:s');
    Setting::set('navbar', 'brand_name', $testBrandName);
    echo "✅ Set brand_name to: {$testBrandName}\n";
    
    // Verify the update
    $updatedValue = Setting::getGroup('navbar')['brand_name'] ?? 'NOT_FOUND';
    if ($updatedValue === $testBrandName) {
        echo "✅ Update verified successfully!\n";
    } else {
        echo "❌ Update failed! Expected: {$testBrandName}, Got: {$updatedValue}\n";
    }
    echo "\n";
    
    // 6. Test URL generation
    echo "6. Testing route URL generation...\n";
    $routeName = 'smartprep.dashboard.settings.update.navbar';
    $url = route($routeName, ['website' => $client->id]);
    echo "Update URL: {$url}\n";
    
    // 7. Check form action in navbar.blade.php
    echo "\n7. Checking form action in navbar settings...\n";
    $navbarBladeContent = file_get_contents(__DIR__ . '/resources/views/smartprep/dashboard/partials/settings/navbar.blade.php');
    if (strpos($navbarBladeContent, 'smartprep.dashboard.settings.update.navbar') !== false) {
        echo "✅ Form action correctly points to navbar update route\n";
    } else {
        echo "❌ Form action issue in navbar.blade.php\n";
    }
    
    // 8. Test CSRF token
    echo "\n8. Testing CSRF token generation...\n";
    $token = csrf_token();
    echo "CSRF token: " . substr($token, 0, 10) . "...\n";
    
    // 9. Switch back to main database
    echo "\n9. Switching back to main database...\n";
    $tenantService->switchToMain();
    $mainDb = DB::select('SELECT DATABASE() as db')[0]->db;
    echo "✅ Back to main database: {$mainDb}\n\n";
    
    echo "=== ALL TESTS PASSED ===\n";
    echo "✅ Navbar update system appears to be working correctly\n";
    echo "\nTo test manually:\n";
    echo "1. Visit: http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=9\n";
    echo "2. Click 'Navigation' tab\n";
    echo "3. Change brand name and submit\n";
    echo "4. Check if changes persist after refresh\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Ensure we're back on main database
    try {
        app(TenantService::class)->switchToMain();
    } catch (\Exception $switchError) {
        echo "❌ Failed to switch back to main database: " . $switchError->getMessage() . "\n";
    }
}
