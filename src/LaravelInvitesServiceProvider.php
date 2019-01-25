<?php

namespace mathewparet\LaravelInvites;

use mathewparet\LaravelInvites\LaravelInvites;
use mathewparet\LaravelInvites\Commands\GenerateInvitations;
use mathewparet\LaravelInvites\Commands\CheckInvitation;
use mathewparet\LaravelInvites\Commands\Cleanup;

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
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'laravelinvites');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->publishes([
            __DIR__.'/../config/laravelinvites.php' => config_path('laravelinvites.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Views/Mail/InvitationMailMarkdown.blade.php' => base_path('resources/views/Mail/InvitationMailMarkdown.blade.php'),
        ], 'mail');

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
        $this->app->singleton('laravelinvites', function(/** @scrutinizer ignore-unused */ $app) {
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

        // Registering package commands.
        $this->commands([
            GenerateInvitations::class,
            CheckInvitation::class,
            Cleanup::class,
        ]);
    }
}
