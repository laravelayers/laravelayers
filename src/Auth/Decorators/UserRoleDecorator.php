<?php

namespace Laravelayers\Auth\Decorators;

use Laravelayers\Foundation\Decorators\DataDecorator;

class UserRoleDecorator extends DataDecorator
{
    /**
     * The column name of the "role".
     *
     * @var string
     */
    protected $roleColumn;

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->get($this->getRoleColumn());
    }

    /**
     * Get the column name of the "role".
     *
     * @return string
     */
    public function getRoleColumn()
    {
        return $this->roleColumn;
    }

    /**
     * Get the user role actions.
     *
     * @return UserActionDecorator|mixed
     */
    public function getUserRoleActions()
    {
        return UserRoleActionDecorator::make($this->getRelation('userRoleActions') ?: collect());
    }
}
