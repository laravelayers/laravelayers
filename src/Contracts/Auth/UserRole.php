<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

/**
 * @see \Laravelayers\Auth\Models\UserRole
 */
interface UserRole extends ModelContract
{
    /**
     * Define the relationship to the user actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions();

    /**
     * Define the relationship to the user roles actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoleActions();

    /**
     * Get the column name for the "role".
     *
     * @return string
     */
    public function getRoleColumnAttribute();

    /**
     * Add a where clause so that the role ID is not equal to the specified one.
     *
     * @param $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotRoleId($query, $id);

    /**
     * Add a where clause so that the role name is equal to the specified.
     *
     * @param $query
     * @param int $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRole($query, $role);

    /**
     * Search by default.
     *
     * @param $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search);

    /**
     * Search by role name.
     *
     * @param $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $search);

    /**
     * Sort by default.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $direction = 'desc');

    /**
     * Sort by role name.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByName($query, $direction = 'asc');
}
