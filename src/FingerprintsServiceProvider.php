<?php

namespace MasudZaman\Fingerprints;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FingerprintsServiceProvider extends ServiceProvider
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
     * Publish Fingerprints configuration
     */
    protected function publishConfig()
    {
        // Publish config files
        $this->publishes([
            realpath(__DIR__.'/config/fingerprints.php') => config_path('fingerprints.php'),
        ], 'config');
    }

    /**
     * Publish Fingerprints migration
     */
    protected function publishMigration()
    {
        $published_migration = glob(database_path('/migrations/*_create_fingerprints_table.php'));
        if (count($published_migration) === 0) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_fingerprints_table.php.stub' => database_path('/migrations/' . date('Y_m_d_His') . '_create_fingerprints_table.php'),
            ], 'migrations');
        }
    }

    protected function bootMacros()
    {
        Request::macro('fingerprint', function () {
            return App::make(FingerprinterInterface::class)->fingerprint($this);
        });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        // Bring in configuration values
        $this->mergeConfigFrom(
            __DIR__ . '/config/fingerprints.php',
            'fingerprints'
        );

        $this->app->bind(TrackingFilterInterface::class, function ($app) {
            return $app->make(config('fingerprints.tracking_filter'));
        });

        $this->app->bind(TrackingLoggerInterface::class, function ($app) {
            return $app->make(config('fingerprints.tracking_logger'));
        });

        $this->app->singleton(FingerprinterInterface::class, function ($app) {
            return $app->make(config('fingerprints.fingerprinter'));
        });

        $this->commands([
            Console\PruneCommand::class,
        ]);
    }
}
