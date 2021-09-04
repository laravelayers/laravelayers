<?php

namespace Laravelayers\Foundation\Services;

use Illuminate\Http\Request;

trait Sorting
{
    /**
     * The name and direction of the default sorting.
     *
     * @var array
     */
    protected $sorting = [];

    /**
     * The query string variable used to store the sort query.
     *
     * @var string
     */
    protected static $sortingName = 'sort';

    /**
     * The query string variable used to store descending sort order.
     *
     * @var string
     */
    protected static $sortingDescName = 'desc';

    /**
     * Use the sort method for the repository on request.
     *
     * @param Request|string $request
     * @param string $direction
     * @param string $method
     * @return $this
     */
    public function sort($request = '', $direction = '', $method = 'sort')
    {
        $name = static::getSortingName();
        $direction = $direction ?: static::getSortingDescName();

        if (!$request instanceof Request) {
            if ($request) {
                $name = $request;
                $request = request();

                static::setSortingName($name);
                static::setSortingDescName($direction);
            }
        }

        if ($request && $request->has($name)) {
            $this->setSorting($request->get($name), $request->get($direction));
        }

        $sortingMethod = $this->getSortingMethod(key($this->getSorting()), $method);

        if ($sortingMethod == $method) {
            if ((clone $this->repository)->fill()->has(key($this->getSorting()))) {
                $column = key($this->getSorting());
            }
        }

        if (!empty($column)) {
            $this->repository->{$sortingMethod}(current($this->getSorting()), $column);
        } else {
            $this->repository->{$sortingMethod}(current($this->getSorting()));
        }

        return $this;
    }

    /**
     * Get the name and direction of the default sorting.
     *
     * @return array
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set the name and direction of the default sorting.
     *
     * @param $sorting
     * @param string $direction
     * @return $this
     */
    public function setSorting($sorting, $direction = '')
    {
        if (is_array($sorting)) {
            $direction = current($sorting);
            $sorting = key($sorting);
        }

        if (is_bool($direction)) {
            $direction = (int) $direction;
        }

        if (strlen($direction)) {
            $direction = ($direction && strtolower($direction) != 'asc') ? 'desc' : 'asc';
        } else {
            $direction = '';
        }

        $this->sorting = strlen($sorting) ? [$sorting => $direction] : [];

        return $this;
    }

    /**
     * Get the query string variable used to store the sorting query method.
     *
     * @return string
     */
    public static function getSortingName()
    {
        return static::$sortingName;
    }

    /**
     * Set the query string variable used to store the sorting query method.
     *
     * @param string $name
     */
    public static function setSortingName($name)
    {
        static::$sortingName = $name;
    }

    /**
     * Get the query string variable used to store descending sorting order.
     *
     * @return string
     */
    public static function getSortingDescName()
    {
        return static::$sortingDescName;
    }

    /**
     * Set the query string variable used to store descending sorting order.
     *
     * @param string $name
     */
    public static function setSortingDescName($name)
    {
        static::$sortingDescName = $name;
    }

    /**
     * Get the sorting method for the repository.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function getSortingMethod($name, $default = 'sort')
    {
        return $this->getRepositoryMethod("{$default}_by_{$name}", $default);
    }
}
