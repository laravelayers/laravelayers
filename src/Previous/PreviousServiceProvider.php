<?php namespace Laravelayers\ Previous;

use Illuminate\Support\ServiceProvider;

class PreviousServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();

        $this->registerMiddleware();
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'previous');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/lang/' => resource_path('lang/vendor/previous'),
            ], 'laravelayers-previous');
        }
    }

    /**
     * Register the middleware.
     *
     * @return void
     */
    public function registerMiddleware()
    {
        if (empty($this->app['router']->getMiddleware()['previous.url'])) {
            $this->app['router']->aliasMiddleware('previous.url', \Laravelayers\Previous\Middleware\PreviousUrlFromQuery::class);
        }
    }
}
