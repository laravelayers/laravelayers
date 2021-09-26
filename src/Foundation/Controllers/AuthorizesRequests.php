<?php

namespace Laravelayers\Foundation\Controllers;

use Illuminate\Support\Facades\Request;

trait AuthorizesRequests
{
    /**
     * Authorize a resource action based on the incoming request.
     *
     * @param  string  $model
     * @param  string|null  $parameter
     * @param  array  $options
     * @param  \Illuminate\Http\Request|null  $request
     * @return void
     */
    public function authorizeResource($model = null, $parameter = null, array $options = [], $request = null)
    {
        if (is_null($model)) {
            if ($parameter) {
                if (Request::route()) {
                    $parameter = trim(preg_replace(
                            '/(\.)?' . Request::route()->getActionMethod() . '$/i',
                            '',
                            $parameter
                        ), '.');
                }

                $parameter .= '.';
            }

            $middleware = [];

            foreach ($this->resourceAbilityMap() as $method => $ability) {
                if (!$parameter && strcasecmp($ability, 'viewAny') == 0) {
                    $ability = '*';
                }

                $middleware["can:{$parameter}{$ability}"][] = $method;
            }

            foreach ($middleware as $middlewareName => $methods) {
                $this->middleware($middlewareName, $options)->only($methods);
            }
        } else {
            parent::authorizeResource($model, $parameter, $options, $request);
        }
    }
}
