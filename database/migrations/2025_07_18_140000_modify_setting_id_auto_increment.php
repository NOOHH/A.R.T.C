<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Enable auto-increment on setting_id
        DB::statement('ALTER TABLE `admin_settings` MODIFY `setting_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        // Revert auto-increment change
        DB::statement('ALTER TABLE `admin_settings` MODIFY `setting_id` BIGINT UNSIGNED NOT NULL');
    }
};
