<?php

namespace Laravelayers\Admin\Services\Auth;

use Laravelayers\Admin\Decorators\Auth\UserDecorator;
use Laravelayers\Admin\Decorators\Auth\UserCollectionDecorator;
use Illuminate\Http\Request;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserRepository as UserRepositoryContract;
use Laravelayers\Contracts\Admin\Services\Auth\UserService as UserServiceContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Services\Service;

class UserService extends Service implements UserServiceContract
{
    /**
     * Create a new UserService instance.
     *
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->repository = $userRepository;

        $this->setDecorators([
            UserDecorator::class,
            UserCollectionDecorator::class,
        ]);
    }

    /**
     * Fill the resource instance with values.
     *
     * @param Request $request
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserDecorator
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
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserDecorator
     */
    public function find($id)
    {
        return $this->repository
            ->withCountUserActions()
            ->findOrFail($id);
    }

    /**
     * Paginate resources.
     *
     * @param Request $request
     * @return \Laravelayers\Pagination\Decorators\PaginatorDecorator|CollectionDecorator|UserCollectionDecorator
     */
    public function paginate(Request $request)
    {
        $this->search($request)
            ->sort($request)
            ->whereStatus();

        return ($ids = $this->getFormElements($request, 'id'))
            ? $this->repository->findOrFail($ids)
            : $this->repository->withCountUserActions()->paginate($this->getPerPage($request));
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator|UserCollectionDecorator $items
     * @return CollectionDecorator|UserCollectionDecorator
     */
    public function updateMultiple(CollectionDecorator $items)
    {
        $request = $items->getElements()->getRequest();

        foreach($items as $key => $item) {
            if ($request->get('pattern')) {
                $item->replaceElements($request->pattern, $request->replacement);
            }

            $this->repository->save($item);
        }

        return $items;
    }
}
