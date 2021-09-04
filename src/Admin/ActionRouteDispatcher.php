<?php

namespace Laravelayers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Laravelayers\Form\Decorators\FormDecorator;

class ActionRouteDispatcher extends ControllerDispatcher
{
    /**
     * Get the middleware for the controller instance.
     *
     * @param  \Illuminate\Routing\Controller  $controller
     * @param  string  $method
     * @return array
     */
    public function getMiddleware($controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            [], $controller, $method
        );

        $method = $this->getMethodFromRequest($parameters, $method);

        return parent::getMiddleware($controller, $method);
    }

    /**
     * Dispatch a request to a given controller and method.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $controller
     * @param  string  $method
     * @return mixed
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(), $controller, $method
        );

        $method = $this->getMethodFromRequest($parameters, $method);

        return parent::dispatch($route, $controller, $method);
    }

    /**
     * Get the controller method from the request.
     *
     * @param array $parameters
     * @param string $method
     * @return string
     */
    protected function getMethodFromRequest($parameters, $method)
    {
        if (in_array($method, ['store', 'index'])) {
            foreach($parameters as $parameter) {
                if ($parameter instanceof Request) {
                    $request = $parameter;

                    break;
                }
            }

            if (isset($request)) {
                if ($action = $request->get('action', $request->old('preaction'))) {
                    if ($request->query('action') != $action) {
                        $_method = Str::camel($action);

                        if (method_exists($request->route()->getController(), $_method)) {
                            $method = $_method;
                        }
                    }
                }
            }
        }

        return $method;
    }
}
