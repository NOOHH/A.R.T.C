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
        Schema::create('module_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
            
            $table->foreign('module_id')->references('modules_id')->on('modules')->onDelete('cascade');
            $table->foreign('subject_id')->references('subject_id')->on('subjects')->onDelete('cascade');
            
            $table->unique(['module_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_subjects');
    }
};
