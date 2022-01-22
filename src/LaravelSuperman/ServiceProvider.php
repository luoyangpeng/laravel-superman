<?php

namespace Itas\LaravelSuperman;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Itas\LaravelSuperman\Commands\SupermanServeCommand;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
        $this->publishes([
            \dirname(__DIR__).'/config/superman.php' => config_path('superman.php'),
        ], 'config');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                SupermanServeCommand::class,
            ]);
          }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/superman.php', 'superman'
        );
    }
}