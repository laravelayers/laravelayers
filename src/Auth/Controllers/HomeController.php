<?php

namespace Laravelayers\Auth\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravelayers\Auth\Decorators\HomeDecorator;
use Laravelayers\Auth\Notifications\Registered as RegisteredNotification;
use Laravelayers\Foundation\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Create a new HomeController instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->service = Auth::guard()->getProvider()->setDecorators(
            HomeDecorator::class
        );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->service->getResult();

        if (session('verified')) {
            $user->notify(app(RegisteredNotification::class, ['user' => $user]));
        }

        return view('auth::home', [
            'elements' => $user->getElements()
        ]);
    }

    /**
     * Update the user in storage.
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $user = $this->service->getResult();

        $user->getElements()->validate();

        $this->service->save($user);

        $this->service->storeImages($user);

        return back();
    }
}
