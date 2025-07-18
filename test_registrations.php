<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Registration;

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Query pending registrations
$registrations = Registration::where('status', 'pending')->get(['enrollment_type', 'firstname', 'lastname', 'created_at']);

echo "Pending Registrations:\n";
echo "Count: " . $registrations->count() . "\n\n";

foreach ($registrations as $reg) {
    echo "- " . ($reg->enrollment_type ?? 'NULL') . " enrollment by " . $reg->firstname . " " . $reg->lastname . " (" . $reg->created_at . ")\n";
}
