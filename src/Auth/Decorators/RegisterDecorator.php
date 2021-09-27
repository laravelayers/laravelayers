<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class RegisterDecorator extends UserDecorator
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
                    'link' => url()->current(),
                ]
            ],
            'name' => [
                'type' => 'text.group',
                'value' => $this->name,
                'icon' => 'icon-user',
                'label' => Lang::get('Name'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Name')]),
                'autofocus' => '',
                'required' => true,
                'rules' => ['required', 'alpha_dash', 'max:255',
                    "unique:{$this->table},{$this->nameColumn}"
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('Email'),
                'error' => Lang::get('validation.email', ['attribute' => Lang::get('Email')]),
                'autocomplete' => 'email',
                'required' => true,
                'rules' => ['required', 'email', 'max:255',
                    "unique:{$this->table},{$this->nameColumn}"
                ]
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('Password'),
                'error' => Lang::get('validation.min.string', ['attribute' => Lang::get('Password'), 'min' => 8]),
                'autocomplete' => 'new-password',
                'required' => true,
                'data-validator' => 'validator',
                'data-validator-name' => 'isLength',
                'data-validator-options' => htmlspecialchars(json_encode(['min' => 8])),
                'rules' => 'required|confirmed|string|min:8'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('Confirm Password'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Confirm Password')]),
                'required' => true
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'class' => 'expanded',
                    'text' => Lang::get('Register')
                ]]
            ]
        ];
    }
}
