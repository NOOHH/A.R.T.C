<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagemodulesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('package_modules', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('package_id')->nullable(false);
            $table->bigInteger('modules_id')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('package_modules');
    }
}
