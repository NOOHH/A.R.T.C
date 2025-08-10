<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsocketsstatisticsentriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('websockets_statistics_entries', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary();
            $table->string('255')('app_id')->nullable(false);
            $table->integer('peak_connection_count')->nullable(false);
            $table->integer('websocket_message_count')->nullable(false);
            $table->integer('api_message_count')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('websockets_statistics_entries');
    }
}
