<?php

namespace Laravelayers\Auth\Repositories;

use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Contracts\Auth\User as UserContract;
use Laravelayers\Contracts\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Foundation\Repositories\Repository;

/**
 * @method UserContract result(UserDecorator $item = null)
 */
class UserRepository extends Repository implements UserRepositoryContract
{
    /**
     * Create a new UserRepository instance.
     *
     * @param UserContract $user
     */
    public function __construct(UserContract $user)
    {
        $this->model = $user;
    }

    /**
     * Loading actions and role actions for users.
     *
     * @return $this
     */
    public function withActionsAndRoles()
    {
        return $this->query(
            $this->model->with(['userActions' => function($query) {
                $query->with('userRole.userRoleActions');
            }])
        );
    }

    /**
     * Mark the given user's email as verified.
     *
     * @param UserDecorator|null $user
     * @return bool
     */
    public function markEmailAsVerified(UserDecorator $user = null)
    {
        return $this->result($user)->markEmailAsVerified();
    }
}
