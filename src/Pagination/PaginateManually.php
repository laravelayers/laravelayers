<?php

namespace Laravelayers\Pagination;

use \Illuminate\Pagination\Paginator as BasePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Manual pagination for the Eloquent model.
 *
 * Based on {@link https://laravel.com/docs/5.5/pagination#manually-creating-a-paginator}.
 *
 * @method Paginator PaginateManually(int $perPage = 15, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method SimplePaginator SimplePaginateManually(int $perPage = 15, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method Paginator DistinctPaginateManually(int $perPage = 15, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 */
trait PaginateManually
{
    /**
     * Paginate manually the given query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Laravelayers\Pagination\Paginator
     */
    public function scopePaginateManually($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($query->getQuery()->groups || $query->getQuery()->distinct) {
            return $query->DistinctPaginateManually($perPage, $columns, $pageName, $page);
        }

        $result = $query->paginate($perPage ?: $this->perPage, $columns, $pageName, $page);

        return new Paginator(
            $result->getCollection(),
            $result->total(),
            $result->perpage(),
            $result->currentPage(),
            static::initOptionsForPaginateManually()
        );
    }

    /**
     * Simple paginate manually the given query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return \Laravelayers\Pagination\SimplePaginator
     */
    public function scopeSimplePaginateManually($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $result = $query->simplePaginate($perPage ?: $this->perPage, $columns, $pageName, $page);

        return (new SimplePaginator(
            $result->getCollection(),
            $result->perpage(),
            $result->currentPage(),
            static::initOptionsForPaginateManually()
        ))->hasMorePagesWhen($result->hasMorePages());
    }

    /**
     * Paginate manually the given query that uses "group by" or "distinct".
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $perPage
     * @param array|string $columnsOrKey
     * @param string $pageName
     * @param int|null $page
     * @return \Laravelayers\Pagination\Paginator
     */
    public function scopeDistinctPaginateManually($query, $perPage = 15, $columnsOrKey = ['*'], $pageName = 'page', $page = null)
    {
        $key = $this->getQualifiedKeyName();
        $columns = $columnsOrKey;

        if (!is_array($columnsOrKey)) {
            $columns = ['*'];

            if ($columnsOrKey) {
                $key = $columnsOrKey;
            }
        }

        $newQuery = (clone $query);
        $newQuery->getQuery()->groups = null;

        return new Paginator(
            $query->forPage($page, $perPage)->get($columns),
            $newQuery->distinct()->count($key),
            $perPage,
            $page ?: Paginator::resolveCurrentPage($pageName),
            static::initOptionsForPaginateManually()
        );
    }

    /**
     *
     *
     * @return array
     */
    public static function initOptionsForPaginateManually()
    {
        $query = function($value) {
            return is_null($value) ? '' : $value;
        };

        return [
            'path'  => BasePaginator::resolveCurrentPath(),
            'query' => array_map($query, Request::query())
        ];
    }
}