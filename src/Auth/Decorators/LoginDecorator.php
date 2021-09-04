<?php

namespace Laravelayers\Auth\Decorators;

use Illuminate\Support\Facades\Lang;
use Laravelayers\Form\Decorators\Form;

class LoginDecorator extends UserDecorator
{
    use Form {
        Form::prepareElements as prepareFormElements;
    }

    /**
     * The login username.
     *
     * @var string
     */
    protected $username = 'email';

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
                'rules' => 'required|string',
                'hidden' => $this->getUsername() != 'name' ?: false
            ],
            'email' => [
                'type' => 'email.js',
                'value' => $this->email,
                'label' => Lang::get('auth::auth.email_text'),
                'error' => Lang::get('auth::auth.email_error'),
                'required' => true,
                'autofocus' => '',
                'rules' => 'required|email',
                'hidden' => $this->getUsername() != 'email' ?: false
            ],
            'password' => [
                'type' => 'password.group',
                'label' => Lang::get('auth::auth.password_text'),
                'error' => Lang::get('auth::auth.password_error'),
                'required' => true,
                'rules' => 'required|string'
            ],
            'remember' => [
                'type' => 'checkbox',
                'value' => [[
                    'text' => Lang::get('auth::auth.remember_text')
                ]],
                'multiple' => false
            ],
            'button' => [
                'type' => 'button',
                'value' => [[
                    'type' => 'submit',
                    'text' => Lang::get('auth::auth.login_button_text'),
                    'class' => 'expanded'
                ]]
            ]
        ];
    }

    /**
     * Get the login username column.
     *
     * @return string
     */
    public function getUsernameColumn()
    {
        return static::getUsername() == 'email'
            ? $this->getEmailColumn()
            : $this->getNameColumn();
    }

    /**
     * Get the login username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the login username.
     *
     * @param string $value
     */
    public function setUsername($value)
    {
        $this->username = $value == 'email' ? 'email' : 'name';
    }

    /**
     * Set the value to "remember".
     *
     * @param string $value
     * @param array $element
     * @return array
     */
    public function setRemember($value, $element = [])
    {
        return array_merge($element, [
            'value' => [
                key($element['value']) => array_merge(current($element['value']), [
                    'selected' => $value ?: false
                ])
            ]
        ]);
    }
}
