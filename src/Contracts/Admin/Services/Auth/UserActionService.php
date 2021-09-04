<?php

namespace Laravelayers\Contracts\Admin\Services\Auth;

use Laravelayers\Contracts\Foundation\Services\Service;
use Laravelayers\Foundation\Decorators\CollectionDecorator;

/**
 * @see \Laravelayers\Admin\Services\Auth\UserActionService
 */
interface UserActionService extends Service
{
    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator $items
     * @return CollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items);

    /**
     * Remove multiple resources from the repository.
     *
     * @param CollectionDecorator $items
     * @return int
     */
    public function destroyMultiple(CollectionDecorator $items);
}

