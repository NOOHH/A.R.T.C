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
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'learning_mode')) {
                $table->enum('learning_mode', ['synchronous', 'asynchronous'])->nullable()->after('enrollment_type');
            }
            if (!Schema::hasColumn('enrollments', 'batch_id')) {
                $table->unsignedBigInteger('batch_id')->nullable()->after('learning_mode');
                $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            }
            if (!Schema::hasColumn('enrollments', 'start_date')) {
                $table->date('start_date')->nullable()->after('batch_id');
            }
            if (!Schema::hasColumn('enrollments', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
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
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('enrollments', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('enrollments', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('enrollments', 'learning_mode')) {
                $table->dropColumn('learning_mode');
            }
        });
    }
};
