<?php namespace Laravelayers\Form;

use Illuminate\Support\ServiceProvider;
use Laravelayers\Form\Decorators\FormDecorator;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();

        $this->registerViews();
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'form');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/lang/' => resource_path('lang/vendor/form'),
            ], 'laravelayers-form');
        }
    }

    /**
     * Register the views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'form');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views/' => resource_path('views/vendor/form'),
            ], 'laravelayers-form');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Laravelayers\Contracts\Form\Form::class,
            Form::class
        );

        $this->app->bind(FormDecorator::class, function ($app, $params) {
            return FormDecorator::make(...$params);
        });
    }
}
