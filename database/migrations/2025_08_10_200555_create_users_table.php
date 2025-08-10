<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false)->primary();
            $table->bigInteger('admin_id');
            $table->bigInteger('directors_id');
            $table->boolean('is_online')->nullable(false)->default(0);
            $table->timestamp('last_seen');
            $table->string('100')('email')->nullable(false);
            $table->string('255')('user_firstname')->nullable(false);
            $table->string('255')('user_lastname')->nullable(false);
            $table->string('255')('password')->nullable(false);
            $table->string('32')('role')->default('unverified');
            $table->integer('enrollment_id');
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
