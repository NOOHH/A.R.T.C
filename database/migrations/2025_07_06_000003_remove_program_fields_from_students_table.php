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
        Schema::table('students', function (Blueprint $table) {
            // Remove columns that should be in enrollments table instead
            if (Schema::hasColumn('students', 'program_id')) {
                $table->dropColumn('program_id');
            }
            if (Schema::hasColumn('students', 'package_id')) {
                $table->dropColumn('package_id');
            }
            if (Schema::hasColumn('students', 'plan_id')) {
                $table->dropColumn('plan_id');
            }
            if (Schema::hasColumn('students', 'program_name')) {
                $table->dropColumn('program_name');
            }
            if (Schema::hasColumn('students', 'package_name')) {
                $table->dropColumn('package_name');
            }
            if (Schema::hasColumn('students', 'plan_name')) {
                $table->dropColumn('plan_name');
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
        Schema::table('students', function (Blueprint $table) {
            $table->integer('program_id')->nullable();
            $table->integer('package_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->string('program_name', 100)->nullable();
            $table->string('package_name', 100)->nullable();
            $table->string('plan_name', 50)->nullable();
        });
    }
};
