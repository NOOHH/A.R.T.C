<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('directors', function (Blueprint $table) {
            $table->integer('directors_id')->nullable(false)->primary();
            $table->integer('admin_id');
            $table->string('100')('directors_name')->nullable(false);
            $table->string('100')('directors_first_name')->nullable(false);
            $table->string('100')('directors_last_name')->nullable(false);
            $table->string('100')('directors_email')->nullable(false);
            $table->string('255')('directors_password')->nullable(false);
            $table->string('20')('referral_code');
            $table->boolean('directors_archived')->default(0);
            $table->boolean('has_all_program_access')->nullable(false)->default(1);
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('directors');
    }
}
