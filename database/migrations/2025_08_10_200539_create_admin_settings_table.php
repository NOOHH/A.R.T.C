<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->bigInteger('setting_id')->nullable(false)->primary();
            $table->string('255')('setting_key')->nullable(false);
            $table->text('setting_value')->nullable(false);
            $table->text('setting_description');
            $table->boolean('is_active')->nullable(false)->default(1);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('admin_settings');
    }
}
