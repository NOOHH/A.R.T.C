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
    }
}
