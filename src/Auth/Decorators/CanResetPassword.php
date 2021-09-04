<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Auth\Passwords\CanResetPassword as BaseCanResetPassword;
use Laravelayers\Auth\Notifications\ResetPassword as ResetPasswordNotification;

trait CanResetPassword
{
    use BaseCanResetPassword;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(app(ResetPasswordNotification::class, ['token' => $token]));
    }
}
