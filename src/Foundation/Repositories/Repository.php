<?php

namespace Laravelayers\Foundation\Repositories;

use \BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Models\Model;
use \Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;

/**
 * The repository class is the layer between the model and the service layer.
 *
 * The repository layer works with the concrete implementation of data storage.
 * The repository plays the role of a query object: It gets data from the database
 * and conducts the work of several Eloquent models.
 * Returns the data, necessarily wrapped in the decorator layer.
 * If a non-existent repository method is called,
 * but such a public method with the `scope` prefix exists in the model,
 * then the model method will be called.
 * If a non-existent repository method is called with the `with` or` withCount` prefix,
 * then the corresponding model method will be called, using the method name as the passed parameter.
 */
class Repository
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * Model instance.
     *
     * @var Model|Builder
     */
    protected $model;

    /**
     * Query result.
     *
     * @var Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\AbstractPaginator
     */
    private $result;

    /**
     * The array of decorator classes.
     *
     * @var array
     */
    protected $decorators = [
        Decorator::class
    ];

    /**
     * The array of collection decorator classes.
     *
     * @var array
     */
    protected $collectionDecorators = [];

    /**
     * List of prefixes for dynamic repository methods.
     *
     * @var string
     */
    protected $prefixes = 'withCount|with|has|doesntHave';

    /**
     * Fill the model with columns listing and make it.
     *
     * @param array|string $relations
     * @param array $values
     * @return DataDecorator
     */
    public function fill($relations = [], $values = [])
    {
        $this->model = $this->fillWithColumns($this->model->getModel(), $values);

        foreach((array) $relations as $list) {
            $model = $this->model;

            foreach(explode('.', $list) as $relation) {
                $value = $this->fillWithColumns($model->{$relation}()->getModel(), Arr::get($values, $relation));

                $model = $this->model->getRelations()[$relation]
                    ?? $model->setRelation($relation, $value)->getRelation($relation);
            }
        }

        return $this->decorate($this->model);
    }

    /**
     * Fill the model with columns listing, get the column types and make it.
     *
     * @param array|string $relations
     * @return DataDecorator
     */
    public function fillWithTypes($relations = [])
    {
        $item = $this->fill($relations);
        $item->types = $this->model->getColumnTypes();

        foreach((array) $relations as $relation) {
            $model = $this->model;
            $types = $item->types;

            foreach(explode('.', $relation) as $value) {
                $model = $model->getRelation($value);
                $types = $types->put($value, $model->getColumnTypes())->{$value};
            }
        }

        return $item;
    }

    /**
     * Get the column types and make it.
     *
     * @return array
     */
    protected function getColumnTypes()
    {
        return $this->model->getColumnTypes();
    }

    /**
     * Fill the model with columns listing.
     *
     * @param EloquentModel|Model $model
     * @param array $values
     * @return EloquentModel|Model
     */
    protected function fillWithColumns(EloquentModel $model, $values = [])
    {
        $columns = array_fill_keys($model->getColumnListing(), null);

        foreach(Arr::only((array) $values, array_keys($columns)) as $key => $value) {
            $columns[$key] = $value;
        }

        return $model
            ->newInstance()
            ->forceFill($columns)
            ->syncOriginal();
    }

    /**
     * Fill the related model with columns listing.
     *
     * @param EloquentModel|Model $model
     * @return EloquentModel|Model
     */
    protected function fillRelationWithColumns(EloquentModel $model)
    {
        return $model
            ->newInstance()
            ->forceFill(array_fill_keys($model->getColumnListing(), null))
            ->syncOriginal();
    }

    /**
     * Find a model by its primary key and make it.
     *
     * @param mixed $id
     * @return DataDecorator|CollectionDecorator|mixed
     */
    public function find($id)
    {
        return $this->decorate(
            $this->model->find($id)
        );
    }

    /**
     * Find a model by its primary key and make it or throw an exception.
     *
     * @param mixed $id
     * @return DataDecorator|CollectionDecorator|mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id)
    {
        return $this->decorate(
            $this->model->findOrFail($id)
        );
    }

    /**
     * Execute the query, get the first result and make it.
     *
     * @return DataDecorator|mixed
     */
    public function first()
    {
        return $this->decorate(
            $this->model->first()
        );
    }

    /**
     * Execute the query, get the first result and make it or throw an exception.
     *
     * @return DataDecorator|mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail()
    {
        return $this->decorate(
            $this->model->firstOrFail()
        );
    }

    /**
     * Paginate the given query and make it.
     *
     * @param int|null $perPage
     * @param string $pageName
     * @param int|null $page
     * @return PaginatorDecorator
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null)
    {
        return $this->decorate(
            $this->model->PaginateManually($perPage, ['*'], $pageName, $page)
        );
    }

    /**
     * Simple paginate the given query and make it.
     *
     * @param int|null $perPage
     * @param string $pageName
     * @param int|null $page
     * @return PaginatorDecorator
     */
    public function simplePaginate($perPage = null, $pageName = 'page', $page = null)
    {
        return $this->decorate(
            $this->model->SimplePaginateManually($perPage, ['*'], $pageName, $page)
        );
    }

    /**
     * Execute the query and make it.
     *
     * @return CollectionDecorator|mixed
     */
    public function get()
    {
        return $this->decorate(
            $this->model->get()
        );
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->model->exists();
    }

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    public function doesntExist()
    {
        return $this->model->doesntExist();
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @return int
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Save the model to the database.
     *
     * @param DataDecorator $item
     * @return DataDecorator|CollectionDecorator|PaginatorDecorator|mixed
     */
    public function save(DataDecorator $item)
    {
        $result = $this->result($item);

        $timestamps = $result->timestamps;

        $this->result($item)->timestamps = $item->timestamps;

        $saved = $result->forcefill($item->get())->save();

        $result->timestamps = $timestamps;

        return $this->decorate($saved ? $this->result() : []);
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param array|int $ids
     * @return int
     */
    public function destroy($ids)
    {
        $result = $this->model->destroy($ids);

        $this->decorate([]);

        return $result;
    }

    /**
     * Get all query results and make it.
     *
     * @return Decorator|mixed
     */
    public function getResult()
    {
        return $this->decorate($this->result());
    }

    /**
     * Get the query result for the model.
     *
     * @param DataDecorator|null $item
     * @return Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\AbstractPaginator
     */
    protected function result(DataDecorator $item = null)
    {
        $result = $this->result ?: $this->model;

        return ($item && $this->result instanceof Collection)
            ? $result->find($item->getKey())
            : $result;
    }

    /**
     * Set the query for the model.
     *
     * @param Builder $query
     * @return $this
     */
    protected function query(Builder $query)
    {
        $this->model = $query;

        return $this;
    }

    /**
     * Decorate the result and reset the model.
     *
     * @param array|Model|Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\AbstractPaginator $result
     * @return Decorator|mixed
     */
    protected function decorate($result)
    {
        $this->model = $this->model->getModel();

        $this->result = $result;

        foreach($this->getDecorators() as $decorator) {
            $result = $decorator::make($result);
        }

        foreach($this->getCollectionDecorators() as $collectionDecorator) {
            $result = $collectionDecorator::make($result);
        }

        return $result;
    }

    /**
     * Get Decorators.
     *
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * Set Decorators.
     *
     * @param array|string $decorators
     * @return $this
     */
    public function setDecorators($decorators)
    {
        $this->decorators = (array) $decorators;

        return $this;
    }

    /**
     * Get Decorators of collection.
     *
     * @return array
     */
    public function getCollectionDecorators()
    {
        return $this->collectionDecorators;
    }

    /**
     * Set Decorators of collection.
     *
     * @param array|string $collectionDecorators
     * @return $this
     */
    public function setCollectionDecorators($collectionDecorators)
    {
        $this->collectionDecorators = (array) $collectionDecorators;

        return $this;
    }

    /**
     * Handle dynamic method calls into the repository.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (preg_match_all("/^({$this->prefixes})(.+)/i", $method, $prefix)) {
            return $this->query(
                $this->model->{$prefix[1][0]}(lcfirst($prefix[2][0]))
            );
        }

        if ($this->methodExists($method)) {
            return $this->query(
                $this->model->{lcfirst($method)}(...$parameters)
            );
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException(
            sprintf('Call to undefined method %s::%s()', static::class, $method)
        );
    }

    /**
     * Checks if method exists.
     *
     * @param  string $method
     * @return bool
     */
    public function methodExists($method)
    {
        return method_exists($this, $method)
            || method_exists($this->model->getModel(), Str::camel("scope_{$method}"))
            || preg_match("/^({$this->prefixes})(.+)/i", $method)
            || static::hasMacro($method);
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->model = clone $this->model;

        $this->result = $this->result ? clone $this->result : $this->result;
    }
}
