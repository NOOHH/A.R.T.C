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
            // Check if table exists and has basic structure
            if (!Schema::hasTable('registrations')) {
                return;
            }

            // Add columns only if they don't exist
            if (!Schema::hasColumn('registrations', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'student_school')) {
                $table->string('student_school', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'street_address')) {
                $table->string('street_address', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'state_province')) {
                $table->string('state_province', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'city')) {
                $table->string('city', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'zipcode')) {
                $table->string('zipcode', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'contact_number')) {
                $table->string('contact_number', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'emergency_contact_number')) {
                $table->string('emergency_contact_number', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'Start_Date')) {
                $table->date('Start_Date')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'status')) {
                $table->string('status', 50)->default('pending');
            }
            if (!Schema::hasColumn('registrations', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'package_name')) {
                $table->string('package_name', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'plan_name')) {
                $table->string('plan_name', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'program_id')) {
                $table->unsignedBigInteger('program_id')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'program_name')) {
                $table->string('program_name', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'good_moral')) {
                $table->string('good_moral', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'PSA')) {
                $table->string('PSA', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'Course_Cert')) {
                $table->string('Course_Cert', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'TOR')) {
                $table->string('TOR', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'Cert_of_Grad')) {
                $table->string('Cert_of_Grad', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'Undergraduate')) {
                $table->string('Undergraduate', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'Graduate')) {
                $table->string('Graduate', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'photo_2x2')) {
                $table->string('photo_2x2', 255)->nullable();
            }

            // Dynamic fields that can be added/removed
            if (!Schema::hasColumn('registrations', 'phone_number')) {
                $table->string('phone_number', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'telephone_number')) {
                $table->string('telephone_number', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'religion')) {
                $table->string('religion', 100)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'citizenship')) {
                $table->string('citizenship', 100)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'civil_status')) {
                $table->string('civil_status', 50)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'birthdate')) {
                $table->date('birthdate')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'gender')) {
                $table->string('gender', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'work_experience')) {
                $table->text('work_experience')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'preferred_schedule')) {
                $table->string('preferred_schedule', 50)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 100)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'health_conditions')) {
                $table->text('health_conditions')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'disability_support')) {
                $table->boolean('disability_support')->default(false);
            }
            if (!Schema::hasColumn('registrations', 'valid_id')) {
                $table->string('valid_id', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'birth_certificate')) {
                $table->string('birth_certificate', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'diploma_certificate')) {
                $table->string('diploma_certificate', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'medical_certificate')) {
                $table->string('medical_certificate', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'passport_photo')) {
                $table->string('passport_photo', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'parent_guardian_name')) {
                $table->string('parent_guardian_name', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'parent_guardian_contact')) {
                $table->string('parent_guardian_contact', 20)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'previous_school')) {
                $table->string('previous_school', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'graduation_year')) {
                $table->year('graduation_year')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'course_taken')) {
                $table->string('course_taken', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'special_needs')) {
                $table->text('special_needs')->nullable();
            }
            if (!Schema::hasColumn('registrations', 'scholarship_program')) {
                $table->string('scholarship_program', 255)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'employment_status')) {
                $table->string('employment_status', 100)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'monthly_income')) {
                $table->decimal('monthly_income', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('registrations', 'registration_id')) {
                $table->id('registration_id')->first();
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
            // We don't drop columns to preserve historical data
            // This is intentional for the "no data loss" requirement
        });
    }
};
