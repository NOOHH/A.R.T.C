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
        Schema::table('announcements', function (Blueprint $table) {
            // Add targeting options
            $table->json('target_users')->nullable()->after('type'); // professors, students, directors
            $table->json('target_programs')->nullable()->after('target_users'); // specific program IDs
            $table->json('target_batches')->nullable()->after('target_programs'); // specific batch IDs
            $table->json('target_plans')->nullable()->after('target_batches'); // full, modular, both
            $table->enum('target_scope', ['all', 'specific'])->default('all')->after('target_plans');
            $table->text('description')->nullable()->after('content'); // description field
            $table->timestamp('publish_date')->nullable()->after('description');
            $table->timestamp('expire_date')->nullable()->after('publish_date');
            $table->boolean('is_published')->default(true)->after('expire_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn([
                'target_users',
                'target_programs', 
                'target_batches',
                'target_plans',
                'target_scope',
                'description',
                'publish_date',
                'expire_date',
                'is_published'
            ]);
        });
    }
};
