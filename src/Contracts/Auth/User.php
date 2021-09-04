<?php

namespace Laravelayers\Contracts\Auth;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravelayers\Contracts\Foundation\Models\Model as ModelContract;

/**
 * @see \Laravelayers\Auth\Models\User
 */
interface User extends ModelContract, MustVerifyEmail
{
    /**
     * Define the relationship to the user actions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActions();

    /**
     * Get the column name for the "name".
     *
     * @return string
     */
    public function getNameColumnAttribute();

    /**
     * Get the email for the user.
     *
     * @return string
     */
    public function getEmailAttribute();

    /**
     * Get the column name for the "email".
     *
     * @return string
     */
    public function getEmailColumnAttribute();

    /**
     * Get the column name for the "password".
     *
     * @return string
     */
    public function getPasswordColumnAttribute();

    /**
     * Get the column name for the "remember token".
     *
     * @return string
     */
    public function getRememberTokenNameAttribute();

    /**
     * Get the name for the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierNameAttribute();

    /**
     * Add a where clause so that the user action is equal to the specified.
     *
     * @param $query
     * @param int $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereAction($query, $action);

    /**
     * Add a where clause so that the credentials for the user are equal to the specified.
     *
     * @param $query
     * @param string $column
     * @param string $value
     * @return $this
     */
    public function scopeWhereCredentials($query, $column, $value);

    /**
     * Search by default.
     *
     * @param $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search);

    /**
     * Search by name.
     *
     * @param $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $search);

    /**
     * Search by email.
     *
     * @param $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByEmail($query, $search);

    /**
     * Sort by default.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $direction = 'desc');

    /**
     * Sort by name.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByName($query, $direction = 'desc');

    /**
     * Sort by email.
     *
     * @param $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByEmail($query, $direction = 'desc');
}
