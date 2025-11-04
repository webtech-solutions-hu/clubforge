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
        // First, add new columns before renaming
        Schema::table('notifications', function (Blueprint $table) {
            // Recipient type: 'global', 'user', 'role'
            $table->enum('recipient_type', ['global', 'user', 'role'])->default('user')->after('user_id');

            // For role-based messages, store role IDs as JSON array
            $table->json('recipient_roles')->nullable()->after('recipient_type');

            // Make user_id nullable for global messages
            $table->foreignId('user_id')->nullable()->change();

            // Add sender information
            $table->foreignId('sender_id')->nullable()->after('id')->constrained('users')->nullOnDelete();

            // Add priority field
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal')->after('message');
        });

        // Rename column type to category
        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('type', 'category');
        });

        // Add new indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['recipient_type', 'created_at'], 'notif_recipient_type_created_at_idx');
            $table->index(['sender_id', 'created_at'], 'notif_sender_id_created_at_idx');
        });

        // Finally, rename the table
        Schema::rename('notifications', 'messages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to notifications
        Schema::rename('messages', 'notifications');

        // Drop new indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notif_recipient_type_created_at_idx');
            $table->dropIndex('notif_sender_id_created_at_idx');
        });

        // Rename category back to type
        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('category', 'type');
        });

        // Drop new columns
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['recipient_type', 'recipient_roles', 'sender_id', 'priority']);

            // Make user_id not nullable again
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
