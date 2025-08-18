<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\FormRequirement;
use App\Observers\FormRequirementObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register FormRequirement observer for automatic column management
        FormRequirement::observe(FormRequirementObserver::class);
        
        // Register view composer for student sidebar
        view()->composer('components.student-sidebar', \App\Http\View\Composers\StudentSidebarComposer::class);
        
        // Register view composer for navbar to ensure navbar data is always available
        view()->composer('layouts.navbar', \App\Http\View\Composers\NavbarComposer::class);
        
        // Register view composer for login pages to ensure settings data is available
        view()->composer('Login.*', \App\Http\View\Composers\NavbarComposer::class);
        
        // Register view composer for SmartPrep views to ensure uiSettings data is available
        view()->composer('smartprep.*', \App\Http\View\Composers\NavbarComposer::class);
        
        // Register view composer for admin layouts to ensure navbar data is available
        view()->composer('admin.*', \App\Http\View\Composers\NavbarComposer::class);
        
        // Register view composer for student layouts to ensure navbar data is available
        view()->composer('student.*', \App\Http\View\Composers\NavbarComposer::class);
        
        // Register view composer for components to ensure navbar data is available
        view()->composer('components.*', \App\Http\View\Composers\NavbarComposer::class);
    }
}
