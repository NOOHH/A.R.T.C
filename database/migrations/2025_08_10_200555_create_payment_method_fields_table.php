<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentmethodfieldsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_method_fields', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('payment_method_id')->nullable(false);
            $table->string('255')('field_name')->nullable(false);
            $table->string('255')('field_label')->nullable(false);
            $table->text('field_type')->nullable(false);
            $table->text('field_options');
            $table->boolean('is_required')->nullable(false)->default(1);
            $table->integer('sort_order')->nullable(false)->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payment_method_fields');
    }
}
