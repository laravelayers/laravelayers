<?php

namespace Laravelayers\Foundation\Services;

use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Laravelayers\Contracts\Foundation\Services\Service as ServiceContract;
use Laravelayers\Contracts\Foundation\Repositories\Repository as RepositoryContract;
use Laravelayers\Form\Decorators\FormDecorator;
use Laravelayers\Form\Services\Files;
use Laravelayers\Form\Services\Images;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Decorators\Decorator;
use Laravelayers\Pagination\Decorators\PaginatorDecorator;

/**
 * The base class for services is the layer between the repository and the controller wrapped in the decorator.
 *
 * The service layer is a layer of business logic.
 * Here, and only here, information about business process flow
 * and interaction between the business models should be situated.
 * Gets the data from the repository, necessarily wrapped in the decorator layer.
 * If a non-existent service method is called, but such a public method exists in the repository,
 * then the repository method will be called.
 *
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
 * @method DataDecorator save(DataDecorator $item)
 * @method int destroy(array|int $ids)
 * @method Decorator|mixed getResult()
 * @method array getDecorators()
 * @method array getCollectionDecorators()
 * @method RepositoryContract setCollectionDecorators(array|string $collectionDecorators)
 * @method bool methodExists()
 */
class Service implements ServiceContract
{
    use Files, Images, Search, Sorting, Status, Macroable {
        __call as macroCall;
    }

    /**
     * Repository instance.
     *
     * @var RepositoryContract
     */
    protected $repository;

    /**
     * The number of repository items to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * The query string variable used to store the number of items that will be displayed on the page.
     *
     * @var string
     */
    protected static $perPageName = 'perpage';

    /**
     * Set decorators for the repository.
     *
     * @param array|string $decorators
     * @param RepositoryContract $repository
     * @return $this
     */
    public function setDecorators($decorators = [], RepositoryContract $repository = null)
    {
        $decorators = (array) $decorators;

        if (!$decorators) {
            $decorators = array_merge(
                $this->repository->getDecorators(),
                $this->repository->getCollectionDecorators()
            );
        }

        $collectionDecorators = [];

        foreach($decorators as $key => $decorator) {
            unset($decorators[$key]);

            $decorators[$decorator] = $decorator;

            if (is_subclass_of($decorator, CollectionDecorator::class)
                || strcasecmp($decorator, CollectionDecorator::class) == 0
            ) {
                $collectionDecorators[$decorator] = $decorator;

                unset($decorators[$decorator]);
            }
        }

        if (!$repository) {
            $repository = $this->repository;
        }

        $repository->setDecorators(array_values($decorators))
            ->setCollectionDecorators(array_values($collectionDecorators));

        return $this;
    }

    /**
     * Get the form elements from the specified request by the name of the form elements prefix.
     *
     * @param Request $request
     * @param string $column
     * @param null|string $index
     * @return array
     */
    public function getFormElements(Request $request, $column = '', $index = null)
    {
        $elements = $request->getFormElements();

        return $column ? array_column($elements, $column, $index) : $elements;
    }

    /**
     * Get the number of repository items to return per page.
     *
     * @param Request $request
     * @return int|null
     */
    public function getPerPage(Request $request = null)
    {
        return $request
            ? $request->get(static::getPerPageName(), $this->perPage)
            : $this->perPage;
    }

    /**
     * Set the number of repository items to return per page.
     *
     * @param int $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        if (is_int($perPage)) {
            $this->perPage = $perPage ?: null;
        }

        return $this;
    }

    /**
     * Get the query string variable used to store the number of items that will be displayed on the page.
     *
     * @return string
     */
    public static function getPerPageName()
    {
        return static::$perPageName;
    }

    /**
     * Set the query string variable used to store the number of items that will be displayed on the page.
     *
     * @param string $name
     */
    public static function setPerPageName($name)
    {
        static::$perPageName = $name;
    }

    /**
     * Get the repository method.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function getRepositoryMethod($name, $default = '')
    {
        $method = Str::camel($name);

        return $this->repository->methodExists($method) ? $method : $default;
    }

    /**
     * Handle dynamic method calls into the service.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($this->repository->methodExists($method)) {
            return $this->repository->{$method}(...$parameters);
        }

        throw new BadMethodCallException(
            sprintf('Call to undefined method %s::%s()', static::class, $method)
        );
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->repository = clone $this->repository;
    }
}
