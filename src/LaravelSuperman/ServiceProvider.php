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
        
    }
}