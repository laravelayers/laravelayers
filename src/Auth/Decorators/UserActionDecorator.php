<?php

namespace Laravelayers\Auth\Decorators;

use Laravelayers\Foundation\Decorators\DataDecorator;

class UserActionDecorator extends DataDecorator
{
    /**
     * The column name of the "user ID".
     *
     * @var string
     */
    protected $userColumn;

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
     * The column name of the "ip".
     *
     * @var string
     */
    protected $ipColumn;

    /**
     * Get the user ID.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->get($this->getUserColumn());
    }

    /**
     * Get the column name of the "user ID".
     *
     * @return string
     */
    public function getUserColumn()
    {
        return $this->userColumn;
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

    /**
     * Get the ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->get($this->getIpColumn());
    }

    /**
     * Get the column name for the "ip".
     *
     * @return string
     */
    public function getIpColumn()
    {
        return $this->ipColumn;
    }

    /**
     * Get the user role.
     *
     * @return UserRoleDecorator|mixed
     */
    public function getUserRole()
    {
        return UserRoleDecorator::make($this->getRelation('userRole'));
    }
}
