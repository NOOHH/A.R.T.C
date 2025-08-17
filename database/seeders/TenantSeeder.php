<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tenant::create([
            'name' => 'A.R.T.C Training Center',
            'slug' => 'artc',
            'database_name' => 'smartprep_artc',
            'domain' => null,
            'status' => 'active',
            'settings' => [
                'site_title' => 'A.R.T.C Training Center',
                'tagline' => 'Excellence in Professional Training',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
            ]
        ]);
    }
}
