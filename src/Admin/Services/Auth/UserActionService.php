<?php

namespace Laravelayers\Admin\Services\Auth;

use Illuminate\Http\Request;
use Laravelayers\Admin\Decorators\Auth\UserActionCollectionDecorator;
use Laravelayers\Admin\Decorators\Auth\UserActionDecorator;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserActionRepository as UserActionRepositoryContract ;
use Laravelayers\Contracts\Admin\Services\Auth\UserActionService as UserActionServiceContract;
use Laravelayers\Foundation\Decorators\CollectionDecorator;
use Laravelayers\Foundation\Services\Service;

class UserActionService extends Service implements UserActionServiceContract
{
    /**
     * Create a new UserActionService instance.
     *
     * @param UserActionRepositoryContract  $userActionRepository
     */
    public function __construct(UserActionRepositoryContract $userActionRepository)
    {
        $this->repository = $userActionRepository;

        $this->setDecorators([
            UserActionDecorator::class,
            UserActionCollectionDecorator::class,
        ]);
    }

    /**
     * Fill the resource instance with values.
     *
     * @param Request $request
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserActionDecorator
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
     * @return \Laravelayers\Foundation\Decorators\DataDecorator|UserActionDecorator
     */
    public function find($id)
    {
        return $this->repository
            ->withUserRole()
            ->whereName($id)
            ->firstOrFail();
    }

    /**
     * Paginate resources.
     *
     * @param Request $request
     * @return \Laravelayers\Pagination\Decorators\PaginatorDecorator|CollectionDecorator|UserActionCollectionDecorator
     */
    public function paginate(Request $request)
    {
        $this->search($request)
            ->sort($request)
            ->whereStatus();

        $this->repository->withUserRole();

        if ($request->get('is_role')) {
            $this->repository->hasUserRole();
        }

        if ($request->user) {
            if ($request->query('action', $request->get('_action')) != 'add') {
                $this->repository->whereUserId($request->user);
            } else {
                $this->repository->whereUserIdNot($request->user);
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

        return $value;
    }

    /**
     * Update multiple resources in the repository.
     *
     * @param CollectionDecorator|UserActionCollectionDecorator $items
     * @return CollectionDecorator|UserActionCollectionDecorator
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
            'users' => (array) $items->first()->getUser()
        ]);
    }
}
