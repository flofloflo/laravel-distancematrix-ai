<?php

namespace Mybit\LaravelDistancematrixAi;

use Illuminate\Support\ServiceProvider;

class LaravelDistancematrixAiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('distancematrix-ai.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'distancematrix-ai');

        // Register the main class to use with the facade
        $this->app->bind('laravel-distancematrix-ai', function($app) {
            return new DistanceMatrix();
        });
    }
}
