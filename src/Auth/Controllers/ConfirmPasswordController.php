<?php

namespace Laravelayers\Auth\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravelayers\Auth\Decorators\ConfirmPasswordDecorator;
use Laravelayers\Foundation\Controllers\Controller;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->service = Auth::guard()->getProvider()->setDecorators(
            ConfirmPasswordDecorator::class
        );
    }

    /**
     * Display the password confirmation view.
     *
     * @return \Illuminate\Http\Response
     */
    public function showConfirmForm()
    {
        $elements = $this->service
            ->getResult()
            ->getElements();

        return view('auth::passwords.confirm', compact('elements'));
    }

    /**
     * Confirm the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $this->service
            ->getResult()
            ->getElements()
            ->validate();

        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended($this->redirectPath());
    }
}