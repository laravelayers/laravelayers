<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

/**
 * @see \Laravelayers\Auth\Models\UserRoleAction
 */
interface UserRoleAction extends ModelContract
{
    /**
     * Define the relationship to the user roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userRoles();

    /**
     * Get the column name for the "role ID".
     *
     * @return string
     */
    public function getRoleColumnAttribute();

    /**
     * Get the column name for the "action".
     *
     * @return string
     */
    public function getActionColumnAttribute();

    /**
     * Get the column name for the "allowed".
     *
     * @return string
     */
    public function getAllowedColumnAttribute();

    /**
     * Add a where clause so that the role ID is equal to the specified.
     *
     * @param $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleId($query, $id);

    /**
     * Add a where clause so that the role IDs are equal to the specified.
     *
     * @param $query
     * @param int $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleIds($query, $ids);

    /**
     * Add a where clause so that the action names do not match the action names of the specified role ID.
     *
     * @param $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRoleIdNot($query, $id);

    /**
     * Add a where clause so that the action name is equal to the specified.
     *
     * @param $query
     * @param int $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereName($query, $name);

    /**
     * Add a where clause so that the action names are equal to the specified.
     *
     * @param $query
     * @param int|array $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNames($query, $names);

    /**
     * Search by default.
     *
     * @param $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search);

    /**
     * Search by action.
     *
     * @param $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByAction($query, $search);

    /**
     * Group by action name.
     *
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupByAction($query);

    /**
     * Sort by default.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $direction = 'desc');

    /**
     * Sort by action.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByAction($query, $direction = 'asc');

    /**
     * Sort by allowed.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByAllowed($query, $direction = 'desc');
}
