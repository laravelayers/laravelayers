<?php

namespace Laravelayers\Pagination\Decorators;

use Laravelayers\Foundation\Decorators\CollectionDecorator;

/**
 * The decorator for the object instance Illuminate\Pagination\Paginator.
 *
 * @package App\Decorators
 */
class PaginatorDecorator extends CollectionDecorator
{
    /**
     * Get data from the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return is_null($key)
            ? $this->getDataKey()->all()
            : $this->getDataKey()[$key];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getData()->toArray();
    }
}
