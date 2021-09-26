<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class VerificationDecorator extends UserDecorator
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
                    'link' => route('verification.resend')
                ]
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('click here to request another'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }
}
