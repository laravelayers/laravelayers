<?php

namespace Laravelayers\Admin\Services\Auth;

use Illuminate\Http\Request;
use Laravelayers\Admin\Decorators\Auth\RoleUserCollectionDecorator;
use Laravelayers\Admin\Decorators\Auth\RoleUserDecorator;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserActionRepository;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Contracts\Admin\Services\Auth\RoleUserService as RoleUserServiceContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Decorators\DataDecorator;

class RoleUserService extends UserService implements RoleUserServiceContract
{
    /**
     * Create a new RoleUserService instance.
     *
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->repository = $userRepository;

        $this->setDecorators([
            RoleUserDecorator::class,
            RoleUserCollectionDecorator::class,
        ]);
    }

    /**
     * Fill the resource instance with values.
     *
     * @param Request $request
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|RoleUserDecorator
     */
    public function fill(Request $request)
    {
        if ($request->has('id')) {
            return ($item = $this->find($request->get('id')))->forget($item->getKeyName());
        }

        return $this->repository->fill();
    }

    /**
     * Find the resource by the specified ID.
     *
     * @param int $id
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|RoleUserDecorator
     */
    public function find($id)
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Paginate resources.
     *
     * @param Request $request
     * @return \Laravelayers\Pagination\Decorators\PaginatorDecorator|CollectionDecorator|RoleUserCollectionDecorator
     */
    public function paginate(Request $request)
    {
        $role = $this->findRole($request->role);

        if ($request->query('action', $request->get('_action')) != 'add') {
            $this->repository->whereAction($role->getRole());
        } else {
            $this->repository->whereActionNot($role->getRole());
        }

        return parent::paginate($request);
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator|RoleUserCollectionDecorator $items
     * @return CollectionDecorator|RoleUserCollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items)
    {
        $request = $items->getElements()->getRequest();

        $actionService = resolve(UserActionService::class);

        $action = $actionService->fill($request);

        $action->put($action->getActionColumn(), $this->findRole($request->role)->getRole());

        foreach($items as $key => $item) {
            $action->setUser($item->getKey());
            $action->setAllowed(1);
            $action->setIp('');

            $actionService->save($action);
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
        $request = $items->getElements()->getRequest();

        return resolve(UserActionRepository::class)->destroy([
            'actions' => (array) $this->findRole($request->role)->getRole(),
            'users' => $items->getKeys()
        ]);
    }

    /**
     * Find the specified role.
     *
     * @param string $role
     * @return DataDecorator
     */
    public function findRole($role)
    {
        return resolve(\Laravelayers\Contracts\Admin\Services\Auth\UserRoleService::class)->find($role);
    }
}
