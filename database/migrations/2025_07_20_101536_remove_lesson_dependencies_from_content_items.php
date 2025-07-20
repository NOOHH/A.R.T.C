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
        Schema::table('content_items', function (Blueprint $table) {
            // Drop lesson_id column if it exists
            if (Schema::hasColumn('content_items', 'lesson_id')) {
                $table->dropColumn('lesson_id');
            }
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
            // Add lesson_id column back if needed
            $table->unsignedBigInteger('lesson_id')->nullable();
        });
    }
};
