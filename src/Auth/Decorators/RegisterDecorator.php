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
                'label' => Lang::get('auth::auth.name_text'),
                'help' => Lang::get('auth::auth.name_help'),
                'error' => Lang::get('auth::auth.name_error'),
                'autofocus' => '',
                'required' => true,
                'rules' => ['required', 'string', 'max:255',
                    "unique:{$this->table},{$this->nameColumn}"
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('auth::auth.email_text'),
                'error' => Lang::get('auth::auth.email_error'),
                'autocomplete' => 'off',
                'required' => true,
                'rules' => ['required', 'email', 'max:255',
                    "unique:{$this->table},{$this->nameColumn}"
                ]
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.password_text'),
                'help' => Lang::get('auth::auth.password_help'),
                'autocomplete' => 'new-password',
                'required' => true,
                'rules' => 'required|confirmed|string|min:6'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.password_confirmation_text'),
                'error' => Lang::get('auth::auth.password_confirmation_error'),
                'required' => true
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'class' => 'expanded',
                    'text' => Lang::get('auth::auth.register_button_text')
                ]]
            ]
        ];
    }
}
