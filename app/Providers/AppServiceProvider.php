<?php

namespace App\Providers;

use App\Services\ModuleService;
use App\Support\ActivityLogger;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleService::class, fn (Application $app) => new ModuleService);
        $this->registerExtensions();
    }

    private function registerExtensions()
    {
        $this->app->extend(SpatieActivityLogger::class, function (SpatieActivityLogger $logger, Application $app) {
            return new ActivityLogger($logger);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
