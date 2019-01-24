<?php

namespace mathewparet\LaravelInvites;

use mathewparet\LaravelInvites\LaravelInvites;
use mathewparet\LaravelInvites\Commands\GenerateInvitations;
use mathewparet\LaravelInvites\Commands\CheckInvitation;

use Illuminate\Support\ServiceProvider;
use Validator;

class LaravelInvitesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mathewparet');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mathewparet');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        Validator::extend('valid_code', 'LaravelInvites@validate', ':attribute is invalid.');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelinvites.php', 'laravelinvites');

        // Register the service the package provides.
        $this->app->singleton('laravelinvites', function ($app) {
            return new LaravelInvites;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelinvites'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelinvites.php' => config_path('laravelinvites.php'),
        ], 'config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mathewparet'),
        ], 'laravelinvites.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mathewparet'),
        ], 'laravelinvites.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/mathewparet'),
        ], 'laravelinvites.views');*/

        // Registering package commands.
        $this->commands([
            GenerateInvitations::class,
            CheckInvitation::class,
        ]);
    }
}
