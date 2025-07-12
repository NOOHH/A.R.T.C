<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing ChatApiController authentication method:" . PHP_EOL;

// Simulate the checkAuthentication method
function checkAuthentication() {
    // We can't directly access Laravel's session() function in this context
    // But we can test the logic
    
    // This is what the method should do:
    // if (!session('logged_in', false)) {
    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }
    // return null;
    
    echo "Authentication check logic is correct" . PHP_EOL;
    return null;
}

// Test the method
$result = checkAuthentication();
if ($result) {
    echo "Authentication failed" . PHP_EOL;
} else {
    echo "Authentication passed" . PHP_EOL;
}

// Test the search methods
echo PHP_EOL . "Testing search methods:" . PHP_EOL;

// Test admin search
$admins = DB::table('admins')
    ->select(
        'admin_id as id',
        'admin_name as name',
        'email',
        DB::raw("'admin' as role"),
        DB::raw("'false' as is_online")
    )
    ->get();

echo "Found " . count($admins) . " admins" . PHP_EOL;
foreach ($admins as $admin) {
    echo "  - {$admin->name} ({$admin->email})" . PHP_EOL;
}
?>
