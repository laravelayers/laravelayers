<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Illuminate\Http\Request;

/**
 * @see \Laravelayers\Foundation\Services\Sorting
 */
interface Sorting
{
    /**
     * Use the sort method for the repository on request.
     *
     * @param Request|string $request
     * @param string $direction
     * @param string $method
     * @return $this
     */
    public function sort($request = '', $direction = '', $method = 'sort');

    /**
     * Get the name and direction of the default sorting.
     *
     * @return array
     */
    public function getSorting();

    /**
     * Set the name and direction of the default sorting.
     *
     * @param $sorting
     * @param string $direction
     * @return $this
     */
    public function setSorting($sorting, $direction = '');

    /**
     * Get the query string variable used to store the sorting query method.
     *
     * @return string
     */
    public static function getSortingName();

    /**
     * Set the query string variable used to store the sorting query method.
     *
     * @param string $name
     */
    public static function setSortingName($name);

    /**
     * Get the query string variable used to store descending sorting order.
     *
     * @return string
     */
    public static function getSortingDescName();

    /**
     * Set the query string variable used to store descending sorting order.
     *
     * @param string $name
     */
    public static function setSortingDescName($name);
}
