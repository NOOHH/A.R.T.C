<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ensure basic permission tables exist (simplified for seed purposes)
        DB::statement("CREATE TABLE IF NOT EXISTS roles (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) UNIQUE, guard_name VARCHAR(255) DEFAULT 'web', created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)");
        DB::statement("CREATE TABLE IF NOT EXISTS permissions (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) UNIQUE, guard_name VARCHAR(255) DEFAULT 'web', created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)");
        DB::statement("CREATE TABLE IF NOT EXISTS role_has_permissions (permission_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY (permission_id, role_id))");

        // Seed default roles for client tenants
        $roles = ['client_super_admin','admin','professor','student','user'];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role], ['guard_name' => 'web']);
        }
    }

    public function down(): void
    {
        // no-op
    }
};



