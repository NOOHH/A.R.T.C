<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop existing primary key
        DB::statement('ALTER TABLE `admin_settings` DROP PRIMARY KEY');
        // Modify setting_id to auto-increment and set as primary key
        DB::statement('ALTER TABLE `admin_settings` MODIFY `setting_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`setting_id`)');
    }

    public function down()
    {
        // Drop primary key
        DB::statement('ALTER TABLE `admin_settings` DROP PRIMARY KEY');
        // Modify setting_id to remove auto-increment
        DB::statement('ALTER TABLE `admin_settings` MODIFY `setting_id` BIGINT UNSIGNED NOT NULL');
        // Re-add primary key
        DB::statement('ALTER TABLE `admin_settings` ADD PRIMARY KEY (`setting_id`)');
    }
};
