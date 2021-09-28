<?php

namespace Laravelayers\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravelayers\Contracts\Auth\UserRole as UserRoleContract;
use Laravelayers\Foundation\Models\Model;

class UserRole extends Model implements UserRoleContract
{
    use HasFactory;
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The column name of the "role name".
     *
     * @var string
     */
    protected $roleColumn = 'name';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'roleColumn'
    ];

    /**
     * Define the relationship to the user actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions()
    {
        return $this->hasMany(UserAction::class, UserAction::getModel()->actionColumn, $this->roleColumn);
    }

    /**
     * Define the relationship to the user roles actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoleActions()
    {
        return $this->hasMany(UserRoleAction::class, UserRoleAction::getModel()->roleColumn, $this->primaryKey);
    }

    /**
     * Get the column name for the "role".
     *
     * @return string
     */
    public function getRoleColumnAttribute()
    {
        return $this->roleColumn;
    }

    /**
     * Add a where clause so that the role ID is not equal to the specified one.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotRoleId($query, $id)
    {
        return $query->whereKeyNot($id);
    }

    /**
     * Add a where clause so that the role name is equal to the specified.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRole($query, $role)
    {
        return $query->where($this->roleColumn, $role);
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
     * Search by role name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where($this->roleColumn, 'like', "{$search}%");
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
     * Sort by role name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByName($query, $direction = 'asc')
    {
        return $query->orderBy($this->roleColumn, $direction);
    }
}
