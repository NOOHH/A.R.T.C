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
            // Make problematic fields nullable to prevent registration form errors
            $table->string('student_school', 50)->nullable()->change();
            $table->string('street_address', 50)->nullable()->change();
            $table->string('state_province', 50)->nullable()->change();
            $table->string('city', 50)->nullable()->change();
            $table->string('zipcode', 20)->nullable()->change();
            $table->string('contact_number', 15)->nullable()->change();
            $table->date('Start_Date')->nullable()->change();
            $table->boolean('disability_support')->default(0)->nullable()->change();
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
            // Revert the fields back to non-nullable (if needed)
            $table->string('student_school', 50)->nullable(false)->change();
            $table->string('street_address', 50)->nullable(false)->change();
            $table->string('state_province', 50)->nullable(false)->change();
            $table->string('city', 50)->nullable(false)->change();
            $table->string('zipcode', 20)->nullable(false)->change();
            $table->string('contact_number', 15)->nullable(false)->change();
            $table->date('Start_Date')->nullable(false)->change();
            $table->boolean('disability_support')->default(0)->nullable(false)->change();
        });
    }
};
