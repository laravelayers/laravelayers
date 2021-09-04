<?php

namespace Laravelayers\Foundation\Decorators;

use Illuminate\Support\Collection;

/**
 * The decorator for the object instance Illuminate\Support\Collection.
 */
class CollectionDecorator extends Decorator
{
    /**
     * Property name with decorator data.
     *
     * @var string
     */
    protected $dataKey = 'items';

    /**
     * Collection of items.
     *
     * @var \Illuminate\Support\Collection|static
     */
    protected $items;

    /**
     * Decorator startup method.
     *
     * @param $data
     * @return $this
     */
    public static function make($data = null)
    {
        $data = $data ?: [];

        if (is_string($data) || is_int($data)) {
            $data = (array) $data;
        }

        if (is_array($data)) {
            $data = collect($data);
        }

        $data = static::prepare($data);

        if ($data instanceof CollectionDecorator) {
            $data = static::decorateData($data);
        }

        return $data;
    }

    /**
     * Decorating data.
     *
     * @param $data
     * @return static
     */
    protected static function decorateData($data)
    {
        if (static::isDecorator($data) === true) {
            if (!strcasecmp(get_class($data), self::class)) {
                $data = $data->getData();
            }

            $data = new static($data);
        } elseif (is_array($data->getData())) {
            $data->setData(collect($data->getData()));
        }

        return $data;
    }

    /**
     * Handle dynamic method calls into the decorator.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($this->getDataKey() instanceof Collection) {
            $data = $this->getDataKey()->$method(...$parameters);

            return $data instanceof Collection
                ? static::make($data)
                : $data;
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Get primary keys for items.
     *
     * @return array
     */
    public function getKeys()
    {
        foreach($this as $item) {
            if (!$item->getKeyName()) {
                break;
            }

            $keys[] = $item->getKey();
        }

        return $keys ?? [];
    }

    /**
     * Get the item with the specified primary key.
     *
     * @param string|array|Collection $key
     * @param mixed $default
     * @return DataDecorator|Collection|mixed
     */
    public function getByKey($key, $default = null)
    {
        $items = $this->getOnly($key);

        if (is_iterable($key) && !$key instanceof DataDecorator) {
            return $items;
        }

        return $items->first() ?: $default;
    }

    /**
     * Get all items except for those with the specified primary keys.
     *
     * @param Collection|array|string $keys
     * @return Collection
     */
    public function getExcept($keys)
    {
        return $this->whereNotIn($this->isNotEmpty() ? $this->first()->getKeyName() : '', $keys);
    }

    /**
     * Get the items with the specified primary keys.
     *
     * @param array|Collection|string $keys
     * @return Collection
     */
    public function getOnly($keys)
    {
        return $this->whereIn($this->isNotEmpty() ? $this->first()->getKeyName() : '', $keys);

    }

    /**
     * Get the selected items and, if you specify the key name,
     * get a string of values for the key with the specified delimiter.
     *
     * @param null|string $key
     * @param string $separator
     * @return array
     */
    public function getSelectedItems($key = null, $separator = ',')
    {
        $items = static::make($this->where('isSelected', true));

        return $key
            ? $items->implode($key, $separator)
            : $items;
    }

    /**
     * Set selected items.
     *
     * @param int|string|array|\Traversable $items
     * @param null|int $key
     * @return $this
     */
    public function setSelectedItems($items, $key = null)
    {
        if (!$key) {
            $key = $this->first()->getKeyName();
        }

        $this->where('isSelected', true)->map(function($item) {
            return $item->setIsSelected(false);
        });

        if (is_int($items) || is_string($items)) {
            $items = preg_split('/[\s]*,[\s]*/', $items, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_iterable($items)) {
            $items = (array) $items;
        }

        $this->whereIn($key, $items)->map(function($item) {
            return $item->setIsSelected(true);
        });

        return $this;
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        if (is_object($this->getDataKey())) {
            $this->{$this->getDataKeyName()} = clone $this->getDataKey();

            if (is_null(static::isDecorator($this->getDataKey()))) {
                foreach ($this->getData() as $key => $item) {
                    if (is_object($item)) {
                        $this[$key] = clone $item;
                    }
                }
            }
        }
    }
}
