<?php

namespace Laravelayers\Contracts\Admin\Services\Auth;

use Laravelayers\Foundation\Decorators\CollectionDecorator;

/**
 * @see \Laravelayers\Admin\Services\Auth\RoleUserService
 */
interface RoleUserService extends UserService
{
    /**
     * Remove multiple resources from the repository.
     *
     * @param CollectionDecorator $items
     * @return int
     */
    public function destroyMultiple(CollectionDecorator $items);
}
