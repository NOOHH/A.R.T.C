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
            // Add missing columns that exist in the actual database but not in Laravel migrations
            if (!Schema::hasColumn('students', 'program_id')) {
                $table->integer('program_id')->nullable();
            }
            if (!Schema::hasColumn('students', 'package_id')) {
                $table->integer('package_id')->nullable();
            }
            if (!Schema::hasColumn('students', 'plan_id')) {
                $table->integer('plan_id')->nullable();
            }
            if (!Schema::hasColumn('students', 'program_name')) {
                $table->string('program_name', 100)->nullable();
            }
            if (!Schema::hasColumn('students', 'package_name')) {
                $table->string('package_name', 100)->nullable();
            }
            if (!Schema::hasColumn('students', 'plan_name')) {
                $table->string('plan_name', 50)->nullable();
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
            $table->dropColumn([
                'program_id',
                'package_id', 
                'plan_id',
                'program_name',
                'package_name',
                'plan_name'
            ]);
        });
    }
};
