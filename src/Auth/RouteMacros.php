<?php namespace Laravelayers\Auth;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

trait RouteMacros
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerToGetNameByAction();
        $this->registerAuthLayer();
    }

    /**
     * Register the GetNameByAction method to get the name of the route by action.
     *
     * @return void
     */
    public function registerToGetNameByAction()
    {
        if (!Route::hasMacro('getNameByAction')) {
            Route::macro('getNameByAction', function ($action) {
                return preg_replace(
                    '/(\.)?'. Request::route()->getActionMethod() .'$/i',
                    '$1' . $action,
                    Request::route()->getName()
                );
            });
        }
    }

    /**
     * Register the AuthLayer method for create auth routes.
     *
     * @return void
     */
    public function registerAuthLayer()
    {
        if (!RouteFacade::hasMacro('authLayer')) {
            RouteFacade::macro('authLayer', function ($options = []) {
                $login = '\Laravelayers\Auth\Controllers\LoginController';
                $register = '\Laravelayers\Auth\Controllers\RegisterController';
                $forgot = '\Laravelayers\Auth\Controllers\ForgotPasswordController';
                $reset = '\Laravelayers\Auth\Controllers\ResetPasswordController';
                $verify = '\Laravelayers\Auth\Controllers\VerificationController';

                if (is_string($options)) {
                    $options = ['path' => $options];
                }

                if (!is_null($options['path'] ?? null)) {
                    $options['path'] = rtrim($options['path'], '\\');
                    $options['path'] = $options['path'] ? $options['path'] . '\\' : '';

                    $controllers = [
                        'login' => $options['path'] . 'LoginController',
                        'register' => $options['path'] . 'RegisterController',
                        'forgot' => $options['path'] . 'ForgotPasswordController',
                        'reset' => $options['path'] . 'ResetPasswordController'
                    ];

                    foreach($controllers as $var => $controller) {
                        if (class_exists($controller) || class_exists('App\Http\Controllers\\' . $controller)) {
                            $$var = $controller;
                        }
                    }
                }

                // Authentication Routes...
                RouteFacade::get('login', $login . '@showLoginForm')->name('login');
                RouteFacade::post('login', $login . '@login');
                RouteFacade::post('logout', $login . '@logout')->name('logout');

                // Registration Routes...
                if ($options['register'] ?? true) {
                    RouteFacade::get('register', $register . '@showRegistrationForm')->name('register');
                    RouteFacade::post('register', $register . '@register');
                }

                // Password Reset Routes...
                if ($options['reset'] ?? true) {
                    RouteFacade::get('password/reset', $forgot . '@showLinkRequestForm')->name('password.request');
                    RouteFacade::post('password/email', $forgot . '@sendResetLinkEmail')->name('password.email');
                    RouteFacade::get('password/reset/{token}', $reset . '@showResetForm')->name('password.reset');
                    RouteFacade::post('password/reset', $reset . '@reset');
                }

                // Email Verification Routes...
                if ($options['verify'] ?? true) {
                    RouteFacade::get('email/verify', $verify . '@show')->name('verification.notice');
                    RouteFacade::get('email/verify/{id}/{hash}', $verify . '@verify')->name('verification.verify');
                    RouteFacade::get('email/resend', $verify . '@resend')->name('verification.resend');
                }
            });
        }
    }
}
