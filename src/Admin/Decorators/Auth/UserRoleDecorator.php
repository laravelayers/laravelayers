<?php

namespace Laravelayers\Admin\Decorators\Auth;

use Illuminate\Support\Str;
use Laravelayers\Admin\Decorators\DataDecorator as TraitDataDecorator;
use Laravelayers\Auth\Decorators\UserRoleDecorator as BaseRoleDecorator ;
use Laravelayers\Contracts\Admin\Decorators\DataDecorator as DataDecoratorContract;

class UserRoleDecorator extends BaseRoleDecorator implements DataDecoratorContract
{
    use TraitDataDecorator;

    /**
     * Initialize translation path.
     *
     * @return string
     */
    protected static function initTranslationPath()
    {
        return 'admin::auth/roles';
    }

    /**
     * Initialize form elements.
     *
     * @return array
     */
    protected function initElements()
    {
        return [
            'role' => [
                'type' => 'text',
                'required' => '',
                'rules' => ['required', 'string', 'max:255',
                    "unique:{$this->table},{$this->roleColumn},{$this->getKey()},{$this->getKeyName()}"
                ]
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
        return [
            'users' => [
                'type' => $this->user_actions_count ? 'show' : 'create',
                'link' => 'admin.auth.roles.users.index',
                'text' => $this->user_actions_count,
                'icon' => 'icon-users',
                'group' => 0
            ],
            'actions' => [
                'type' => $this->user_role_actions_count ? 'show' : 'create',
                'link' => 'admin.auth.roles.actions.index',
                'text' => $this->user_role_actions_count,
                'icon' => 'icon-user-shield',
                'group' => 0
            ]
        ];
    }

    /**
     * Initialize form elements for editing multiple collection elements.
     *
     * @return array
     */
    protected function initMultipleElements()
    {
        return ['role'];
    }

    /**
     * Initialize actions.
     *
     * @return array
     */
    protected function initActions()
    {
        return array_merge($this->getDefaultActions('edit'), $this->initActionsElement());
    }

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getRole()
    {
        return parent::getRole() ?: 'role.';
    }

    /**
     * Set the role value.
     *
     * @param string $value
     * @return string
     */
    public function setRole($value)
    {
        if (!Str::startsWith($value, 'role.')) {
            $value = 'role.' . $value;
        }

        $this->put($this->getRoleColumn(), $value);

        return $value;
    }
}
