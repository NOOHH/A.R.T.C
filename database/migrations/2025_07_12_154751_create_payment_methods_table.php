<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['is_enabled']);
            $table->index(['sort_order']);
            $table->index(['method_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};
