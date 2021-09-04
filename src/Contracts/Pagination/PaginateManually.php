<?php

namespace Laravelayers\Contracts\Pagination;

/**
 * @see \Laravelayers\Pagination\PaginateManually
 */
interface PaginateManually
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
    public function scopePaginateManually($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null);

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
    public function scopeSimplePaginateManually($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null);
}
