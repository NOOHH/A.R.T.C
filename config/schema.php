<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Schema Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration controls how Laravel handles database schemas.
    | You can disable automatic schema loading here.
    |
    */

    'auto_load' => env('LARAVEL_LOAD_SCHEMA', false),
    
    'schema_path' => env('LARAVEL_LOAD_PATH', database_path('schema/mysql-schema.sql')),
    
    'database' => [
        'user' => env('LARAVEL_LOAD_USER'),
        'password' => env('LARAVEL_LOAD_PASSWORD'),
        'host' => env('LARAVEL_LOAD_HOST'),
        'port' => env('LARAVEL_LOAD_PORT'),
        'name' => env('LARAVEL_LOAD_DATABASE'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Schema Loading Method
    |--------------------------------------------------------------------------
    |
    | Choose how to load schemas:
    | - 'mysql': Use MySQL command line client (requires mysql client)
    | - 'laravel': Use Laravel's database connection (recommended)
    | - 'disabled': Disable schema loading entirely
    |
    */
    
    'method' => env('SCHEMA_LOADING_METHOD', 'disabled'),
];
