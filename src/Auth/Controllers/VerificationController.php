<?php

namespace Laravelayers\Auth\Controllers;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravelayers\Auth\Decorators\VerificationDecorator;
use Laravelayers\Foundation\Controllers\Controller;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');

        $this->service = Auth::guard()->getProvider()->setDecorators(
            VerificationDecorator::class
        );
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $elements = $this->service->getResult()->getElements();

        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth::verify', compact('elements'));
    }
}