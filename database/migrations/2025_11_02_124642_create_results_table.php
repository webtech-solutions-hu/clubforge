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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->nullable(); // Points/score
            $table->integer('ranking')->nullable(); // Placement (1st, 2nd, etc.)
            $table->integer('experience_points')->nullable(); // XP for RPG
            $table->text('narrative_outcome')->nullable(); // Story outcome for RPG
            $table->json('achievements')->nullable(); // Badges, awards, etc.
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
