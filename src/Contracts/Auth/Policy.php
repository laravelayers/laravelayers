<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Auth\Decorators\UserDecorator;

/**
 * @see \Laravelayers\Auth\Policies\Policy
 */
interface Policy
{
    /**
     * Determine if the given ability should be granted for the specified user.
     *
     * @param UserDecorator $user
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function check(UserDecorator $user, $ability, $arguments = []);
}
