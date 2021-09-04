<?php

namespace Laravelayers\Contracts\Auth;

use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

/**
 * @see \Laravelayers\Auth\Models\UserAction
 */
interface UserAction extends ModelContract
{
    /**
     * Define the relationship to the users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users();

    /**
     * Define the relationship to the user roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoles();

    /**
     * Define the relationship to the user role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userRole();

    /**
     * Get the column name for the "user ID".
     *
     * @return string
     */
    public function getUserColumnAttribute();

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
     * Get the column name for the "ip".
     *
     * @return string
     */
    public function getIpColumnAttribute();

    /**
     * Add a where clause so that the user ID is equal to the specified.
     *
     * @param $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserId($query, $id);

    /**
     * Add a where clause so that the user IDs are equal to the specified.
     *
     * @param $query
     * @param int $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserIds($query, $ids);

    /**
     * Add a where clause so that the action names do not match the action names of the specified user ID.
     *
     * @param $query
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUserIdNot($query, $id);

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
     * Add a condition that the privileges contain roles.
     *
     * @param $query
     * @return mixed
     */
    public function scopeIsRole($query);

    /**
     * Search by default.
     *
     * @param $query
     * @param $search
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
     * Search byip.
     *
     * @param $query
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByIp($query, $search);

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
