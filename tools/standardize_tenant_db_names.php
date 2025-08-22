<?php
// Utility script to list tenant databases and SUGGEST (not perform) rename commands
// Pattern we want: smartprep_<slug>
// Some older tenants might have names like smartprep_<slug>-smartprep-local

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

$tenants = Tenant::all();

echo "Inspecting " . $tenants->count() . " tenants...\n\n";
foreach ($tenants as $t) {
    $expected = 'smartprep_' . $t->slug;
    if ($t->database_name !== $expected) {
        echo "Tenant ID {$t->id} slug={$t->slug}\n";
        echo "  Current DB : {$t->database_name}\n";
        echo "  Expected DB: {$expected}\n";
        echo "  ACTION: Manually create & migrate or rename DB (MySQL doesn't support easy rename pre-8.0).\n";
        echo "    CREATE DATABASE IF NOT EXISTS `{$expected}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        echo "    -- Copy schema & data (excluding volatile tables as needed)\n";
        echo "    SET @src='{$t->database_name}', @dst='{$expected}';\n";
        echo "    -- For each table: CREATE TABLE `@dst`.`table` LIKE `@src`.`table`; INSERT INTO `@dst`.`table` SELECT * FROM `@src`.`table`;\n";
        echo "    UPDATE tenants SET database_name='{$expected}' WHERE id={$t->id};\n\n";
    }
}

echo "Done. No changes performed automatically.\n";
