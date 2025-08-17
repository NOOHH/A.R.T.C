<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupMultiTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:multi-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the multi-tenant system with separate databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up multi-tenant system...');

        // Step 1: Run migrations on main database (smartprep)
        $this->info('Running migrations on main database (smartprep)...');
        $this->call('migrate', ['--database' => 'mysql']);

        // Step 2: Run seeders on main database
        $this->info('Running seeders on main database...');
        $this->call('db:seed', ['--database' => 'mysql']);

        // Step 3: Run migrations on tenant database (smartprep_artc)
        $this->info('Running migrations on tenant database (smartprep_artc)...');
        $this->call('migrate', ['--database' => 'tenant']);

        $this->info('Multi-tenant system setup completed!');
        $this->info('Main database (smartprep): Landing page and admin management');
        $this->info('Tenant database (smartprep_artc): Client/tenant data');
        $this->info('Access tenant at: /t/artc');
    }
}
