<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('plan', function (Blueprint $table) {
            $table->integer('plan_id')->nullable(false)->primary();
            $table->string('50')('plan_name')->nullable(false);
            $table->text('description');
            $table->boolean('enable_synchronous')->nullable(false)->default(1);
            $table->boolean('enable_asynchronous')->nullable(false)->default(1);
            $table->text('learning_mode_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('plan');
    }
}
