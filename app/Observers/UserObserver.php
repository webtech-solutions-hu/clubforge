<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;

class UserObserver
{
    /**
     * Handle the User "creating" event - check capacity before allowing creation
     */
    public function creating(User $user): bool
    {
        // Check if we can add a new user
        if (!SubscriptionService::canAddUser()) {
            $tier = SubscriptionService::currentTier();
            $limit = $tier?->max_users ?? 200;

            throw new \Exception("User limit reached. Your current plan allows a maximum of {$limit} users. Please upgrade your subscription to add more users.");
        }

        return true;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Update user count in subscription
        $this->updateSubscriptionUserCount();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Update user count in subscription
        $this->updateSubscriptionUserCount();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Update user count in subscription
        $this->updateSubscriptionUserCount();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Update user count in subscription
        $this->updateSubscriptionUserCount();
    }

    /**
     * Update the license user count
     */
    protected function updateSubscriptionUserCount(): void
    {
        $license = SubscriptionService::current();

        if ($license) {
            $license->updateUserCount();
        }
    }
}
