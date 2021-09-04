<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class ResetPasswordDecorator extends UserDecorator
{
    use Form {
        Form::prepareElements as prepareFormElements;
    }

    /**
     * Initialize form elements.
     *
     * @return array
     */
    protected function initElements()
    {
        return [
            'form' => [
                'type' => 'form.js',
                'value' => [
                    'method' => 'POST',
                    'link' => route('password.reset', ['token' => null])
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('auth::auth.email_text'),
                'error' => Lang::get('auth::auth.email_error'),
                'autofocus' => '',
                'required' => true,
                'rules' => 'required|email'
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.new_password_text'),
                'help' => Lang::get('auth::auth.password_help'),
                'required' => true,
                'rules' => 'required|confirmed|string|min:6'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.new_password_confirmation_text'),
                'error' => Lang::get('auth::auth.new_password_confirmation_error')
            ],
            'token' => [
                'type' => 'hidden',
                'value' => request()->token,
                'rules' => 'required'
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('auth::passwords.reset_button_text'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
