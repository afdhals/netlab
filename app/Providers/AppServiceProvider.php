<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Application services can be registered here as the prototype grows.
    }

    public function boot(): void
    {
        // No external services are required by this learning module.
    }
}
