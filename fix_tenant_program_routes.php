<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$filePath = 'app/Http/Controllers/Tenant/TenantAdminProgramController.php';
$content = file_get_contents($filePath);

// Replace all incorrect route references
$content = str_replace(
    "->route('tenant.admin.programs.index', ['tenant' => \$tenant])",
    "->route('tenant.draft.admin.programs', ['tenant' => \$tenant])",
    $content
);

file_put_contents($filePath, $content);

echo "Fixed all route references in TenantAdminProgramController.php\n";
echo "Changed 'tenant.admin.programs.index' to 'tenant.draft.admin.programs'\n";
