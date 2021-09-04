<?php

namespace Laravelayers\Admin\Services\Auth;

use Illuminate\Http\Request;
use Laravelayers\Admin\Decorators\Auth\UserRoleActionCollectionDecorator;
use Laravelayers\Admin\Decorators\Auth\UserRoleActionDecorator;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleActionRepository as UserRoleActionRepositoryContract;
use Laravelayers\Contracts\Admin\Services\Auth\UserRoleActionService as UserRoleActionServiceContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Services\Service;

class UserRoleActionService extends Service implements UserRoleActionServiceContract
{
    /**
     * Create a new UserRoleActionService instance.
     *
     * @param UserRoleActionRepositoryContract $userRoleActionRepository
     */
    public function __construct(UserRoleActionRepositoryContract $userRoleActionRepository)
    {
        $this->repository = $userRoleActionRepository;

        $this->setDecorators([
            UserRoleActionDecorator::class,
            UserRoleActionCollectionDecorator::class,
        ]);
    }

    /**
     * Fill the resource instance with values.
     *
     * @param Request $request
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserRoleActionDecorator
     */
    public function fill(Request $request)
    {
        if ($request->has('id')) {
            return $this->whereName($request->get('id'))->first();
        }

        return $this->repository->fill();
    }

    /**
     * Find the resource by the specified ID.
     *
     * @param int $id
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserRoleActionDecorator
     */
    public function find($id)
    {
        return $this->repository
            ->whereName($id)
            ->firstOrFail();
    }

    /**
     * Paginate resources.
     *
     * @param Request $request
     * @return \Laravelayers\Pagination\Decorators\PaginatorDecorator|CollectionDecorator|UserRoleActionCollectionDecorator
     */
    public function paginate(Request $request)
    {
        $this->search($request)
            ->sort($request)
            ->whereStatus();

        if ($request->role) {
            if ($request->query('action', $request->get('_action')) != 'add') {
                $this->repository->whereRoleId($request->role);
            } else {
                $this->repository->whereRoleIdNot($request->role);
            }
        }

        $this->repository->groupByAction();

        if ($ids = $this->getFormElements($request, 'id')) {
            $items = $this->repository->whereNames($ids)->get();
        } else {
            $items = $this->repository->paginate($this->getPerPage($request));
        }

        return $items;
    }

    /**
     * Prepare the search query string value.
     *
     * @param string $value
     * @return string
     */
    public function prepareSearch($value)
    {
        if (strpos($value, '/') !== false) {
            $value = trim(str_replace('/', '.', preg_replace(
                '/(^|\/)[0-9]+(\/?)/', '/', parse_url($value)['path']
            )), '.');
        }

        $value = preg_replace('/role(\.|$)/', '', $value);

        return $value;
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator|UserRoleActionCollectionDecorator $items
     * @return CollectionDecorator|UserRoleActionCollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items)
    {
        $request = $items->getElements()->getRequest();

        foreach($items as $key => $item) {
            if ($request->get('pattern')) {
                $item->replaceElements($request->pattern, $request->replacement);
            }

            if ($request->has('allowed')) {
                $item->setAllowed($request->get('allowed'));
            }

            $this->repository->save($item);
        }

        return $items;
    }

    /**
     * Remove multiple resources from the repository.
     *
     * @param CollectionDecorator $items
     * @return int
     */
    public function destroyMultiple(CollectionDecorator $items)
    {
        return $this->repository->destroy([
            'actions' => $items->getKeys(),
            'roles' => (array) $items->first()->getRole()
        ]);
    }
}
