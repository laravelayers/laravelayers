<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Illuminate\Http\Request;

interface Status
{
    /**
     * Use the status comparison method for the repository on request.
     *
     * @param Request|string $request
     * @param string $status
     * @param array|string $operator
     * @param string $method
     * @return $this
     */
    public function whereStatus($request = '', $status = '', $operator = '', $method = 'whereStatus');

    /**
     * Get the status name and comparison operator.
     *
     * @return array
     */
    public function getStatus();

    /**
     * Set the status name and comparison operator.
     *
     * @param string $status
     * @param array|string $operator
     * @param bool $admin
     * @return $this
     */
    public function setStatus($status, $operator = '', $admin = false);

    /**
     * Set the status name for the administrator and comparison operator.
     *
     * @param array|string $status
     * @param string $operator
     * @return $this
     */
    public function setAdminStatus($status, $operator = '=');

    /**
     * Get the query string variable used to store the status query.
     *
     * @return string
     */
    public static function getStatusName();

    /**
     * Set the query string variable used to store the status query.
     *
     * @param string $name
     */
    public static function setStatusName($name);
}
