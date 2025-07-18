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
        Schema::create('registration_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('registration_id');
            $table->integer('module_id');
            $table->unsignedBigInteger('subject_id')->nullable(); // Using subject_id instead of course_id
            $table->integer('package_id')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('registration_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('module_id')->references('modules_id')->on('modules')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('courses')->onDelete('cascade');
            $table->foreign('package_id')->references('package_id')->on('packages')->onDelete('cascade');
            
            // Ensure unique combination
            $table->unique(['registration_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_modules');
    }
};
