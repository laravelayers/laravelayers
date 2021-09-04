<?php

namespace Laravelayers\Pagination;

use Illuminate\Support\ServiceProvider;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;

class PaginationServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'pagination');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/lang/' => resource_path('lang/vendor/pagination'),
            ], 'laravelayers-pagination');
        }
    }

    /**
     * Register the views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'pagination');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views/' => resource_path('views/vendor/pagination'),
            ], 'laravelayers-pagination');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaginatorDecorator::class, function ($app, $params) {
            return PaginatorDecorator::make(...$params);
        });
    }
}
