<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Native\Laravel\Facades\Window;

class NativePhpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure NativePHP window settings
        if ($this->app->runningInConsole() && class_exists(\Native\Laravel\Facades\Window::class)) {
            Window::open()
                ->id('main')
                ->title('Mini Splitwise')
                ->width(1200)
                ->height(800)
                ->minWidth(800)
                ->minHeight(600)
                ->url('/');
        }
    }
}
