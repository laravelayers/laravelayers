<?php

namespace Laravelayers\Auth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class ResetPassword extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return parent::toMail($notifiable)
            ->greeting(Lang::get('Hello!'))
            ->salutation(new HtmlString(
                    Lang::get('Regards') . ',<br>' . config('app.name'))
            );
    }
}
