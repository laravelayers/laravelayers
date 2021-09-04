<?php

namespace Laravelayers\Admin\Repositories\Auth;

use Laravelayers\Contracts\Admin\Repositories\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Contracts\Auth\User as UserContract;
use Laravelayers\Foundation\Repositories\Repository;

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
}
