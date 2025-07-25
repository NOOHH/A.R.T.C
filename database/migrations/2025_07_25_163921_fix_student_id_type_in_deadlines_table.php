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
        Schema::table('deadlines', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['student_id']);
            
            // Change student_id from unsignedBigInteger to string to match students table
            $table->string('student_id', 20)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deadlines', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['student_id']);
            
            // Change back to unsignedBigInteger
            $table->unsignedBigInteger('student_id')->change();
            
            // Note: We can't restore the original foreign key because it would fail
            // if there are string student_ids in the table
        });
    }
};
