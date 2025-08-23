<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('debug:route-middleware', function () {
    $this->comment('=== ROUTE MIDDLEWARE ANALYSIS ===');
    
    // Test different route patterns to see which middleware is applied
    $testRoutes = [
        '/admin/modules/archived',
        '/admin/quiz-generator',
        '/admin/modules/course-content-upload',
        '/t/draft/smartprep/admin/archived', // Working tenant route
    ];
    
    foreach ($testRoutes as $testPath) {
        $this->info("Testing path: $testPath");
        
        // Find the route that matches this path
        $route = null;
        foreach (\Illuminate\Support\Facades\Route::getRoutes() as $routeItem) {
            if ($routeItem->matches(request()->create($testPath, 'GET'))) {
                $route = $routeItem;
                break;
            }
        }
        
        if ($route) {
            $this->line("Route found: " . ($route->getName() ?: 'unnamed'));
            $this->line("Route URI: {$route->uri()}");
            $this->line("Middleware: " . implode(', ', $route->gatherMiddleware()));
            $this->line("Controller: {$route->getActionName()}");
        } else {
            $this->line("No route found for this path");
        }
        
        $this->line("---");
    }
})->purpose('Debug middleware applied to routes');
