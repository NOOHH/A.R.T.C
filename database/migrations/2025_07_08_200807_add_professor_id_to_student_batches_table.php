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
        Schema::table('student_batches', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('description'); // Admin who created the batch
            $table->integer('professor_id')->nullable()->after('created_by');
            $table->timestamp('professor_assigned_at')->nullable()->after('professor_id');
            $table->integer('professor_assigned_by')->nullable()->after('professor_assigned_at'); // Admin who assigned
            
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('set null');
            $table->foreign('professor_assigned_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_batches', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropForeign(['professor_assigned_by']);
            $table->dropColumn(['created_by', 'professor_id', 'professor_assigned_at', 'professor_assigned_by']);
        });
    }
};
