<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('class_meetings') || !Schema::hasTable('admins')) {
            return; // Safety: one of the tables not present
        }

        Schema::table('class_meetings', function (Blueprint $table) {
            // Add foreign key for created_by referencing admins primary key (admin_id or id)
            if (Schema::hasColumn('admins', 'admin_id')) {
                $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('cascade');
            } elseif (Schema::hasColumn('admins', 'id')) {
                $table->foreign('created_by')->references('id')->on('admins')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('class_meetings')) {
            return;
        }
        Schema::table('class_meetings', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
    }
};
