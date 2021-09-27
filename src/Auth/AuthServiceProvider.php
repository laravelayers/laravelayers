<?php

namespace Laravelayers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravelayers\Auth\Policies\Policy;
use Laravelayers\Contracts\Auth\Policy as PolicyContract;

class AuthServiceProvider extends ServiceProvider
{
    use RouteMacros {
        RouteMacros::boot as bootFromRouteMacros;
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserService();

        $this->registerUserRepository();

        $this->registerUserModel();

        $this->registerPolicyContract();

        $this->registerUserProvider();
    }

    /**
     * Register the user service.
     *
     * @return void
     */
    public function registerUserService()
    {
        $this->app->bind(
            \Laravelayers\Contracts\Auth\UserService::class,
            \Laravelayers\Auth\Services\UserService::class
        );
    }

    /**
     * Register the user repository.
     *
     * @return void
     */
    public function registerUserRepository()
    {
        $this->app->bind(
            \Laravelayers\Contracts\Auth\UserRepository::class,
            \Laravelayers\Auth\Repositories\UserRepository::class
        );
    }

    /**
     * Register the user model.
     *
     * @return void
     */
    public function registerUserModel()
    {
        $this->app->bind(
            \Laravelayers\Contracts\Auth\User::class,
            \Laravelayers\Auth\Models\User::class
        );
    }

    /**
     * Register the application's policy to run before all Gate checks.
     *
     * @return void
     */
    public function registerPolicyContract()
    {
        $this->app->bind(
            PolicyContract::class,
            Policy::class
        );
    }

    /**
     * Register the user provider.
     *
     * @return void
     */
    public function registerUserProvider()
    {
        Auth::provider('eloquent', function($app, array $config) {
            return $app->make(ServiceUserProvider::class);
        });
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicy();

        $this->registerMigrations();

        $this->registerTranslations();

        $this->registerViews();

        $this->registerConsoleCommands();

        $this->bootFromRouteMacros();
    }

    /**
     * Register the application's policy to run before all Gate checks.
     *
     * @return void
     */
    public function registerPolicy()
    {
        Gate::before(function ($user, $ability, $arguments) {
            return $this->app
                ->make(PolicyContract::class)
                ->check($user, $ability, $arguments);
        });
    }

    /**
     * Register migrations files.
     *
     * @return void
     */
    public function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/resources/lang', 'auth');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/resources/lang' => resource_path('lang/vendor/auth'),
            ], 'laravelayers-auth');
        }
    }

    /**
     * Register the views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'auth');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/auth'),
            ], 'laravelayers-auth');
        }
    }

    /**
     * Register the console commands.
     *
     * @return void
     */
    public function registerConsoleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Laravelayers\Auth\Console\AuthMakeCommand::class,
            ]);

            $this->app->extend('command.auth.make', function () {
                return $this->app->make(\Laravelayers\Auth\Console\AuthMakeCommand::class);
            });
        }
    }
}
