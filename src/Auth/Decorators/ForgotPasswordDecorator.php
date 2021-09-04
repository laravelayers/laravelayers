<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class ForgotPasswordDecorator extends UserDecorator
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
                    'link' => route('password.email'),
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'label' => Lang::get('auth::auth.email_text'),
                'error' => Lang::get('auth::auth.email_error'),
                'autofocus' => '',
                'required' => true,
                'rules' => 'required|email|max:255'
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('auth::passwords.forgot_button_text'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
