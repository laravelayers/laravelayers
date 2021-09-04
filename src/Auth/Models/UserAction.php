<?php

namespace Laravelayers\Auth\Models;

use Laravelayers\Contracts\Auth\UserAction as UserActionContract;
use Laravelayers\Foundation\Models\Model;

class UserAction extends Model implements UserActionContract
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
     * The column name of the "user ID".
     *
     * @var string
     */
    protected $userColumn = 'user_id';

    /**
     * The column name of the "action".
     *
     * @var string
     */
    protected $actionColumn = 'action';

    /**
     * The column name of the "allowed".
     *
     * @var string
     */
    protected $allowedColumn = 'allowed';

    /**
     * The column name of the "ip".
     *
     * @var string
     */
    protected $ipColumn = 'ip';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'userColumn', 'actionColumn', 'allowedColumn', 'ipColumn'
    ];

    /**
     * Define the relationship to the users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(User::class, User::getModel()->getKeyName(), $this->userColumn);
    }

    /**
     * Define the relationship to the user roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class, UserRole::getModel()->roleColumn, $this->actionColumn);
    }

    /**
     * Define the relationship to the user role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userRole()
    {
        return $this->hasOne(UserRole::class, UserRole::getModel()->roleColumn, $this->actionColumn);
    }

    /**
     * Get the column name for the "user ID".
     *
     * @return string
     */
    public function getUserColumnAttribute()
    {
        return $this->userColumn;
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
     * Get the column name for the "ip".
     *
     * @return string
     */
    public function getIpColumnAttribute()
    {
        return $this->ipColumn;
    }

    /**
     * Add a where clause so that the user ID is equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserId($query, $id)
    {
        return $query->where($this->userColumn, $id);
    }

    /**
     * Add a where clause so that the user IDs are equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserIds($query, $ids)
    {
        return $query->whereIn($this->userColumn, (array) $ids);
    }

    /**
     * Add a where clause so that the action names do not match the action names of the specified user ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserIdNot($query, $id)
    {
        return $query->whereNotIn($this->actionColumn, function($query) use($id) {
            $query->select($this->actionColumn)
                ->from($this->getTable())
                ->where($this->userColumn, $id);
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
     * Add a condition that the privileges contain roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeIsRole($query)
    {
        return $query->has('userRole');
    }

    /**
     * Search by default.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $search
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
     * Search byip.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByIp($query, $search)
    {
        return $query->where($this->ipColumn, 'like', "%{$search}%");
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
