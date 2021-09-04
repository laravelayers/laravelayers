<?php

namespace Laravelayers\Date;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class DateServiceProvider extends ServiceProvider
{
    use BladeDirectives {
        BladeDirectives::boot as bootFromBladeDirectives;
    }
    use CarbonMacros {
        CarbonMacros::boot as bootFromCarbonMacros;
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    public function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/date.php', 'date'
        );

        if (is_null(config('date.locale'))) {
            config(['date.locale' => config('app.locale')]);
        }

        if (!config('date.datetime.format')) {
            config(['date.datetime.format' => config('date.format') . ' ' . config('date.time.format')]);
        }

        // Set the default format used when type juggling a Carbon instance to a string
        Carbon::setToStringFormat(config('date.datetime.format'));
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerLocale();

        $this->registerConfigFiles();

        $this->bootFromCarbonMacros();

        $this->bootFromBladeDirectives();
    }

    /**
     * Register the locale.
     *
     * @return void
     */
    public function registerLocale()
    {

        if ($locale = config('date.locale')) {
            $locale = (array) $locale;

            $locale[1] = $locale[1]
                ?? strtolower($locale[0]) . '_' . strtoupper($locale[0]);

            // Carbon::now()->addYear()->diffForHumans(); // in one year
            Carbon::setLocale($locale[0]);

            // Carbon::now()->formatLocalized('%A %d %B %Y'); // monday 20 march 2017
            setlocale(LC_TIME, $locale[1], $locale[0]);
        }
    }

    /**
     * Register configuration files.
     *
     * @return void
     */
    public function registerConfigFiles()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/date.php' => config_path('date.php')
            ], 'laravelayers-date');
        }
    }
}
