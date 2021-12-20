<?php

namespace PeriodicNotice;

use PeriodicNotice\Console\Commands\SendPeriodicalNotificationsBatch;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'periodic-notice');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/periodic-notice.php' => config_path('periodic-notice.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/periodic-notice'),
            ], 'lang');


            $this->commands([
                SendPeriodicalNotificationsBatch::class,
            ]);

            $this->registerMigrations();
        }
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/periodic-notice.php', 'periodic-notice');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (PeriodicNoticeManager::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
