<?php

namespace App\Providers;

use App\Listeners\JobProcessedListener;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Listen for successful job completions
        Event::listen(
            JobProcessed::class,
            JobProcessedListener::class,
        );

        // Register model observers
        User::observe(UserObserver::class);
    }
}
