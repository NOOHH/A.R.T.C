<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Log what's currently in the request when we make a real form submission
// This will be in Laravel's debug log

use Illuminate\Support\Facades\Log;

// Create a test route that logs the full request
Route::post('/test-smartprep-form', function(\Illuminate\Http\Request $request) {
    Log::info('SmartPrep Form Test - Full Request Data:', [
        'all_input' => $request->all(),
        'hero_title' => $request->input('hero_title'),
        'hero_title_length' => strlen($request->input('hero_title', '')),
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'content_type' => $request->header('Content-Type'),
        'expects_json' => $request->expectsJson(),
    ]);
    
    return response()->json([
        'success' => true,
        'received_data' => $request->all(),
        'hero_title' => $request->input('hero_title'),
        'hero_title_length' => strlen($request->input('hero_title', ''))
    ]);
})->middleware('web');

echo "Test route /test-smartprep-form created successfully.\n";
echo "You can now test form submission manually.\n";
