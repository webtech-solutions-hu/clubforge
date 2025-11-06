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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_tier_id')->constrained('subscription_tiers')->onDelete('restrict');
            $table->string('status')->default('active'); // active, cancelled, expired, trial

            // Subscription Dates
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Usage Tracking
            $table->integer('current_users')->default(0);
            $table->bigInteger('current_storage_mb')->default(0);

            // Billing Information (placeholder for future payment integration)
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
