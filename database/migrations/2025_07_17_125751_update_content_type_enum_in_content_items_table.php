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
        Schema::table('content_items', function (Blueprint $table) {
            // Update the enum to include 'lesson' type
            DB::statement("ALTER TABLE content_items MODIFY content_type ENUM('assignment', 'quiz', 'test', 'link', 'video', 'lesson')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_items', function (Blueprint $table) {
            // Revert the enum to original values
            DB::statement("ALTER TABLE content_items MODIFY content_type ENUM('assignment', 'quiz', 'test', 'link', 'video', 'document')");
        });
    }
};
