<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use App\Formatters\Utf8LineFormatter;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Set PHP internal encoding to UTF-8
        ini_set('default_charset', 'UTF-8');
        mb_internal_encoding('UTF-8');
        
        // Configure Monolog to use UTF-8 encoding
        $this->configureMonolog();
    }

    /**
     * Configure Monolog with proper UTF-8 encoding
     */
    protected function configureMonolog()
    {
        // Set the default timezone for logs
        date_default_timezone_set(config('app.timezone', 'UTC'));

        // Get the logger instance
        $logger = $this->app->make('log');
        
        // Configure all handlers to use UTF-8
        foreach ($logger->getHandlers() as $handler) {
            // Set up a custom UTF-8 formatter
            $formatter = new Utf8LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true, // Allow inline line breaks
                true  // Ignore empty context and extra
            );
            
            $handler->setFormatter($formatter);
        }
    }
}
