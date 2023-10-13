<?php

namespace Muhanz\Shoapi;

use Illuminate\Support\ServiceProvider;

class ShoapiServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'muhanz');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'muhanz');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shoapi.php', 'shoapi');
        $this->mergeConfigFrom(__DIR__.'/../config/shoapi_path.php', 'shoapi_path');

        // Register the service the package provides.
        $this->app->singleton('shoapi', function ($app) {
            return new Shoapi;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['shoapi'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/shoapi.php' => config_path('shoapi.php'),
        ], 'shoapi.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/muhanz'),
        ], 'shoapi.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/muhanz'),
        ], 'shoapi.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/muhanz'),
        ], 'shoapi.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
