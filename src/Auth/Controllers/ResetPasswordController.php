<?php

namespace Laravelayers\Auth\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Laravelayers\Auth\Decorators\ResetPasswordDecorator;
use Laravelayers\Foundation\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        ResetsPasswords::credentials as passwordResetCredentials;
    }

    /**
     * Where to redirect users after resetting their password.
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

        $this->service = Auth::guard()->getProvider()->setDecorators(
            ResetPasswordDecorator::class
        );
    }

    /**
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $elements = $this->service
            ->getResult()
            ->getElements();

        return view('auth::passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
            'elements' => $elements
        ]);
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     */
    public function validate(Request $request, array $rules,
                             array $messages = [], array $customAttributes = [])
    {
        $this->service
            ->getResult()
            ->getElements()
            ->setSuccess('')
            ->validate();

        return $this->extractInputFromRules($request, $rules);
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge([
            $this->service->getResult()->getEmailColumn() => $this->passwordResetCredentials($request)['email']],
            Arr::except($this->passwordResetCredentials($request), 'email')
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user = $this->service->find($user->getKey());

        $user->setPassword($password);

        $user->setRememberToken(Str::random(60));

        $this->service->save($user);

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('status', Lang::get(Lang::has($response) ? $response : 'auth::' . $response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => Lang::get(Lang::has($response) ? $response : 'auth::' . $response)]);
    }
}
