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
        $this->app->bind(
            \App\Repositories\ReceiptRepositoryInterface::class,
            \App\Repositories\ReceiptRepository::class
        );
        $this->app->bind(
            \App\Repositories\AnalyticsRepositoryInterface::class,
            \App\Repositories\AnalyticsRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
