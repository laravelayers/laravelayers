<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Illuminate\Http\Request;

/**
 * @see \Laravelayers\Foundation\Services\Search
 */
interface Search
{
    /**
     * Use the search method for the repository on request.
     *
     * @param Request|string $request
     * @param string $field
     * @param string $method
     * @return $this
     */
    public function search($request, $field = '', $method = 'search');

    /**
     * Prepare the search query string value.
     *
     * @param string $value
     * @return string
     */
    public function prepareSearch($value);

    /**
     * Get the query string variable used to store the search query.
     *
     * @return string
     */
    public static function getSearchName();

    /**
     * Set the query string variable used to store the search query.
     *
     * @param string $name
     */
    public static function setSearchName($name);

    /**
     * Get the query string variable used to store the search query method.
     *
     * @return string
     */
    public static function getSearchByName();

    /**
     * Set the query string variable used to store the search query method.
     *
     * @param string $name
     */
    public static function setSearchByName($name);
}
