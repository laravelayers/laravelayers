<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Laravelayers\Admin\Decorators\DataDecorator as TraitDataDecorator;
use Laravelayers\Auth\Decorators\UserDecorator as BaseUserDecorator;
use Laravelayers\Contracts\Admin\Decorators\DataDecorator as DataDecoratorContract;

class UserDecorator extends BaseUserDecorator implements DataDecoratorContract
{
    use TraitDataDecorator;

    /**
     * Initialize translation path.
     *
     * @return string
     */
    protected static function initTranslationPath()
    {
        return 'admin::auth/users';
    }

    /**
     * Initialize form elements.
     *
     * @return array
     */
    protected function initElements()
    {
        return [
            'name' => [
                'type' => 'text',
                'required' => '',
                'rules' => ['required', 'string', 'max:255',
                    "unique:{$this->table},{$this->nameColumn},{$this->getKey()},{$this->primaryKey}"
                ]
            ],
            'email' => [
                'type' => 'email',
                'required' => '',
                'rules' => ['required', 'email', 'max:255',
                    "unique:{$this->table},{$this->emailColumn},{$this->getKey()},{$this->primaryKey}"
                ]
            ],
            'password' => [
                'type' => 'text',
                'value' => $this->getNewPassword(),
                'readonly' => '',
                'required' => '',
                'rules' => 'required|string|min:6',
                'hidden' => !$this->isAction('create')
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
                'hidden' => !$this->isAction(['edit', 'show'])
            ],
        ];
    }

    /**
     * Initialize the form element for actions.
     *
     * @return array
     */
    protected function initActionsElement()
    {
        return Arr::except($this->initActions(), 'create');
    }

    /**
     * Initialize form elements for editing multiple collection elements.
     *
     * @return array
     */
    protected function initMultipleElements()
    {
        return ['name', 'email'];
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return array_merge($this->getDefaultActions(), [
            'actions' => [
                'type' => $this->user_actions_count ? 'show' : 'create',
                'link' => 'admin.auth.users.actions.index',
                'text' => $this->user_actions_count,
                'icon' => 'icon-user-shield',
                'group' => 1,
                'hidden' => Gate::denies('admin.auth.users.actions.*') ?: false
            ]
        ]);
    }

    /**
     * Render the email.
     *
     * @return mixed|string
     */
    public function renderEmail()
    {
        return $this->renderAsLink($this->email, 'mailto:' . $this->email);
    }

    /**
     * Get a new generated password.
     *
     * @return string
     */
    public function getNewPassword()
    {
        return substr(str_shuffle(
            preg_replace('/[^A-Z\d]/i', '',  Hash::make(mt_rand() . mt_rand()))
        ), 0, 16);
    }

    /**
     * Set cropped avatar data.
     *
     * @param string $value
     * @return $this
     */
    public function setCroppedAvatar($value)
    {
        return $this->setCroppedImage($value);
    }

    /**
     * Render the avatar.
     *
     * @return string
     */
    public function renderAvatar()
    {
        return $this->renderAsImage($this->getAvatar());
    }

    /**
     * Get the sort order.
     *
     * @return int|null
     */
    public function getSorting()
    {
        return 1;
    }
}
