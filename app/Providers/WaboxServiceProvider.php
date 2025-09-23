<?php

namespace App\Providers;

use App\Services\WaboxService;
use Illuminate\Support\ServiceProvider;

class WaboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WaboxService::class, function ($app) {
            return new WaboxService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
