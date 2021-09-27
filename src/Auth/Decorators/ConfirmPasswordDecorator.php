<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class ConfirmPasswordDecorator extends UserDecorator
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
                    'link' => route('password.confirm')
                ]
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('Password'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Password')]),
                'required' => true,
                'rules' => 'required|password'
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('Confirm Password'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
