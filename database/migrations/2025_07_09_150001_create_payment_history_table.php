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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id('payment_history_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('student_id')->nullable();
            $table->unsignedInteger('program_id');
            $table->unsignedInteger('package_id');
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'gcash', 'other'])->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->unsignedBigInteger('processed_by_admin_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('enrollment_id')->references('enrollment_id')->on('enrollments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('package_id')->references('package_id')->on('packages')->onDelete('cascade');
            $table->foreign('processed_by_admin_id')->references('admin_id')->on('admins')->onDelete('set null');
            
            // Indexes
            $table->index(['enrollment_id', 'payment_status']);
            $table->index('user_id');
            $table->index('student_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_history');
    }
};
