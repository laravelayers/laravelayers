<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Auth\MustVerifyEmail as BaseMustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Laravelayers\Auth\Notifications\VerifyEmail as VerifyEmailNotification;

trait MustVerifyEmail
{
    use BaseMustVerifyEmail;

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return Auth::guard()->getProvider()->markEmailAsVerified();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(app(VerifyEmailNotification::class));
    }
}
