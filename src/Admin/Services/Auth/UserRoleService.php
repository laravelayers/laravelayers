<?php

namespace Laravelayers\Admin\Services\Auth;

use Laravelayers\Admin\Decorators\Auth\UserRoleCollectionDecorator;
use Laravelayers\Admin\Decorators\Auth\UserRoleDecorator;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleRepository as UserRoleRepositoryContract;
use Laravelayers\Contracts\Admin\Services\Auth\UserRoleService as UserRoleServiceContract;
use Laravelayers\Foundation\Services\Service;
use Illuminate\Http\Request;
use Laravelayers\Foundation\Decorators\CollectionDecorator;

class UserRoleService extends Service implements UserRoleServiceContract
{
    /**
     * Create a new UserRoleService instance.
     *
     * @param UserRoleRepositoryContract $roleRepository
     */
    public function __construct(UserRoleRepositoryContract $roleRepository)
    {
        $this->repository = $roleRepository;

        $this->setDecorators([
            UserRoleDecorator::class,
            UserRoleCollectionDecorator::class,
        ]);
    }

    /**
     * Fill the resource instance with values.
     *
     * @param Request $request
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserRoleDecorator
     */
    public function fill(Request $request)
    {
        if ($request->get('role')) {
            $role = $this->repository->WhereRole($request->get('role'))->first();
        }

        if ($request->has('id')) {
            $item = $this->find($request->get('id'));
            $item = $item->forget($item->getKeyName());
        } else {
            $item = $this->repository->fill();
        }

        if (!empty($role) && $role->isNotEmpty()) {
            $item->getElements()->setError('role', trans('admin/auth/role.errors.exists'));
        }

        return $item;
    }

    /**
     * Find the resource by the specified ID.
     *
     * @param int $id
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserRoleDecorator
     */
    public function find($id)
    {
        return $this->repository
            ->withCountUserRoleActions()
            ->withCountUserActions()
            ->findOrFail($id);
    }

    /**
     * Paginate resources.
     *
     * @param Request $request
     * @return \Laravelayers\Pagination\Decorators\PaginatorDecorator|CollectionDecorator|UserRoleCollectionDecorator
     */
    public function paginate(Request $request)
    {
        $this->search($request)
            ->sort($request)
            ->whereStatus();

        if ($ids = $this->getFormElements($request, 'id')) {
            $items = $this->repository->findOrFail($ids);
        } else {
            $items = $this->repository
                ->withCountUserRoleActions()
                ->withCountUserActions()
                ->paginate($this->getPerPage($request));
        }

        return $items;
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator|UserRoleCollectionDecorator $items
     * @return CollectionDecorator|UserRoleCollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items)
    {
        $request = $items->getElements()->getRequest();

        foreach ($items as $key => $item) {
            if ($request->get('pattern')) {
                $item->replaceElements($request->pattern, $request->replacement, ['role']);
            }

            $this->repository->save($item);
        }

        return $items;
    }
}
