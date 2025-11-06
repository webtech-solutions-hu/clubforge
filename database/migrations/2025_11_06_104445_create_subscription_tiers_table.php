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
        Schema::create('subscription_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // starter, pro, club_plus, enterprise
            $table->string('display_name'); // Starter, Pro, Club+, Enterprise
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('billing_period')->default('monthly'); // monthly, yearly

            // Capacity Limits
            $table->integer('max_users')->nullable()->comment('null = unlimited');
            $table->bigInteger('max_storage_mb')->nullable()->comment('null = unlimited, storage in MB');

            // Feature Flags
            $table->boolean('analytics_basic')->default(true);
            $table->boolean('analytics_advanced')->default(false);
            $table->boolean('analytics_export')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('remove_branding')->default(false);
            $table->boolean('custom_domain')->default(false);
            $table->boolean('webhooks_enabled')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('sso_enabled')->default(false);
            $table->boolean('advanced_moderation')->default(false);

            // Support Level
            $table->string('support_level')->default('email'); // email, priority, dedicated
            $table->integer('support_response_hours')->default(72);

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_tiers');
    }
};
