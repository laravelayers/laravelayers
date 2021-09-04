<?php

namespace Laravelayers\Contracts\Foundation\Repositories;

use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;

/**
 * @see \Laravelayers\Foundation\Repositories\Repository
 */
interface Repository
{
    /**
     * Fill the model with columns listing and make it.
     *
     * @param array|string $relations
     * @param array $values
     * @return DataDecorator
     */
    public function fill($relations = [], $values = []);

    /**
     * Fill the model with columns listing, get the column types and make it.
     *
     * @param array|string $relations
     * @return DataDecorator
     */
    public function fillWithTypes($relations = []);

    /**
     * Find a model by its primary key and make it.
     *
     * @param mixed $id
     * @return DataDecorator|CollectionDecorator|mixed
     */
    public function find($id);

    /**
     * Find a model by its primary key and make it or throw an exception.
     *
     * @param mixed $id
     * @return DataDecorator|CollectionDecorator|mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id);

    /**
     * Execute the query, get the first result and make it.
     *
     * @return DataDecorator|mixed
     */
    public function first();

    /**
     * Execute the query, get the first result and make it or throw an exception.
     *
     * @return DataDecorator|mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail();

    /**
     * Paginate the given query and make it.
     *
     * @param int|null $perPage
     * @param string $pageName
     * @param int|null $page
     * @return PaginatorDecorator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null);

    /**
     * Simple paginate the given query and make it.
     *
     * @param int|null $perPage
     * @param string $pageName
     * @param int|null $page
     * @return PaginatorDecorator
     */
    public function simplePaginate($perPage = null, $pageName = 'page', $page = null);

    /**
     * Execute the query and make it.
     *
     * @return CollectionDecorator|mixed
     */
    public function get();

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists();

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    public function doesntExist();

    /**
     * Retrieve the "count" result of the query.
     *
     * @return int
     */
    public function count();

    /**
     * Save the model to the database.
     *
     * @param DataDecorator $item
     * @return DataDecorator|CollectionDecorator|PaginatorDecorator|mixed
     */
    public function save(DataDecorator $item);

    /**
     * Destroy the models for the given IDs.
     *
     * @param array|int $ids
     * @return int
     */
    public function destroy($ids);

    /**
     * Get all query results and make it.
     *
     * @return Decorator|mixed
     */
    public function getResult();

    /**
     * Get Decorators.
     *
     * @return array
     */
    public function getDecorators();

    /**
     * Set Decorators.
     *
     * @param array|string $decorators
     * @return $this
     */
    public function setDecorators($decorators);

    /**
     * Get Decorators of collection.
     *
     * @return array
     */
    public function getCollectionDecorators();

    /**
     * Set Decorators of collection.
     *
     * @param array|string $collectionDecorators
     * @return $this
     */
    public function setCollectionDecorators($collectionDecorators);

    /**
     * Checks if method exists.
     *
     * @param  string $method
     * @return bool
     */
    public function methodExists($method);
}
