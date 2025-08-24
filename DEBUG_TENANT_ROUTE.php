<?php
// Quick debugging script to test the tenant route directly
echo "=== DIRECT TENANT ROUTE TEST ===\n";

try {
    // Directly test the PreviewController
    $tenantSlug = 'test1';
    
    // Check if the tenant exists
    $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
    if (!$tenant) {
        echo "❌ Tenant '$tenantSlug' not found in database\n";
        echo "Available tenants:\n";
        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $t) {
            echo "- {$t->slug}\n";
        }
        exit;
    }
    
    echo "✅ Tenant '$tenantSlug' found: {$tenant->name}\n";
    echo "Database: {$tenant->database_name}\n\n";
    
    // Test tenant service
    $tenantService = app(\App\Services\TenantService::class);
    $tenantService->switchToTenant($tenant);
    
    echo "✅ Successfully switched to tenant database\n";
    
    // Test settings loading
    $settings = [
        'homepage' => \App\Models\Setting::getGroup('homepage')->toArray(),
        'navbar' => \App\Models\Setting::getGroup('navbar')->toArray(),
    ];
    
    echo "✅ Settings loaded successfully\n";
    echo "Homepage settings count: " . count($settings['homepage']) . "\n";
    echo "Navbar settings count: " . count($settings['navbar']) . "\n";
    
    // Switch back
    $tenantService->switchToMain();
    echo "✅ Switched back to main database\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
