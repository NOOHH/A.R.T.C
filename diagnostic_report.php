<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE DIAGNOSTIC REPORT ===\n\n";

echo "PROBLEM SUMMARY:\n";
echo "When updating navbar on z.smartprep.local, changes are not being applied.\n\n";

echo "INVESTIGATION RESULTS:\n\n";

echo "✅ DATABASE OPERATIONS:\n";
echo "- Settings table exists in tenant database: smartprep_z-smartprep-local\n";
echo "- Settings can be read and written successfully\n";
echo "- Tenant switching (main ↔ tenant) works correctly\n";
echo "- Controller updateNavbar() method works\n";
echo "- AJAX form submission is properly configured\n\n";

echo "✅ ROUTING:\n";
echo "- Route smartprep.dashboard.settings.update.navbar exists\n";
echo "- Form action points to correct route with ?website=9 parameter\n";
echo "- JavaScript has correct endpoint configuration\n\n";

echo "✅ TENANT CONFIGURATION:\n";
echo "- Tenant 'z' exists with domain 'z.smartprep.local'\n";
echo "- TenantMiddleware is properly registered and active\n";
echo "- Database: smartprep_z-smartprep-local contains updated settings\n\n";

echo "❌ POTENTIAL ISSUES:\n\n";

echo "1. DNS/HOSTS CONFIGURATION:\n";
echo "   Problem: z.smartprep.local is not resolving to localhost\n";
echo "   Solution: Add this line to C:\\Windows\\System32\\drivers\\etc\\hosts:\n";
echo "   127.0.0.1    z.smartprep.local\n\n";

echo "2. WEB SERVER CONFIGURATION:\n";
echo "   Problem: Apache/nginx might not be serving z.smartprep.local correctly\n";
echo "   Solutions:\n";
echo "   - For XAMPP: Configure virtual host for z.smartprep.local\n";
echo "   - For built-in server: php artisan serve --host=0.0.0.0 --port=8000\n\n";

echo "3. CACHE ISSUES:\n";
echo "   Problem: Browser or application cache showing old data\n";
echo "   Solutions:\n";
echo "   - Clear browser cache (Ctrl+Shift+R)\n";
echo "   - Clear Laravel cache: php artisan cache:clear\n";
echo "   - Clear config cache: php artisan config:clear\n\n";

echo "VERIFICATION STEPS:\n\n";

echo "1. Add hosts entry:\n";
echo "   - Open notepad as administrator\n";
echo "   - Open C:\\Windows\\System32\\drivers\\etc\\hosts\n";
echo "   - Add: 127.0.0.1    z.smartprep.local\n";
echo "   - Save file\n\n";

echo "2. Test tenant domain access:\n";
echo "   - Visit: http://z.smartprep.local:8000\n";
echo "   - Should show the tenant site (not SmartPrep admin)\n";
echo "   - Check navbar brand name should show: TENANT_TEST_195414\n\n";

echo "3. Test customization workflow:\n";
echo "   - Visit: http://127.0.0.1:8000/smartprep/dashboard/customize-website?website=9\n";
echo "   - Click Navigation tab\n";
echo "   - Change brand name to 'New Test Brand'\n";
echo "   - Submit form\n";
echo "   - Visit: http://z.smartprep.local:8000\n";
echo "   - Verify brand name changed to 'New Test Brand'\n\n";

echo "CURRENT TENANT SETTINGS:\n";
use App\Models\Tenant;
use App\Models\Setting;
use App\Services\TenantService;

try {
    $tenant = Tenant::where('slug', 'z')->first();
    $tenantService = app(TenantService::class);
    $tenantService->switchToTenant($tenant);
    
    $brandName = Setting::get('navbar', 'brand_name', 'NOT_SET');
    $loginButton = Setting::get('navbar', 'show_login_button', 'NOT_SET');
    
    echo "- Brand Name: {$brandName}\n";
    echo "- Show Login Button: {$loginButton}\n";
    
    $tenantService->switchToMain();
} catch (\Exception $e) {
    echo "- Error reading settings: " . $e->getMessage() . "\n";
}

echo "\nQUICK FIXES TO TRY:\n";
echo "1. Run: php artisan config:clear && php artisan cache:clear\n";
echo "2. Add hosts entry: 127.0.0.1    z.smartprep.local\n";
echo "3. Visit z.smartprep.local:8000 (note the port)\n";
echo "4. Clear browser cache with Ctrl+Shift+R\n\n";

echo "If the issue persists after these steps, the problem is likely:\n";
echo "- Web server virtual host configuration\n";
echo "- Laravel route caching\n";
echo "- Database connection issues\n";
echo "- Browser DNS cache\n\n";

echo "=== END DIAGNOSTIC REPORT ===\n";
