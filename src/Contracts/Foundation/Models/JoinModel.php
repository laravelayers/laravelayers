<?php

namespace Laravelayers\Contracts\Foundation\Models;

/**
 * @see \Laravelayers\Foundation\Models\JoinModel
 */
interface JoinModel
{
    /**
     * Automatically join tables of related Eloquent models using the "join" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param string $type
     * @param bool $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinModel($query, $relationNames, $operatorOrClosure = '=', $type = 'inner', $where = false);

    /**
     * Automatically join tables of related Eloquent models using the "join where" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param string $type
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinModelWhere($query, $relationNames, $operatorOrClosure = '=', $type = 'inner');

    /**
     * Automatically join tables of related Eloquent models using the "left join" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param bool $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeftJoinModel($query, $relationNames, $operatorOrClosure = '=', $where = false);

    /**
     * Automatically join tables of related Eloquent models using the "left join where" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeftJoinModelWhere($query, $relationNames, $operatorOrClosure = '=');

    /**
     * Automatically join tables of related Eloquent models using the "right Join" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param bool $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRightJoinModel($query, $relationNames, $operatorOrClosure = '=', $where = false);

    /**
     * Automatically join tables of related Eloquent models using the "rightJoinWhere" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRightJoinModelWhere($query, $relationNames, $operatorOrClosure = '=');
}
