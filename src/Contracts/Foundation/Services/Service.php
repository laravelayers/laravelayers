<?php

namespace Laravelayers\Contracts\Foundation\Services;

use Illuminate\Http\Request;
use Laravelayers\Contracts\Foundation\Repositories\Repository as RepositoryContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;

/**
 * @method DataDecorator fill(array|string $relations = [], array $values = [])
 * @method DataDecorator fillWithTypes(array|string $relations)
 * @method DataDecorator|CollectionDecorator|mixed find(mixed $id)
 * @method DataDecorator|CollectionDecorator|mixed findOrFail(mixed $id)
 * @method DataDecorator|mixed first()
 * @method DataDecorator|mixed firstOrFail()
 * @method PaginatorDecorator paginate(int|null $perPage = null, string $pageName = 'page', int|null $page = null)
 * @method PaginatorDecorator simplePaginate(int|null $perPage = null, string $pageName = 'page', int|null $page = null)
 * @method CollectionDecorator get()
 * @method bool exists()
 * @method bool doesntExist()
 * @method int count()
 * @see DataDecorator save(DataDecorator $item)
 * @method int destroy(array|int $ids)
 * @method Decorator|mixed getResult()
 * @method array getDecorators()
 * @method array getCollectionDecorators()
 * @method RepositoryContract setCollectionDecorators(array|string $collectionDecorators)
 * @method bool methodExists()
 *
 * @see \Laravelayers\Foundation\Services\Service
 */
interface Service extends Files, Images, Search, Sorting, Status
{
    /**
     * Set decorators for the repository.
     *
     * @param array|string $decorators
     * @param RepositoryContract $repository
     * @return $this
     */
    public function setDecorators($decorators = [], RepositoryContract $repository = null);

    /**
     * Get the form elements from the specified request by the name of the form elements prefix.
     *
     * @param Request $request
     * @param string $column
     * @param null|string $index
     * @return array
     */
    public function getFormElements(Request $request, $column = '', $index = null);

    /**
     * Get the number of repository items to return per page.
     *
     * @param Request $request
     * @return int|null
     */
    public function getPerPage(Request $request = null);

    /**
     * Set the number of repository items to return per page.
     *
     * @param int $perPage
     * @return $this
     */
    public function setPerPage($perPage);

    /**
     * Get the query string variable used to store the number of items that will be displayed on the page.
     *
     * @return string
     */
    public static function getPerPageName();

    /**
     * Set the query string variable used to store the number of items that will be displayed on the page.
     *
     * @param string $name
     */
    public static function setPerPageName($name);
}
