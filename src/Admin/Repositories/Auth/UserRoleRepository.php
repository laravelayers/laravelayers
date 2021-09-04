<?php

namespace Laravelayers\Admin\Repositories\Auth;

use Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleRepository as UserRoleRepositoryContract;
use Laravelayers\Contracts\Auth\UserRole as UserRoleContract;
use Laravelayers\Foundation\Repositories\Repository;

class UserRoleRepository extends Repository implements UserRoleRepositoryContract
{
    /**
     * Create a new UserRoleRepository instance.
     *
     * @param UserRoleContract $userRole
     */
    public function __construct(UserRoleContract $userRole)
    {
        $this->model = $userRole;
    }
}
