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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->string('student_id', 30);
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('package_id');
            $table->enum('payment_method', ['credit_card', 'gcash', 'bank_transfer', 'installment']);
            $table->decimal('amount', 10, 2);
            $table->enum('payment_status', ['pending', 'pending_verification', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->json('payment_details')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['student_id']);
            $table->index(['enrollment_id']);
            $table->index(['payment_status']);
            $table->index(['created_at']);

            // Foreign keys (we'll add these if the tables exist)
            // $table->foreign('enrollment_id')->references('enrollment_id')->on('enrollments')->onDelete('cascade');
            // $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            // $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            // $table->foreign('package_id')->references('package_id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
