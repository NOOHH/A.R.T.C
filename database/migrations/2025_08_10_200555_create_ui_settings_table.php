<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUisettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ui_settings', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('section')->nullable(false);
            $table->string('255')('setting_key')->nullable(false);
            $table->text('setting_value')->nullable(false);
            $table->text('setting_type')->nullable(false)->default('text');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ui_settings');
    }
}
