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
                    'link' => route('password.request')
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('Email'),
                'error' => Lang::get('validation.email', ['attribute' => Lang::get('Email')]),
                'autofocus' => '',
                'required' => true,
                'rules' => 'required|email'
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('New Password'),
                'error' => Lang::get('validation.min.string', ['attribute' => Lang::get('Password'), 'min' => 8]),
                'data-validator' => 'validator',
                'data-validator-name' => 'isLength',
                'data-validator-options' => htmlspecialchars(json_encode(['min' => 8])),
                'required' => true,
                'rules' => 'required|string|min:8'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('Confirm Password'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Confirm Password')])
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
                    'text' => Lang::get('Reset Password'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
