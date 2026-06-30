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
        // Force HTTPS URL scheme only when NOT accessing locally via localhost/127.0.0.1
        if (str_starts_with(config('app.url'), 'https://')) {
            $host = request()->getHost();
            if (!in_array($host, ['127.0.0.1', 'localhost', '::1'])) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}
