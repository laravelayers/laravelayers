<?php namespace Laravelayers\Admin;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Traversable;

trait RouteMacros
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAdminRouteGroup();

        $this->registerAdminResource();

        $this->registerAdminResources();
    }

    /**
     * Register the Admin method for create a admin route group.
     *
     * @return void
     */
    public function registerAdminRouteGroup()
    {
        if (!Route::hasMacro('adminGroup')) {
            Route::macro('adminGroup', function ($routes) {
                $prefix = config('admin.prefix') ?: 'admin';

                $this->middleware('previous.url')
                    ->prefix($prefix)
                    ->name(sprintf('%s.', $prefix))
                    ->group(function () use ($routes) {
                        if ($routes instanceof Closure) {
                            $routes();
                        } elseif (is_array($routes) || $routes instanceof Traversable) {
                            $this->adminResources($routes);
                        } else {
                            $this->adminResource($routes);
                        }
                    });
            });
        }
    }

    /**
     * Register the AdminResources method for register an array of admin resource controllers.
     *
     * @return void
     */
    public function registerAdminResources()
    {
        if (!Route::hasMacro('adminResources')) {
            Route::macro('adminResources', function (array $resources) {
                foreach ($resources as $name => $controller) {
                    Route::adminResource($name, $controller);
                }
            });
        }
    }

    /**
     * Register the AdminResource method for route a resource to a admin controller.
     *
     * @return void
     */
    public function registerAdminResource()
    {
        if (!Route::hasMacro('adminResource')) {
            Route::macro('adminResource', function ($name, $controller, array $options = []) {
                $options['names'] = $options['names'] ?? str_replace('/', '.', $name);

                Route::adminGroup(function () use ($name, $controller, $options) {
                    Route::resource($name, $controller, $options);
                });
            });
        }
    }
}
