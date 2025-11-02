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
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable()->after('email_verified_at');
            $table->text('address')->nullable()->after('city');
            $table->string('mobile')->nullable()->after('address');
            $table->json('social_media_links')->nullable()->after('mobile');
            $table->text('bio')->nullable()->after('social_media_links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'address',
                'mobile',
                'social_media_links',
                'bio',
            ]);
        });
    }
};
