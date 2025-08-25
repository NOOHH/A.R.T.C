<?php
echo "🛠️  IMPLEMENTING COMPREHENSIVE ENROLLMENT BUTTON FIX\n";
echo "===================================================\n\n";

// Step 1: Fix the enrollment.blade.php file
echo "1️⃣ FIXING ENROLLMENT BUTTON HARDCODED PATHS:\n";
echo "--------------------------------------------\n";

$enrollmentView = 'resources/views/welcome/enrollment.blade.php';
$backupFile = $enrollmentView . '.backup.' . date('Y-m-d-H-i-s');

if (file_exists($enrollmentView)) {
    // Create backup
    copy($enrollmentView, $backupFile);
    echo "✅ Backup created: $backupFile\n";
    
    $content = file_get_contents($enrollmentView);
    $originalContent = $content;
    
    // Fix 1: Replace hardcoded /enrollment/modular with route helper
    $content = str_replace(
        'data-url="/enrollment/modular"',
        'data-url="{{ route(\'enrollment.modular\') }}"',
        $content
    );
    
    // Fix 2: Replace JavaScript hardcoded paths
    $content = str_replace(
        "window.location.href = '/enrollment/modular';",
        "window.location.href = '{{ route('enrollment.modular') }}';",
        $content
    );
    
    // Fix 3: Replace onclick hardcoded path
    $content = str_replace(
        "onclick=\"window.location.href='/enrollment/modular'; return false;\"",
        "onclick=\"window.location.href='{{ route('enrollment.modular') }}'; return false;\"",
        $content
    );
    
    // Fix 4: Replace window.open hardcoded path
    $content = str_replace(
        "window.open('/enrollment/modular', '_self');",
        "window.open('{{ route('enrollment.modular') }}', '_self');",
        $content
    );
    
    // Fix 5: Replace form action hardcoded path
    $content = str_replace(
        "form.action = '/enrollment/modular';",
        "form.action = '{{ route('enrollment.modular') }}';",
        $content
    );
    
    // Fix 6: Replace any remaining standalone /enrollment/modular references
    $content = preg_replace(
        "/(['\"])\/enrollment\/modular(['\"])/",
        "$1{{ route('enrollment.modular') }}$2",
        $content
    );
    
    // Check if changes were made
    if ($content !== $originalContent) {
        file_put_contents($enrollmentView, $content);
        echo "✅ Enrollment view file updated with tenant-aware routing\n";
        
        // Count fixes applied
        $fixes = [
            'data-url attributes' => substr_count($originalContent, 'data-url="/enrollment/modular"'),
            'JavaScript redirects' => substr_count($originalContent, "window.location.href = '/enrollment/modular'"),
            'onclick handlers' => substr_count($originalContent, "onclick=\"window.location.href='/enrollment/modular'"),
            'window.open calls' => substr_count($originalContent, "window.open('/enrollment/modular'"),
            'form actions' => substr_count($originalContent, "form.action = '/enrollment/modular'")
        ];
        
        foreach ($fixes as $type => $count) {
            if ($count > 0) {
                echo "   ✅ Fixed $count $type\n";
            }
        }
    } else {
        echo "ℹ️  No changes needed - file already uses route helpers\n";
    }
    
} else {
    echo "❌ Enrollment view file not found\n";
    exit(1);
}

echo "\n2️⃣ VERIFYING TENANT CONTEXT AVAILABILITY:\n";
echo "-----------------------------------------\n";

// Check if there's a helper to get current tenant context
$helperFiles = [
    'app/Helpers/TenantHelper.php',
    'app/Services/TenantService.php',
    'app/Http/Middleware/TenantMiddleware.php'
];

foreach ($helperFiles as $file) {
    if (file_exists($file)) {
        echo "✅ Found: $file\n";
        $content = file_get_contents($file);
        
        // Check for tenant context methods
        if (strpos($content, 'getCurrentTenant') !== false) {
            echo "   ✅ Has getCurrentTenant method\n";
        }
        if (strpos($content, 'switchToTenant') !== false) {
            echo "   ✅ Has switchToTenant method\n";
        }
    } else {
        echo "❌ Not found: $file\n";
    }
}

echo "\n3️⃣ ADDING TENANT-AWARE ENROLLMENT HELPER:\n";
echo "-----------------------------------------\n";

// Create a helper to generate tenant-aware enrollment URLs
$helperContent = '<?php

if (!function_exists(\'tenant_enrollment_url\')) {
    /**
     * Generate tenant-aware enrollment URL
     * 
     * @param string $type (\'full\' or \'modular\')
     * @return string
     */
    function tenant_enrollment_url($type = \'full\') {
        // Check if we\'re in tenant context
        $request = request();
        
        // Extract tenant from current URL
        $currentUrl = $request->url();
        
        // Pattern: /t/{tenant}/ or /t/draft/{tenant}/
        if (preg_match(\'/\/t\/(?:draft\/)?([^\/]+)/\', $currentUrl, $matches)) {
            $tenant = $matches[1];
            $isDraft = strpos($currentUrl, \'/draft/\') !== false;
            
            if ($isDraft) {
                return url("/t/draft/{$tenant}/enrollment/{$type}");
            } else {
                return url("/t/{$tenant}/enrollment/{$type}");
            }
        }
        
        // Fallback to regular route
        return route("enrollment.{$type}");
    }
}

if (!function_exists(\'current_tenant_slug\')) {
    /**
     * Get current tenant slug from URL
     * 
     * @return string|null
     */
    function current_tenant_slug() {
        $request = request();
        $currentUrl = $request->url();
        
        if (preg_match(\'/\/t\/(?:draft\/)?([^\/]+)/\', $currentUrl, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}

if (!function_exists(\'is_draft_tenant\')) {
    /**
     * Check if current context is draft tenant
     * 
     * @return bool
     */
    function is_draft_tenant() {
        $request = request();
        return strpos($request->url(), \'/t/draft/\') !== false;
    }
}
';

$helperFile = 'app/Helpers/TenantEnrollmentHelper.php';
$helperDir = dirname($helperFile);

if (!is_dir($helperDir)) {
    mkdir($helperDir, 0755, true);
    echo "✅ Created helpers directory\n";
}

file_put_contents($helperFile, $helperContent);
echo "✅ Created tenant enrollment helper: $helperFile\n";

echo "\n4️⃣ UPDATING COMPOSER TO AUTOLOAD HELPERS:\n";
echo "-----------------------------------------\n";

$composerFile = 'composer.json';
if (file_exists($composerFile)) {
    $composer = json_decode(file_get_contents($composerFile), true);
    
    // Add helpers to autoload files
    if (!isset($composer['autoload']['files'])) {
        $composer['autoload']['files'] = [];
    }
    
    $helperPath = 'app/Helpers/TenantEnrollmentHelper.php';
    if (!in_array($helperPath, $composer['autoload']['files'])) {
        $composer['autoload']['files'][] = $helperPath;
        
        file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "✅ Added helper to composer autoload\n";
        echo "ℹ️  Run \'composer dump-autoload\' to reload helpers\n";
    } else {
        echo "✅ Helper already in composer autoload\n";
    }
} else {
    echo "❌ Composer.json not found\n";
}

echo "\n=== FIX IMPLEMENTATION COMPLETE ===\n";
echo "Next: Creating comprehensive tests...\n";
?>
