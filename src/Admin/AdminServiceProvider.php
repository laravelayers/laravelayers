<?php

namespace Laravelayers\Admin;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Admin\Controllers\Controller;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Laravelayers\Admin\Middleware\CheckSubdomainAdmin;

class AdminServiceProvider extends ServiceProvider
{
    use RouteMacros {
        RouteMacros::boot as bootFromRouteMacros;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerActionRouteDispatcher();

        $this->registerConfig();

        $this->registerUserService();

        $this->registerUserRepository();

        $this->registerUserModel();
    }

    /**
     * Register the action route dispatcher.
     *
     * @return void
     */
    public function registerActionRouteDispatcher()
    {
        $this->app->singleton(ControllerDispatcherContract::class, ActionRouteDispatcher::class);
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    public function registerConfig()
    {
        config(['admin.prefix' => config('admin.prefix') ?: 'admin']);
    }

    /**
     * Register the user service.
     *
     * @return void
     */
    public function registerUserService()
    {
        $this->app->bind(
            \Laravelayers\Contracts\Admin\Services\Auth\RoleUserService::class,
            \Laravelayers\Admin\Services\Auth\RoleUserService::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Services\Auth\UserActionService::class,
            \Laravelayers\Admin\Services\Auth\UserActionService::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Services\Auth\UserRoleActionService::class,
            \Laravelayers\Admin\Services\Auth\UserRoleActionService::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Services\Auth\UserRoleService::class,
            \Laravelayers\Admin\Services\Auth\UserRoleService::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Services\Auth\UserService::class,
            \Laravelayers\Admin\Services\Auth\UserService::class
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
            \Laravelayers\Contracts\Admin\Repositories\Auth\UserActionRepository::class,
            \Laravelayers\Admin\Repositories\Auth\UserActionRepository::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Repositories\Auth\UserRepository::class,
            \Laravelayers\Admin\Repositories\Auth\UserRepository::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleRepository::class,
            \Laravelayers\Admin\Repositories\Auth\UserRoleRepository::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleActionRepository::class,
            \Laravelayers\Admin\Repositories\Auth\UserRoleActionRepository::class
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

        $this->app->bind(
            \Laravelayers\Contracts\Auth\UserAction::class,
            \Laravelayers\Auth\Models\UserAction::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Auth\UserRole::class,
            \Laravelayers\Auth\Models\UserRole::class
        );

        $this->app->bind(
            \Laravelayers\Contracts\Auth\UserRoleAction::class,
            \Laravelayers\Auth\Models\UserRoleAction::class
        );
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();

        $this->registerViews();

        $this->registerMenuView();

        $this->registerAssets();

        $this->registerConsoleCommands();

        $this->bootFromRouteMacros();
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'admin');
        $this->loadJSONTranslationsFrom(__DIR__.'/resources/lang');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/admin'),
        ], 'laravelayers-admin');
    }

    /**
     * Register the views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'admin');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views/' => resource_path('views/vendor/admin'),
            ], 'laravelayers-admin');
        }
    }

    /**
     * Register the view for the admin menu.
     *
     * @return void
     */
    public function registerMenuView()
    {
        view()->composer(['admin::layouts.app', 'admin::layouts.menuBar', 'admin::layouts.header'], function($view) {
            if (Auth::check() && Auth::user()->can('admin.*')) {
                $controller = Request::route()->getController();

                if (!method_exists($controller, 'getMenu')) {
                    $controller = resolve(Controller::class);
                }

                $menu = $controller->getMenu();

                $view->with(['title' => $menu->title, 'menu' => $menu->menu, 'path' => $menu->path]);
            }
        });
    }

    /**
     * Register the assets.
     *
     * @return void
     */
    public function registerAssets()
    {
        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js') . "/vendor/admin",
            __DIR__.'/resources/sass' => resource_path('sass') . "/vendor/admin",
        ], 'laravelayers-admin');
    }

    /**
     * Register the console commands.
     *
     * @return void
     */
    public function registerConsoleCommands()
    {
        $this->commands([
            'command.admin.menu.cache'
        ]);

        $this->app->bind('command.admin.menu.cache', function () {
            return $this->app->make(\Laravelayers\Admin\Console\Commands\AdminMenuCacheCommand::class);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                'command.admin.controller.make',
                'command.admin.decorator.make',
                'command.admin.repository.make',
                'command.admin.routes.make',
                'command.admin.service.make',
                'command.admin.stub.publish'
            ]);

            $this->app->bind('command.admin.controller.make', function () {
                return $this->app->make(\Laravelayers\Admin\Console\Commands\AdminControllerMakeCommand::class);
            });

            $this->app->bind('command.admin.decorator.make', function () {
                return $this->app->make(\Laravelayers\Admin\Console\Commands\AdminDecoratorMakeCommand::class);
            });

            $this->app->bind('command.admin.repository.make', function () {
                return $this->app->make(\Laravelayers\Admin\Console\Commands\AdminRepositoryMakeCommand::class);
            });

            $this->app->bind('command.admin.routes.make', function () {
                return $this->app->make(\Laravelayers\Admin\Console\Commands\AdminRoutesMakeCommand::class);
            });

            $this->app->bind('command.admin.service.make', function () {
                return $this->app->make( \Laravelayers\Admin\Console\Commands\AdminServiceMakeCommand::class);
            });

            $this->app->bind('command.admin.stub.publish', function () {
                return $this->app->make( \Laravelayers\Admin\Console\Commands\AdminStubPublishCommand::class);
            });
        }
    }
}
