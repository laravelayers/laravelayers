<?php

namespace Laravelayers\Auth\Decorators;

use Laravelayers\Foundation\Decorators\DataDecorator;

class UserRoleActionDecorator extends DataDecorator
{
    /**
     * The column name of the "role".
     *
     * @var string
     */
    protected $roleColumn;

    /**
     * The column name of the "action".
     *
     * @var string
     */
    protected $actionColumn;

    /**
     * The column name of the "allowed".
     *
     * @var string
     */
    protected $allowedColumn;

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
     * Get the action name.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->get($this->getActionColumn());
    }

    /**
     * Get the column name of the "action".
     *
     * @return string
     */
    public function getActionColumn()
    {
        return $this->actionColumn;
    }

    /**
     * Get the action mode.
     *
     * @return string
     */
    public function getAllowed()
    {
        return $this->get($this->getAllowedColumn());
    }

    /**
     * Get the column name for the "allowed".
     *
     * @return string
     */
    public function getAllowedColumn()
    {
        return $this->allowedColumn;
    }
}
