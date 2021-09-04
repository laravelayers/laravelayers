<?php

namespace Laravelayers\Foundation\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait Status
{
    /**
     * Status name and comparison operator.
     *
     * @var array
     */
    protected $status = [];

    /**
     * Status name for the administrator and comparison operator.
     *
     * @var array
     */
    protected $adminStatus = [];

    /**
     * The query string variable used to store the status query.
     *
     * @var string
     */
    protected static $statusName = 'status';

    /**
     * Use the status comparison method for the repository on request.
     *
     * @param Request|string $request
     * @param string $status
     * @param array|string $operator
     * @param string $method
     * @return $this
     */
    public function whereStatus($request = '', $status = '', $operator = '', $method = 'whereStatus')
    {
        $name = static::getStatusName();

        if (!$request instanceof Request) {
            if ($request) {
                $name = $request;
                $request = request();

                static::setStatusName($name);
            }
        }

        if ($request) {
            $status = $request->has($name) ? $request->get($name) : $status;
        } else {
            $status = '';
        }

        if (strlen($status)) {
            $operator = $operator ?: current($this->getStatus());

            $this->setStatus($status, $operator, false)
                ->setStatus($status, $operator, true);
        }

        if ($status = $this->getStatus()) {
            $operator = current($status);
            $status = key($status);

            if ($statusMethod = $this->getStatusMethod($status, $method)) {
                if ($statusMethod == $method) {
                    $this->repository->{$statusMethod}($operator, $status);
                } else {
                    $this->repository->{$statusMethod}($operator);
                }
            }
        }

        return $this;
    }

    /**
     * Get the status name and comparison operator.
     *
     * @return array
     */
    public function getStatus()
    {
        if (Gate::allows('admin.*')) {
            $route = Route::getCurrentRoute()->getName();

            if ($route && !Str::startsWith($route, config('admin.prefix'))) {
                $route = sprintf('%1$s.%2$s', config('admin.prefix'), $route);

                if (Gate::allows($route)) {
                    return $this->adminStatus ?: [];
                }
            }
        }

        return $this->status ?: [];
    }

    /**
     * Set the status name and comparison operator.
     *
     * @param string $status
     * @param array|string $operator
     * @param bool $admin
     * @return $this
     */
    public function setStatus($status, $operator = '', $admin = false)
    {
        if (is_array($status)) {
            $operator = current($status);
            $status = key($status);
        }

        if (!$operator) {
            $operator = '=';
        }

        $this->{$admin ? 'adminStatus' : 'status'} = strlen($status) ? [$status => $operator] : [];

        return $this;
    }

    /**
     * Set the status name for the administrator and comparison operator.
     *
     * @param array|string $status
     * @param string $operator
     * @return $this
     */
    public function setAdminStatus($status, $operator = '=')
    {
        return $this->setStatus($status, $operator, true);
    }

    /**
     * Get the query string variable used to store the status query.
     *
     * @return string
     */
    public static function getStatusName()
    {
        return static::$statusName;
    }

    /**
     * Set the query string variable used to store the status query.
     *
     * @param string $name
     */
    public static function setStatusName($name)
    {
        static::$statusName = $name;
    }

    /**
     * Get the status method for the repository.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function getStatusMethod($name, $default = '')
    {
        return $this->getRepositoryMethod("{$default}_{$name}", $default);
    }
}
