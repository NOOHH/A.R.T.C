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
            // Add package, program, and plan fields
            $table->unsignedBigInteger('package_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('program_id')->nullable()->after('package_id');
            $table->unsignedBigInteger('plan_id')->nullable()->after('program_id');
            
            // Add name fields for easy display
            $table->string('package_name')->nullable()->after('plan_id');
            $table->string('program_name')->nullable()->after('package_name');
            $table->string('plan_name')->nullable()->after('program_name');
            
            // Add enrollment type and learning mode
            $table->enum('enrollment_type', ['full', 'modular'])->nullable()->after('plan_name');
            $table->enum('learning_mode', ['synchronous', 'asynchronous'])->nullable()->after('enrollment_type');
            
            // Add foreign key constraints if the tables exist
            if (Schema::hasTable('packages')) {
                $table->foreign('package_id')->references('package_id')->on('packages')->onDelete('set null');
            }
            if (Schema::hasTable('programs')) {
                $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
            }
            if (Schema::hasTable('plans')) {
                $table->foreign('plan_id')->references('plan_id')->on('plans')->onDelete('set null');
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
            // Drop foreign keys first
            if (Schema::hasTable('packages')) {
                $table->dropForeign(['package_id']);
            }
            if (Schema::hasTable('programs')) {
                $table->dropForeign(['program_id']);
            }
            if (Schema::hasTable('plans')) {
                $table->dropForeign(['plan_id']);
            }
            
            // Drop columns
            $table->dropColumn([
                'package_id',
                'program_id', 
                'plan_id',
                'package_name',
                'program_name',
                'plan_name',
                'enrollment_type',
                'learning_mode'
            ]);
        });
    }
};
