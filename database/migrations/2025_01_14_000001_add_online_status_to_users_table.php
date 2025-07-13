<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add online status and last seen timestamp
            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('role');
            }
            
            if (!Schema::hasColumn('users', 'last_seen')) {
                $table->timestamp('last_seen')->nullable()->after('is_online');
            }
            
            // Add indexes for better performance
            $table->index(['role', 'is_online'], 'users_role_online_index');
            $table->index('last_seen', 'users_last_seen_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('users_role_online_index');
            $table->dropIndex('users_last_seen_index');
            
            // Drop columns
            if (Schema::hasColumn('users', 'is_online')) {
                $table->dropColumn('is_online');
            }
            
            if (Schema::hasColumn('users', 'last_seen')) {
                $table->dropColumn('last_seen');
            }
        });
    }
};
