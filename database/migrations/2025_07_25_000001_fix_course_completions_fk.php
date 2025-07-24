<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Drop the old foreign key by its actual name
        try { DB::statement('ALTER TABLE course_completions DROP FOREIGN KEY fk_cc_course'); } catch (\Exception $e) {}
        Schema::table('course_completions', function (Blueprint $table) {
            $table->foreign('course_id')->references('subject_id')->on('courses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('course_completions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
    }
}; 