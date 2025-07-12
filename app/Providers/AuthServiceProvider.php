<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Message' => 'App\Policies\MessagePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define gates for message authorization
        Gate::define('send-message', function ($user, $receiver) {
            return $user->can('sendMessage', [$receiver]);
        });

        Gate::define('view-messages', function ($user, $otherUser) {
            return $user->can('viewMessages', [$otherUser]);
        });
    }
}
