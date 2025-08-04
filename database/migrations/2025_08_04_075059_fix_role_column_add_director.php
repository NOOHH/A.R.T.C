<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, we need to drop the ENUM constraint and recreate as VARCHAR
        // This is a MySQL-specific approach to handle ENUM to VARCHAR conversion
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(32) DEFAULT 'unverified'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to ENUM with original values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('unverified','student','professor') DEFAULT 'unverified'");
    }
};
