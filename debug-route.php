<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Temporary route without middleware for debugging payment history
Route::get('/debug-payment-history', function() {
    // Create controller instance and call the method directly
    $controller = new AdminController();
    return $controller->paymentHistory();
});
?>
