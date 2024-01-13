<?php

namespace MasudZaman\Trails;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class TrailsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigration();
        $this->bootMacros();
    }

    /**
     * Publish Trails configuration
     */
    protected function publishConfig()
    {
        // Publish config files
        $this->publishes([
            realpath(__DIR__.'/config/trails.php') => config_path('trails.php'),
        ], 'config');
    }

    /**
     * Publish Trails migration
     */
    protected function publishMigration()
    {
        $published_migration = glob(database_path('/migrations/*_create_trails_table.php'));
        if (count($published_migration) === 0) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_trails_table.php.stub' => database_path('/migrations/' . date('Y_m_d_His') . '_create_trails_table.php'),
            ], 'migrations');
        }
    }

    protected function bootMacros()
    {
        Request::macro('trail', function () {
            return App::make(TrailerInterface::class)->trail($this);
        });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        // Bring in configuration values
        $this->mergeConfigFrom(
            __DIR__ . '/config/trails.php',
            'trails'
        );

        $this->app->bind(TrackingFilterInterface::class, function ($app) {
            return $app->make(config('trails.tracking_filter'));
        });

        $this->app->bind(TrackingLoggerInterface::class, function ($app) {
            return $app->make(config('trails.tracking_logger'));
        });

        $this->app->singleton(TrailerInterface::class, function ($app) {
            return $app->make(config('trails.trailer'));
        });

        $this->commands([
            Console\PruneCommand::class,
        ]);
    }
}
