<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Auth\Decorators\UserDecorator;
use Laravelayers\Contracts\Foundation\Repositories\Repository;

/**
 * @see \Laravelayers\Auth\Repositories\UserRepository
 */
interface UserRepository extends Repository
{
    /**
     * Loading actions and role actions for users.
     *
     * @return $this
     */
    public function withActionsAndRoles();

    /**
     * Mark the given user's email as verified.
     *
     * @param UserDecorator|null $user
     * @return bool
     */
    public function markEmailAsVerified(UserDecorator $user = null);
}
