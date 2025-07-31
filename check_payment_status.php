<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "Payment statuses in database:\n";
$statuses = DB::table('payments')->select('payment_status')->distinct()->get();
foreach ($statuses as $status) {
    $count = DB::table('payments')->where('payment_status', $status->payment_status)->count();
    echo "- {$status->payment_status}: {$count} records\n";
}
?>
