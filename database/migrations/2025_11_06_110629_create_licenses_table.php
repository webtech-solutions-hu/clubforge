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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->string('tier'); // starter, pro, club_plus, enterprise
            $table->string('status')->default('active'); // active, expired, suspended

            // License dates
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

            // Usage tracking
            $table->integer('current_users')->default(0);
            $table->bigInteger('current_storage_mb')->default(0);

            // Metadata
            $table->json('metadata')->nullable(); // For additional license info

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
