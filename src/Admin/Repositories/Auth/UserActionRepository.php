<?php

namespace Laravelayers\Admin\Repositories\Auth;

use App\Decorators\Admin\Auth\UserActionDecorator;

use Illuminate\Support\Facades\DB;
use Laravelayers\Contracts\Admin\Repositories\Auth\UserActionRepository as UserActionRepositoryContract;
use Laravelayers\Contracts\Auth\UserAction as UserActionContract;
use Laravelayers\Foundation\Decorators\DataDecorator;
use Laravelayers\Foundation\Repositories\Repository;

class UserActionRepository extends Repository implements UserActionRepositoryContract
{
    /**
     * Create a new UserActionRepository instance.
     *
     * @param UserActionContract $userAction
     */
    public function __construct(UserActionContract $userAction)
    {
        $this->model = $userAction;
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
                ->whereUserId($item->getUser())
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
            ->whereUserIds($ids['users'])
            ->whereIn($this->model->actionColumn, $ids['actions'])
            ->delete();
    }
}
