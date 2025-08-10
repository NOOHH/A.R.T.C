<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('professors', function (Blueprint $table) {
            $table->integer('professor_id')->nullable(false)->primary();
            $table->integer('admin_id')->nullable(false);
            $table->string('100')('professor_name')->nullable(false);
            $table->string('100')('professor_first_name')->nullable(false);
            $table->string('100')('professor_last_name')->nullable(false);
            $table->string('100')('professor_email')->nullable(false);
            $table->string('255')('professor_password')->nullable(false);
            $table->string('20')('referral_code');
            $table->string('255')('profile_photo');
            $table->text('dynamic_data');
            $table->boolean('professor_archived')->default(0);
            $table->timestamp('created_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable(false)->default('current_timestamp()');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('professors');
    }
}
