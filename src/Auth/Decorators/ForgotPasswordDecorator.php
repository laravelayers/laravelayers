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
                'label' => Lang::get('Email'),
                'error' => Lang::get('validation.email', ['attribute' => Lang::get('Email')]),
                'autofocus' => '',
                'required' => true,
                'rules' => 'required|email|max:255'
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('Send Password Reset Link'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
