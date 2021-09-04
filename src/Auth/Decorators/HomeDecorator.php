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
                'label' => Lang::get('auth::auth.name_text'),
                'help' => Lang::get('auth::auth.name_help'),
                'error' => Lang::get('auth::auth.name_error'),
                'autofocus' => '',
                'required' => true,
                'rules' => ['required', 'string', 'max:255',
                    "unique:{$this->table},{$this->nameColumn},{$this->getKey()},{$this->primaryKey}"
                ]
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('auth::auth.email_text'),
                'error' => Lang::get('auth::auth.email_error'),
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
                'group' => 'avatar',
                'data-image-mode' => 3,
                'rules' => 'mimes:jpg,png',
                'hidden' => static::$isHiddenAvatar
            ],
            'old_password' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.password_text'),
                'error' => Lang::get('auth::auth.password_error'),
                'required' => true,
                'rules' => 'required|string|min:6'
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.new_password_text'),
                'help' => Lang::get('auth::auth.password_help'),
                'autocomplete' => 'new-password',
                'rules' => 'confirmed|string|min:6'
            ],
            'password_confirmation' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.new_password_confirmation_text'),
                'error' => Lang::get('auth::auth.new_password_confirmation_error')
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('auth::auth.profile_button_text'),
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
