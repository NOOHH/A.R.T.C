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
        Schema::table('registrations', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('resubmitted_at');
            $table->bigInteger('approved_by')->nullable()->after('approved_at');
            $table->text('undo_reason')->nullable()->after('approved_by');
            $table->timestamp('undone_at')->nullable()->after('undo_reason');
            $table->bigInteger('undone_by')->nullable()->after('undone_at');
            $table->json('fields_to_redo')->nullable()->after('undone_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'approved_at',
                'approved_by', 
                'undo_reason',
                'undone_at',
                'undone_by',
                'fields_to_redo'
            ]);
        });
    }
};