<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Contracts\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Contracts\Foundation\Services\Service;

/**
 * @see \Laravelayers\Auth\Services\UserService
 */
interface UserService extends Service
{
    /**
     * Create a new UserService instance.
     *
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository);

    /**
     * Find the user by the specified ID and load the actions and role actions for him.
     *
     * @param int $id
     * @return \Laravelayers\Foundation\Decorators\Decorator
     */
    public function findWithActionsAndRoles($id);
}
