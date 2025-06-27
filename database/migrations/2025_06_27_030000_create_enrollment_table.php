<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollment', function (Blueprint $table) {
            $table->increments('enrollment_id');
            $table->string('Modular_enrollment', 50);
            $table->string('Complete_Program', 50);
            $table->unsignedInteger('package_id');
            $table->foreign('package_id')->references('package_id')->on('packages');
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollment');
    }
};
