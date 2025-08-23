<?php
// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check website customization data and how it's applied
echo "🔍 ADMIN CUSTOMIZATION DATA INVESTIGATION\n";
echo "==========================================\n\n";

try {
    // Check if tenant_websites table exists and what data we have
    echo "Phase 1: Checking website customization data\n";
    echo "---------------------------------------------\n";
    
    $website = \Illuminate\Support\Facades\DB::table('tenant_websites')->where('id', 15)->first();
    
    if ($website) {
        echo "✅ Website ID 15 found:\n";
        echo "   Brand Name: " . ($website->brand_name ?? 'NULL') . "\n";
        echo "   Logo: " . ($website->logo ?? 'NULL') . "\n";
        echo "   Primary Color: " . ($website->primary_color ?? 'NULL') . "\n";
        echo "   Secondary Color: " . ($website->secondary_color ?? 'NULL') . "\n";
        echo "   Created: " . ($website->created_at ?? 'NULL') . "\n";
    } else {
        echo "❌ Website ID 15 not found in tenant_websites table\n";
        
        // Check what websites do exist
        $websites = \Illuminate\Support\Facades\DB::table('tenant_websites')->select('id', 'brand_name')->get();
        echo "\nAvailable websites:\n";
        foreach ($websites as $w) {
            echo "   ID {$w->id}: {$w->brand_name}\n";
        }
    }
    
    echo "\nPhase 2: Checking how customization is applied in admin routes\n";
    echo "--------------------------------------------------------------\n";
    
    // Test URL with website parameter
    $testUrl = 'http://127.0.0.1:8000/t/draft/test1/admin-dashboard?website=15&preview=true&t=' . time();
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ],
            'timeout' => 10
        ]
    ]);
    
    echo "Testing: $testUrl\n";
    $content = @file_get_contents($testUrl, false, $context);
    
    if ($content) {
        echo "✅ Page loaded (" . strlen($content) . " bytes)\n";
        
        // Check if customization is applied
        if (strpos($content, 'test1') !== false) {
            echo "✅ Custom brand name 'test1' found in content\n";
        } else {
            echo "❌ Custom brand name 'test1' NOT found in content\n";
        }
        
        // Check what brand name is actually used
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $content, $matches)) {
            echo "📄 Page title: " . trim($matches[1]) . "\n";
        }
        
        // Look for brand name in navbar or header
        if (preg_match('/brand[^>]*>([^<]+)</i', $content, $matches)) {
            echo "🏷️  Brand in navbar: " . trim($matches[1]) . "\n";
        } else {
            echo "⚠️  No brand name found in navbar\n";
        }
        
    } else {
        echo "❌ Failed to load page\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nPhase 3: Recommendations\n";
echo "------------------------\n";
echo "1. Check if admin preview routes apply website customization\n";
echo "2. Verify customization middleware is active for admin preview\n";
echo "3. Ensure navbar/layout templates use customization data\n";
echo "4. Test both dashboard and other admin pages for consistency\n";
?>
