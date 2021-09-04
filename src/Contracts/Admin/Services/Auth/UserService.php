<?php

namespace Laravelayers\Contracts\Admin\Services\Auth;

use Laravelayers\Contracts\Foundation\Services\Service;
use Laravelayers\Foundation\Decorators\CollectionDecorator;

/**
 * @see \Laravelayers\Admin\Services\Auth\UserService
 */
interface UserService extends Service
{
    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator $items
     * @return CollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items);
}
