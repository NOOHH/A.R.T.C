<?php

namespace App\Console\Commands;

use App\Services\TenantService;
use Illuminate\Console\Command;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with a copy of the template database';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');

        $this->info("Creating tenant: $name ($domain)");

        try {
            $tenant = $this->tenantService->createTenant($name, $domain);
            
            $this->info("✅ Tenant created successfully!");
            $this->table(['ID', 'Name', 'Domain', 'Database'], [
                [$tenant->id, $tenant->name, $tenant->domain, $tenant->database_name]
            ]);
            
            $this->info("The tenant database has been created with the same structure as smartprep_artc");
            $this->info("You can now access this tenant at: http://$domain");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to create tenant: " . $e->getMessage());
            return 1;
        }
    }
}
