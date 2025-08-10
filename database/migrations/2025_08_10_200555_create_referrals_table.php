<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigInteger('referral_id')->nullable(false)->primary();
            $table->string('20')('referral_code')->nullable(false);
            $table->enum(''director','professor'')('referrer_type')->nullable(false);
            $table->integer('referrer_id')->nullable(false);
            $table->string('30')('student_id')->nullable(false);
            $table->integer('registration_id')->nullable(false);
            $table->timestamp('used_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
