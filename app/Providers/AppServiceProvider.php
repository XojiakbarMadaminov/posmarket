<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\CartService::class, fn () => new \App\Services\CartService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
