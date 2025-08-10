<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentmethodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigInteger('payment_method_id')->nullable(false)->primary();
            $table->string('255')('method_name')->nullable(false);
            $table->enum(''credit_card','gcash','maya','bank_transfer','cash','other'')('method_type')->nullable(false);
            $table->text('description');
            $table->string('255')('qr_code_path');
            $table->text('instructions');
            $table->text('dynamic_fields');
            $table->boolean('is_enabled')->nullable(false)->default(1);
            $table->integer('sort_order')->nullable(false)->default(0);
            $table->bigInteger('created_by_admin_id')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
