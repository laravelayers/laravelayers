<?php

namespace Laravelayers\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class Registered extends Notification
{
    /**
     * The user.
     *
     * @var string
     */
    public $user;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        return (new MailMessage)
            ->level('success')
            ->subject(Lang::getFromJson('Registered'))
            ->greeting(Lang::getFromJson('Welcome!'))
            ->line("**{$this->user->name}**, " . Lang::getFromJson('you are successfully registered.'))
            ->action(Lang::getFromJson('Go to the site'), route('home'))
            ->salutation(new HtmlString(
                    Lang::getFromJson('Regards') . ',<br>' . config('app.name'))
            );
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
