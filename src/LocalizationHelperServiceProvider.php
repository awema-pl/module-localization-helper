<?php

namespace AwemaPL\LocalizationHelper;

use Illuminate\Support\ServiceProvider;

class LocalizationHelperServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->publishes([
            __DIR__.'/../resources/translations' => resource_path('lang'),
        ], 'awema-pl-langs');

        app('localizationhelper')->autoCopyTranslations();
        if (file_exists($file = __DIR__ . '/helpers.php'))
        {
            require_once $file;
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/localizationhelper.php', 'localizationhelper');

        // Register the service the package provides.
        $this->app->singleton('localizationhelper', function ($app) {
            return new LocalizationHelper(
                config('localizationhelper.base_path')
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['localizationhelper'];
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
            __DIR__.'/../config/localizationhelper.php' => config_path('localizationhelper.php'),
        ], 'localizationhelper.config');
    }
}
