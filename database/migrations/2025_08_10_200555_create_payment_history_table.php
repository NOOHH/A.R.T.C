<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymenthistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payment_history', function (Blueprint $table) {
            $table->bigInteger('payment_history_id')->nullable(false)->primary();
            $table->integer('enrollment_id')->nullable(false);
            $table->integer('user_id');
            $table->string('255')('student_id');
            $table->integer('program_id')->nullable(false);
            $table->integer('package_id')->nullable(false);
            $table->decimal('10', '2')('amount');
            $table->enum(''pending','paid','failed','refunded','cancelled','processing'')('payment_status');
            $table->enum(''cash','card','bank_transfer','gcash','manual','other'')('payment_method');
            $table->text('payment_notes');
            $table->timestamp('payment_date');
            $table->integer('processed_by_admin_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payment_history');
    }
}
