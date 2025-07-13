<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Add encryption flag (if we want to track which messages are encrypted)
            $table->boolean('is_encrypted')->default(true)->after('message');
            
            // Add indexes for better performance
            $table->index(['sender_id', 'receiver_id', 'sent_at']);
            $table->index(['receiver_id', 'read_at']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('is_encrypted');
            $table->dropIndex(['sender_id', 'receiver_id', 'sent_at']);
            $table->dropIndex(['receiver_id', 'read_at']);
            $table->dropIndex('sent_at');
        });
    }
};
