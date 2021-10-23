<?php

namespace Laravelayers\Foundation\Services;

use Illuminate\Http\Request;

trait Search
{
    /**
     * The query string variable used to store the search query.
     *
     * @var string
     */
    protected static $searchName = 'search';

    /**
     * The query string variable used to store the search query method.
     *
     * @var string
     */
    protected static $searchByName = 'search_by';

    /**
     * Use the search method for the repository on request.
     *
     * @param Request|string $request
     * @param string $field
     * @param string $method
     * @return $this
     */
    public function search($request, $field = '', $method = 'search')
    {
        $name = static::getSearchName();
        $field = $field ?: static::getSearchByName();

        if (!$request instanceof Request) {
            $name = $request;
            $request = request();

            static::setSearchName($name);
            static::setSearchByName($field);
        }

        if ($search = $this->prepareSearch($request->get($name))) {
            $searchMethod = $this->getSearchMethod($request->get($field), $method);

            if ($searchMethod == $method) {
                if (array_key_exists($request->get($field), (clone $this->repository)->fill()->get())) {
                    $column = $request->get($field);
                }
            }

            if (!empty($column)) {
                $this->repository->{$searchMethod}($search, $column);
            } else {
                $this->repository->{$searchMethod}($search);
            }
        }

        return $this;
    }

    /**
     * Prepare the search query string value.
     *
     * @param string $value
     * @return string
     */
    public function prepareSearch($value)
    {
        return $value;
    }

    /**
     * Get the query string variable used to store the search query.
     *
     * @return string
     */
    public static function getSearchName()
    {
        return static::$searchName;
    }

    /**
     * Set the query string variable used to store the search query.
     *
     * @param string $name
     */
    public static function setSearchName($name)
    {
        static::$searchName = $name;
    }

    /**
     * Get the query string variable used to store the search query method.
     *
     * @return string
     */
    public static function getSearchByName()
    {
        return static::$searchByName;
    }

    /**
     * Set the query string variable used to store the search query method.
     *
     * @param string $name
     */
    public static function setSearchByName($name)
    {
        static::$searchByName = $name;
    }

    /**
     * Get the search method for the repository.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function getSearchMethod($name, $default = 'search')
    {
        return $this->getRepositoryMethod("{$default}_by_{$name}", $default);
    }
}
