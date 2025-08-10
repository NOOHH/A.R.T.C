<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigInteger('chat_id')->nullable(false)->primary();
            $table->bigInteger('sender_id')->nullable(false);
            $table->bigInteger('receiver_id')->nullable(false);
            $table->text('body_cipher')->nullable(false);
            $table->boolean('is_read')->default(0);
            $table->boolean('is_encrypted')->nullable(false)->default(1);
            $table->timestamp('sent_at')->nullable(false)->default('current_timestamp()');
            $table->timestamp('read_at');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
