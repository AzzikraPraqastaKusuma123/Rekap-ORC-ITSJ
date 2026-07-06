<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

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

        // Professional Security: Rate Limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(120)->by($request->session()->getId() ?: $request->ip());
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $user = auth()->user();
            $role = $user ? $user->role : 'user';

            $roleTheme = match ($role) {
                'superadmin' => [
                    'bg' => 'bg-blue-600',
                    'bgHover' => 'hover:bg-blue-700',
                    'text' => 'text-blue-600',
                    'textDark' => 'dark:text-blue-400',
                    'gradient' => 'to-blue-400',
                    'ring' => 'focus:ring-blue-500',
                    'badgeBg' => 'bg-blue-500/10',
                    'badgeBorder' => 'border-blue-500/20',
                    'icon' => 'text-blue-500',
                    'svgBg' => 'bg-blue-500/20',
                    'svgPulse' => 'bg-blue-500',
                    'shadow' => 'shadow-[0_2px_10px_#3b82f6]',
                    'gradientHex' => 'from-blue-500 via-cyan-400 to-blue-600',
                    'borderGlow' => 'hover:border-blue-500/30',
                    'brandPrimary' => '#2563eb',
                    'brandHover' => '#1d4ed8',
                ],
                'admin' => [
                    'bg' => 'bg-emerald-600',
                    'bgHover' => 'hover:bg-emerald-700',
                    'text' => 'text-emerald-600',
                    'textDark' => 'dark:text-emerald-400',
                    'gradient' => 'to-emerald-400',
                    'ring' => 'focus:ring-emerald-500',
                    'badgeBg' => 'bg-emerald-500/10',
                    'badgeBorder' => 'border-emerald-500/20',
                    'icon' => 'text-emerald-500',
                    'svgBg' => 'bg-emerald-500/20',
                    'svgPulse' => 'bg-emerald-500',
                    'shadow' => 'shadow-[0_2px_10px_#10b981]',
                    'gradientHex' => 'from-emerald-500 via-teal-400 to-emerald-600',
                    'borderGlow' => 'hover:border-emerald-500/30',
                    'brandPrimary' => '#059669',
                    'brandHover' => '#047857',
                ],
                'staff' => [
                    'bg' => 'bg-amber-600',
                    'bgHover' => 'hover:bg-amber-700',
                    'text' => 'text-amber-600',
                    'textDark' => 'dark:text-amber-400',
                    'gradient' => 'to-amber-400',
                    'ring' => 'focus:ring-amber-500',
                    'badgeBg' => 'bg-amber-500/10',
                    'badgeBorder' => 'border-amber-500/20',
                    'icon' => 'text-amber-500',
                    'svgBg' => 'bg-amber-500/20',
                    'svgPulse' => 'bg-amber-500',
                    'shadow' => 'shadow-[0_2px_10px_#f59e0b]',
                    'gradientHex' => 'from-amber-500 via-orange-400 to-amber-600',
                    'borderGlow' => 'hover:border-amber-500/30',
                    'brandPrimary' => '#d97706',
                    'brandHover' => '#b45309',
                ],
                default => [
                    'bg' => 'bg-blue-600',
                    'bgHover' => 'hover:bg-blue-700',
                    'text' => 'text-blue-600',
                    'textDark' => 'dark:text-blue-400',
                    'gradient' => 'to-blue-400',
                    'ring' => 'focus:ring-blue-500',
                    'badgeBg' => 'bg-blue-500/10',
                    'badgeBorder' => 'border-blue-500/20',
                    'icon' => 'text-blue-500',
                    'svgBg' => 'bg-blue-500/20',
                    'svgPulse' => 'bg-blue-500',
                    'shadow' => 'shadow-[0_2px_10px_#3b82f6]',
                    'gradientHex' => 'from-blue-500 via-cyan-400 to-blue-600',
                    'borderGlow' => 'hover:border-blue-500/30',
                    'brandPrimary' => '#2563eb',
                    'brandHover' => '#1d4ed8',
                ],
            };
            $view->with('roleTheme', $roleTheme);
        });
    }
}
