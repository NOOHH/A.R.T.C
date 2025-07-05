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
        Schema::table('registrations', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('registrations', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('registrations', 'package_name')) {
                $table->string('package_name')->nullable()->after('package_id');
            }
            if (!Schema::hasColumn('registrations', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('package_name');
            }
            if (!Schema::hasColumn('registrations', 'plan_name')) {
                $table->string('plan_name')->nullable()->after('plan_id');
            }
            if (!Schema::hasColumn('registrations', 'program_id')) {
                $table->unsignedBigInteger('program_id')->nullable()->after('plan_name');
            }
            if (!Schema::hasColumn('registrations', 'program_name')) {
                $table->string('program_name')->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('registrations', 'enrollment_type')) {
                $table->string('enrollment_type', 20)->nullable()->after('program_name');
            }
            if (!Schema::hasColumn('registrations', 'learning_mode')) {
                $table->string('learning_mode', 50)->nullable()->after('enrollment_type');
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
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'package_id', 'package_name', 'plan_id', 'plan_name', 
                'program_id', 'program_name', 'enrollment_type', 'learning_mode'
            ]);
        });
    }
};
