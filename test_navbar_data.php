<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Test the NavbarComposer
use App\Http\View\Composers\NavbarComposer;
use Illuminate\View\View;

echo "=== Testing NavbarComposer Data ===\n";

// Create a mock view
$viewFactory = app('view');
$view = $viewFactory->make('layouts.navbar');

// Create composer and test
$composer = new NavbarComposer();
$composer->compose($view);

// Get the data that was passed to the view
$data = $view->getData();

echo "Navbar data passed to view:\n";
var_dump($data['navbar'] ?? 'NO NAVBAR DATA');

echo "\nSettings data passed to view:\n";
var_dump($data['settings'] ?? 'NO SETTINGS DATA');

echo "\n=== Direct database check ===\n";
$navbarFromDB = \App\Models\UiSetting::getSection('navbar');
echo "Direct from database:\n";
var_dump($navbarFromDB->toArray());
