<?php

namespace Creem\Laravel;

use Illuminate\Support\ServiceProvider;

class CreemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge the package config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/creem.php', 'creem'
        );

        // Register the main class as a singleton
        // We use the class name as the key so Dependency Injection works
        $this->app->singleton(Creem::class, function ($app) {
            return new Creem(
                $app['config']->get('creem.api_key'),
                $app['config']->get('creem.api_url')
            );
        });
        
        // Also register it with a simple name 'creem' for the Facade
        $this->app->alias(Creem::class, 'creem');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Allow users to publish the config file
        $this->publishes([
            __DIR__.'/../config/creem.php' => config_path('creem.php'),
        ], 'creem-config');
    }
}