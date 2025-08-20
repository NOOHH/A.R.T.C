<?php

namespace Modules\Lms\Providers;

use Illuminate\Support\ServiceProvider;

class LmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Future shared LMS services (course repository, quiz engine, etc.)
    }

    public function boot(): void
    {
        // Future: publish migrations, config, etc.
    }
}
