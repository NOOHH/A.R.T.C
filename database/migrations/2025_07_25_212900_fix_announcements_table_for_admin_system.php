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
        Schema::table('announcements', function (Blueprint $table) {
            // Make professor_id and program_id nullable since admin creates announcements
            $table->unsignedBigInteger('professor_id')->nullable()->change();
            $table->unsignedBigInteger('program_id')->nullable()->change();
            
            // Add admin_id for tracking who created the announcement
            $table->unsignedBigInteger('admin_id')->nullable()->after('announcement_id');
            
            // Update type enum to include more options
            $table->enum('type', ['general', 'urgent', 'event', 'system', 'video', 'assignment', 'quiz'])->default('general')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Revert the changes
            $table->unsignedBigInteger('professor_id')->nullable(false)->change();
            $table->unsignedBigInteger('program_id')->nullable(false)->change();
            $table->dropColumn('admin_id');
            $table->enum('type', ['general', 'video', 'assignment', 'quiz'])->default('general')->change();
        });
    }
};
