<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigInteger('payment_id')->nullable(false)->primary();
            $table->bigInteger('enrollment_id')->nullable(false);
            $table->string('30')('student_id')->nullable(false);
            $table->bigInteger('program_id')->nullable(false);
            $table->bigInteger('package_id')->nullable(false);
            $table->enum(''credit_card','gcash','bank_transfer','cash','admin_marked'')('payment_method')->nullable(false);
            $table->decimal('10', '2')('amount')->nullable(false);
            $table->enum(''pending','paid','failed','cancelled','rejected','resubmitted'')('payment_status')->default('pending');
            $table->text('rejection_reason');
            $table->bigInteger('rejected_by');
            $table->timestamp('rejected_at');
            $table->text('rejected_fields');
            $table->timestamp('resubmitted_at');
            $table->integer('resubmission_count')->default(0);
            $table->text('payment_details');
            $table->bigInteger('verified_by');
            $table->timestamp('verified_at');
            $table->string('255')('receipt_number');
            $table->string('255')('reference_number');
            $table->text('notes');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
