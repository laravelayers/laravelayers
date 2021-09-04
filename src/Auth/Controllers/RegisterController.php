<?php

namespace Laravelayers\Auth\Controllers;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Laravelayers\Auth\Decorators\RegisterDecorator;
use Laravelayers\Auth\Notifications\Registered as RegisteredNotification;
use Laravelayers\Foundation\Controllers\Controller;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        $this->service = $this->guard()
            ->getProvider()
            ->setDecorators(RegisterDecorator::class);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $elements = $this->service
            ->fill()
            ->getElements();

        return view('auth::register', compact('elements'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return $this->service
            ->fill()
            ->getElements()
            ->setSuccess('')
            ->validate();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Laravelayers\Auth\Decorators\UserDecorator
     */
    protected function create(array $data)
    {
        $user = $this->service->getResult();

        $user->getElements()->setSuccess(Lang::get(Lang::has($trans = 'auth.registered') ? $trans : 'auth::' . $trans));

        return $this->service->save($user);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if (!Route::has('verification.notice')) {
            $user->notify(app(RegisteredNotification::class, ['user' => $user]));
        }

        return redirect($this->redirectPath());
    }
}
