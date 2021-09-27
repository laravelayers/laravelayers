<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class HomeDecorator extends UserDecorator
{
    use Form {
        Form::prepareElements as prepareFormElements;
    }

    /**
     * Determines if the avatar element is hidden.
     *
     * @var array
     */
    public static $isHiddenAvatar = false;

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
                    "unique:{$this->table},{$this->nameColumn},{$this->getKey()},{$this->primaryKey}"
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('Email'),
                'error' => Lang::get('validation.email', ['attribute' => Lang::get('Email')]),
                'required' => true,
                'rules' => ['required', 'email', 'max:255',
                    "unique:{$this->table},{$this->emailColumn},{$this->getKey()},{$this->primaryKey}"
                ]
            ],
            'avatar' => [
                'type' => 'file.js',
                'accept' => [
                    'image/jpeg',
                    'image/png'
                ],
                'value' => $this->getAvatar(),
                'group' => Lang::get('Avatar'),
                'data-image-mode' => 3,
                'rules' => 'mimes:jpg,png',
                'hidden' => static::$isHiddenAvatar
            ],
            'old_password' => [
                'type' => 'password.group',
                'label' => Lang::get('Password'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Password')]),
                'required' => true,
                'rules' => 'required|string'
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('New Password'),
                'error' => Lang::get('validation.min.string', ['attribute' => Lang::get('Password'), 'min' => 8]),
                'autocomplete' => 'new-password',
                'data-validator' => 'validator',
                'data-validator-name' => 'isLength',
                'data-validator-options' => htmlspecialchars(json_encode(['min' => 8])),
                'rules' => 'confirmed|string|min:8'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('Confirm Password'),
                'error' => Lang::get('validation.required', ['attribute' => Lang::get('Confirm Password')])
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('Save'),
                    'class' => 'expanded',
                ]]
            ]
        ];
    }

    /**
     * Set the old password.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setOldPassword($value, $element = [])
    {
        return array_merge($element, [
            'value' => $value
        ]);
    }

    /**
     * Set the password confirmation.
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setPasswordConfirmation($value, $element = [])
    {
        return array_merge($element, [
            'value' => $value
        ]);
    }
}
