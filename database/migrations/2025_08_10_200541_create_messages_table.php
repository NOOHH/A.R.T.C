<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('sender_id')->nullable(false);
            $table->string('50')('sender_type')->nullable(false);
            $table->bigInteger('receiver_id')->nullable(false);
            $table->string('50')('receiver_type')->nullable(false);
            $table->text('message')->nullable(false);
            $table->timestamp('sent_at')->nullable(false)->default('current_timestamp()');
            $table->boolean('is_read')->nullable(false)->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->text('content')->nullable(false);
            $table->timestamp('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
