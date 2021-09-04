<?php

namespace Laravelayers\Admin\Repositories\Auth;

use App\Decorators\Admin\Auth\UserActionDecorator;

use Illuminate\Support\Facades\DB;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserRoleActionRepository as UserRoleActionRepositoryContract;
use Laravelayers\Contracts\Auth\UserRoleAction as UserRoleActionContract;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Repositories\Repository;

class UserRoleActionRepository extends Repository implements UserRoleActionRepositoryContract
{
    /**
     * Create a new UserRoleActionRepository instance.
     *
     * @param UserRoleActionContract $userRoleAction
     */
    public function __construct(UserRoleActionContract $userRoleAction)
    {
        $this->model = $userRoleAction;
    }

    /**
     * Save the model to the database.
     *
     * @param DataDecorator|UserActionDecorator $item
     * @return \Laravelayers\Foundation\Decorators\Decorator|mixed
     */
    public function save(DataDecorator $item)
    {
        DB::transaction(function() use($item) {
            $this->model = $this->model->newInstance();

            $this->model
                ->whereRoleId($item->getRole())
                ->where($this->model->actionColumn, $item->getKey())
                ->delete();

            $this->model
                ->forcefill($item->get())
                ->save();
        });

        return $this->decorate($this->model);
    }

    /**
     * Destroy the models of associated products for the given IDs.
     *
     * @param  array|int  $ids
     * @return int
     */
    public function destroy($ids)
    {
        return $this->model
            ->whereRoleIds($ids['roles'])
            ->whereIn($this->model->actionColumn, $ids['actions'])
            ->delete();
    }
}
