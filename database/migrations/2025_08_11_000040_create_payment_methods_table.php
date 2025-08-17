<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->string('method_name');
            $table->enum('method_type', ['credit_card', 'gcash', 'maya', 'bank_transfer', 'cash', 'other']);
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};

