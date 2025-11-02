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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // login, role_changed, etc.
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable(); // Additional data (old/new values, etc.)
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'event_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
