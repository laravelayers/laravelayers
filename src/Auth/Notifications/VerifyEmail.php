<?php

namespace Laravelayers\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class VerifyEmail extends VerifyEmailNotification
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
            ->greeting(Lang::getFromJson('Hello!'))
            ->salutation(new HtmlString(
                    Lang::getFromJson('Regards') . ',<br>' . config('app.name'))
            );
    }
}
