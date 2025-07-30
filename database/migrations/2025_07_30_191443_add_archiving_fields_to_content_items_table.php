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
        Schema::table('content_items', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('is_active');
            $table->timestamp('archived_at')->nullable()->after('is_archived');
            $table->unsignedBigInteger('archived_by_professor_id')->nullable()->after('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'archived_at', 'archived_by_professor_id']);
        });
    }
};
