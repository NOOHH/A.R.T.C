<?php

namespace Clients\ARTC\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ArtcServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Placeholder for ARTC specific bindings later
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }
        
        // Load all route files inside clients/ARTC/routes
        foreach (glob(__DIR__.'/../../routes/*.php') as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }
        $this->loadViewsFrom(resource_path('views'), 'artc'); // Optionally add dedicated path later
    }
}
