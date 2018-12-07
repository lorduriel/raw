<?php

namespace LoRDFM\Raw;

use Illuminate\Support\ServiceProvider;
use LoRDFM\Raw\Commands\RepositoryCommand;

class RawServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('raw.php'),
        ], "config");


        $this->commands([
            RepositoryCommand::class
        ]);

        /*
        if(config('raw') != null){
            if (file_exists(base_path(config('raw')['routes_default_path']))) {
                echo "exists";
                $routeGroups = scandir(base_path(config('raw')['routes_default_path']));
                foreach ($routeGroups as $routeGroup) {
                    if($routeGroup != '.' && $routeGroup != '..'){
                        $this->loadRoutesFrom(base_path(config('raw')['routes_default_path'])."\\".$routeGroup);
                    }
                }
            }
        }
        */
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Raw::class, function ($app) {
            return new Raw();
        });

        $this->app->alias(Raw::class, 'Raw');
    }
}