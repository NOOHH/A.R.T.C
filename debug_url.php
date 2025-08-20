<?php
// Simple test to see what URL is being accessed by the iframe
echo "Current URL: " . request()->fullUrl() . "\n";
echo "Path: " . request()->path() . "\n";
echo "Segments: " . implode('/', request()->segments()) . "\n";
echo "Is t/*: " . (request()->is('t/*') ? 'YES' : 'NO') . "\n";

if (request()->is('t/*')) {
    $segments = request()->segments();
    if (count($segments) >= 2 && $segments[0] === 't') {
        $tenantSlug = $segments[1];
        echo "Tenant slug from URL: {$tenantSlug}\n";
        
        $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
        if ($tenant) {
            echo "Found tenant: {$tenant->name} (DB: {$tenant->database_name})\n";
        } else {
            echo "No tenant found for slug: {$tenantSlug}\n";
        }
    }
}
?>
