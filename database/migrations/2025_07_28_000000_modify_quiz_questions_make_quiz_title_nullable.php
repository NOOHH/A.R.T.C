<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make quiz_title column nullable with default null to avoid missing default errors.
     *
     * @return void
     */
    public function up()
    {
        // Using raw statement to avoid requiring doctrine/dbal
        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE `quiz_questions` MODIFY `quiz_title` VARCHAR(255) NULL DEFAULT NULL;'
        );
    }

    /**
     * Reverse the migrations.
     * Revert quiz_title to NOT NULL without default.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE `quiz_questions` MODIFY `quiz_title` VARCHAR(255) NOT NULL;'
        );
    }
};
