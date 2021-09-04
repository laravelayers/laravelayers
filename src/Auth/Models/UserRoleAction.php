<?php

namespace Laravelayers\Auth\Models;

use Laravelayers\Contracts\Auth\UserRoleAction as UserRoleActionContract;
use Laravelayers\Foundation\Models\Model;

class UserRoleAction extends Model implements UserRoleActionContract
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'action';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The column name of the "role ID".
     *
     * @var string
     */
    protected $roleColumn = 'role_id';

    /**
     * The column name of the "action".
     *
     * @var string
     */
    protected $actionColumn = 'action';

    /**
     * The column name of the "action mode".
     *
     * @var string
     */
    protected $allowedColumn = 'allowed';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'roleColumn', 'actionColumn', 'allowedColumn'
    ];

    /**
     * Define the relationship to the user roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userRoles()
    {
        return $this->belongsTo(UserRole::class, UserRole::getModel()->getKeyName(), UserRole::getModel()->getKeyName());
    }

    /**
     * Get the column name for the "role ID".
     *
     * @return string
     */
    public function getRoleColumnAttribute()
    {
        return $this->roleColumn;
    }

    /**
     * Get the column name for the "action".
     *
     * @return string
     */
    public function getActionColumnAttribute()
    {
        return $this->actionColumn;
    }

    /**
     * Get the column name for the "allowed".
     *
     * @return string
     */
    public function getAllowedColumnAttribute()
    {
        return $this->allowedColumn;
    }

    /**
     * Add a where clause so that the role ID is equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleId($query, $id)
    {
        return $query->where($this->roleColumn, $id);
    }

    /**
     * Add a where clause so that the role IDs are equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleIds($query, $ids)
    {
        return $query->whereIn($this->roleColumn, (array) $ids);
    }

    /**
     * Add a where clause so that the action names do not match the action names of the specified role ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleIdNot($query, $id)
    {
        return $query->whereNotIn($this->actionColumn, function($query) use($id) {
            $query->select($this->actionColumn)
                ->from($this->getTable())
                ->where($this->roleColumn, $id);
        });
    }

    /**
     * Add a where clause so that the action name is equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereName($query, $name)
    {
        return $query->where($this->actionColumn, $name);
    }

    /**
     * Add a where clause so that the action names are equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|array $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNames($query, $names)
    {
        return $query->whereIn($this->actionColumn, (array) $names);
    }

    /**
     * Search by default.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereKey($search);
    }

    /**
     * Search by action.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByAction($query, $search)
    {
        return $query->where($this->actionColumn, 'like', "%{$search}%");
    }

    /**
     * Group by action name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByAction($query)
    {
        return $query->groupBy($this->actionColumn);
    }

    /**
     * Sort by default.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $direction = 'desc')
    {
        return $query->orderBy($this->getQualifiedKeyName(), $direction);
    }

    /**
     * Sort by action.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByAction($query, $direction = 'asc')
    {
        return $query->orderBy($this->actionColumn, $direction);
    }

    /**
     * Sort by allowed.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByAllowed($query, $direction = 'desc')
    {
        return $query->orderBy($this->allowedColumn, $direction);
    }
}
