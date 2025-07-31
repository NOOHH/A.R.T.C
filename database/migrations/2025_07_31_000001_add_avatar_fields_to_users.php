<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add avatar field to admins table if it doesn't exist
        if (!Schema::hasColumn('admins', 'avatar')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('email');
            });
        }

        // Add avatar field to professors table if it doesn't exist
        if (!Schema::hasColumn('professors', 'avatar')) {
            Schema::table('professors', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('professor_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });

        Schema::table('professors', function (Blueprint $table) {
            if (Schema::hasColumn('professors', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });
    }
};
