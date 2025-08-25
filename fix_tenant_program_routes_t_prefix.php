<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing TenantAdminProgramController route names for t/ prefixed routes...\n";

$controllerPath = 'app/Http/Controllers/Tenant/TenantAdminProgramController.php';
$content = file_get_contents($controllerPath);

// Replace route names to match the t/ prefixed routes
$replacements = [
    "->route('tenant.draft.admin.programs', ['tenant' => \$tenant])" => "->route('tenant.admin.programs.index', ['tenant' => \$tenant])",
    "->route('tenant.draft.admin.programs.store', ['tenant' => \$tenant])" => "->route('tenant.admin.programs.store', ['tenant' => \$tenant])",
    "->route('tenant.draft.admin.programs.batch-store', ['tenant' => \$tenant])" => "->route('tenant.admin.programs.batch-store', ['tenant' => \$tenant])",
    "->route('tenant.draft.admin.programs.destroy', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.programs.destroy', ['tenant' => \$tenant, 'id' => \$id])",
    "->route('tenant.draft.admin.programs.toggle-archive', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.programs.toggle-archive', ['tenant' => \$tenant, 'id' => \$id])",
];

$updatedContent = $content;
foreach ($replacements as $old => $new) {
    $updatedContent = str_replace($old, $new, $updatedContent);
}

if ($updatedContent !== $content) {
    file_put_contents($controllerPath, $updatedContent);
    echo "✅ Updated TenantAdminProgramController route names\n";
    
    // Count the changes
    $changes = 0;
    foreach ($replacements as $old => $new) {
        $changes += substr_count($content, $old);
    }
    echo "📊 Made {$changes} route name replacements\n";
} else {
    echo "ℹ️ No changes needed - route names already correct\n";
}

// Also update TenantAdminPackageController
$packageControllerPath = 'app/Http/Controllers/Tenant/TenantAdminPackageController.php';
if (file_exists($packageControllerPath)) {
    $packageContent = file_get_contents($packageControllerPath);
    
    $packageReplacements = [
        "->route('tenant.draft.admin.packages', ['tenant' => \$tenant])" => "->route('tenant.admin.packages.index', ['tenant' => \$tenant])",
        "->route('tenant.draft.admin.packages.store', ['tenant' => \$tenant])" => "->route('tenant.admin.packages.store', ['tenant' => \$tenant])",
        "->route('tenant.draft.admin.packages.show', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.packages.show', ['tenant' => \$tenant, 'id' => \$id])",
        "->route('tenant.draft.admin.packages.update', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.packages.update', ['tenant' => \$tenant, 'id' => \$id])",
        "->route('tenant.draft.admin.packages.destroy', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.packages.destroy', ['tenant' => \$tenant, 'id' => \$id])",
        "->route('tenant.draft.admin.packages.archive', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.packages.archive', ['tenant' => \$tenant, 'id' => \$id])",
        "->route('tenant.draft.admin.packages.restore', ['tenant' => \$tenant, 'id' => \$id])" => "->route('tenant.admin.packages.restore', ['tenant' => \$tenant, 'id' => \$id])",
    ];
    
    $updatedPackageContent = $packageContent;
    foreach ($packageReplacements as $old => $new) {
        $updatedPackageContent = str_replace($old, $new, $updatedPackageContent);
    }
    
    if ($updatedPackageContent !== $packageContent) {
        file_put_contents($packageControllerPath, $updatedPackageContent);
        echo "✅ Updated TenantAdminPackageController route names\n";
        
        $packageChanges = 0;
        foreach ($packageReplacements as $old => $new) {
            $packageChanges += substr_count($packageContent, $old);
        }
        echo "📊 Made {$packageChanges} package route name replacements\n";
    } else {
        echo "ℹ️ No changes needed for package controller - route names already correct\n";
    }
}

echo "🎯 Route name fixes completed!\n";
