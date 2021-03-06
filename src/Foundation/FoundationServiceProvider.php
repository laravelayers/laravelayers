<?php
namespace Laravelayers\Foundation;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Laravelayers\Foundation\Console\Presets\Foundation;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Dto\Dto;

class FoundationServiceProvider extends ServiceProvider
{
    use BladeDirectives, CollectionMacros, RequestMacros {
        BladeDirectives::boot as bootFromBladeDirectives;
        CollectionMacros::boot as bootFromEloquentCollectionMacros;
        RequestMacros::boot as bootFromRequestMacros;
    }

    /**
     * Bootstrap the application services.
     *
     * @throws \Throwable
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();

        $this->registerViews();

        $this->registerAssets();

        $this->registerCommands();

        $this->bootFromBladeDirectives();

        $this->bootFromEloquentCollectionMacros();

        $this->bootFromRequestMacros();
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'foundation');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/foundation'),
        ], 'laravelayers-foundation');
    }

    /**
     * Register the views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'foundation');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/foundation'),
        ], 'laravelayers-foundation');
    }

    /**
     * Register the assets.
     *
     * @return void
     */
    public function registerAssets()
    {
        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js') . "/vendor/foundation",
            __DIR__.'/resources/sass' => resource_path('sass') . "/vendor/foundation",
        ], 'laravelayers-foundation');
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'command.install.laravelyares',
                'command.decorator.make',
                'command.repository.make',
                'command.service.make',
                'command.js.make',
                'command.stub.publish'
            ]);

            $this->app->bind('command.install.laravelyares', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\InstallLaravelayersCommand::class);
            });

            $this->app->extend('command.controller.make', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\ControllerMakeCommand::class);
            });

            $this->app->bind('command.decorator.make', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\DecoratorMakeCommand::class);
            });

            $this->app->extend('command.model.make', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\ModelMakeCommand::class);
            });

            $this->app->bind('command.repository.make', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\RepositoryMakeCommand::class);
            });

            $this->app->bind('command.service.make', function () {
                return $this->app->make( \Laravelayers\Foundation\Console\Commands\ServiceMakeCommand::class);
            });

            $this->app->bind('command.js.make', function () {
                return $this->app->make(\Laravelayers\Foundation\Console\Commands\JsMakeCommand::class);
            });

            $this->app->bind('command.stub.publish', function () {
                return $this->app->make( \Laravelayers\Foundation\Console\Commands\StubPublishCommand::class);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Dto::class, function ($app, $params) {
            return Dto::make(...$params);
        });

        $this->app->bind(DataDecorator::class, function ($app, $params) {
            return DataDecorator::make(...$params);
        });

        $this->app->bind(CollectionDecorator::class, function ($app, $params) {
            return CollectionDecorator::make(...$params);
        });
    }
}
