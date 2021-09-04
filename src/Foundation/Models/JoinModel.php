<?php

namespace Laravelayers\Foundation\Models;

use Closure;

/**
 * Additional methods to automatically join tables related Eloquent models.
 *
 * Based on {@link http://laravel-tricks.com/tricks/automatic-join-on-eloquent-models-with-relations-setup}
 *
 * @method \Illuminate\Database\Query\Builder JoinModel(string|array $relationNames, string|callback $operatorOrClosure = '=', string $type = 'inner', bool $where = false)
 * @method \Illuminate\Database\Query\Builder JoinModelWhere(string|array $relationNames, string|callback $operatorOrClosure = '=', string $type = 'inner')
 * @method \Illuminate\Database\Query\Builder LeftJoinModel(string|array $relationNames, string|callback $operatorOrClosure = '=', string $type = 'inner', bool $where = false)
 * @method \Illuminate\Database\Query\Builder LeftJoinModelWhere(string|array $relationNames, string|callback $operatorOrClosure = '=')
 * @method \Illuminate\Database\Query\Builder RightJoinModel(string|array $relationNames, string|callback $operatorOrClosure = '=', bool $where = false)
 * @method \Illuminate\Database\Query\Builder RightJoinModelWhere(string|array $relationNames, string|callback $operatorOrClosure = '=')
 */
trait JoinModel
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
    public function scopeJoinModel($query, $relationNames, $operatorOrClosure = '=', $type = 'inner', $where = false)
    {
        // If $relationNames = array('query' => 'value', 'relationNames' => 'value', 'columns' => 'value')
        if (!empty($relationNames['relationNames'])) {
            extract($relationNames);
        }

        // If the value of the first element of the array is really a Closure instance
        // Then save the Closure instance in the variable $relationClosure
        // Next, use the Closure instance of the variable $relationClosure
        // for the last of name in the variable $relationNames
        if(is_array($relationNames))
        {
            // Example:
            // $relationNames = ['product.price' => function($query) {
            //     $query->Sort();
            // }];
            $relationNameKey = key($relationNames);
            $relationNameCurrent = current($relationNames);
            if ($relationNameCurrent  instanceof Closure) {
                $relationNames = $relationNameKey;
                $relationClosure = $relationNameCurrent;
            }
        }

        // If the parameter $operatorOrClosure is really a Closure instance
        // Then save the Closure instance in the variable $joinClosure
        // Next, use the Closure instance of the variable $joinClosure
        // for the last of name in the variable $relationNames
        if($operatorOrClosure instanceof Closure) {
            $joinClosure = $operatorOrClosure;
            $operatorOrClosure = '=';
        }

        // Create an array of nested related tables with parameters
        if (!is_array($relationNames)) {
            $relationNames = explode('.', $relationNames);
            foreach($relationNames as $key => $relationName) {
                $relationNames[$key] = [
                    0 => $relationName,
                    1 => $operatorOrClosure,
                    2 => $type,
                    3 => $where,
                    4 => null
                ];
            }

            // Use the Closure instance of the variable $joinClosure
            // for the last of name in the variable $relationNames
            // To add a query to join
            if (!empty($joinClosure)) {
                $relationNames[$key][1] = $joinClosure;
            }

            // Use the Closure instance of the variable $relationClosure
            // for the last of name in the variable $relationNames
            if (!empty($relationClosure)) {
                $relationNames[$key][4] = $relationClosure;
            }
        }

        // Set the arguments of the current table
        $relationNameFirst = array_shift($relationNames);
        $relationName = $relationNameFirst[0];

        if (!empty($relationNameFirst[1])) {
            $operatorOrClosure = $relationNameFirst[1];
        }

        if (!empty($relationNameFirst[2])) {
            $type = $relationNameFirst[2];
        }

        if (!empty($relationNameFirst[3])) {
            $where = $relationNameFirst[3];
        }

        // Get related table
        $relation = $this->$relationName();
        $table = $relation->getRelated()->getTable();
        $first = $relation->getQualifiedParentKeyName();

        // Checking the existence of the table in the query
        $is_table = false;
        if (!empty($query->getQuery()->joins)) {
            foreach($query->getQuery()->joins as $join) {
                if (strtolower($table) == strtolower($join->table)) {
                    $is_table = true;
                    break;
                }
            }
        }

        // Make a join
        if (!$is_table) {
            // If the parameter $operatorOrClosure is really a Closure instance
            // Add a query to join
            // Else make a join
            if ($operatorOrClosure instanceof Closure) {
                $query->join($table, $operatorOrClosure);
            } else {
                $relation_method = class_basename(get_class($relation));

                if ($relation_method == 'BelongsToMany') {
                    $second = $relation->getQualifiedForeignPivotKeyName();
                    $query->join($relation->getTable(), $first, $operatorOrClosure, $second, $type, $where);

                    $first = $relation->getQualifiedRelatedPivotKeyName();
                    $second = $relation->getRelated()->getQualifiedKeyName();
                } elseif ($relation_method == 'BelongsTo') {
                    //TODO-WHEN-UPDATING-LARAVEL: to 5.8
                    //$first = $relation->getQualifiedForeignKeyName();
                    $first = $relation->getQualifiedForeignKey();
                    $second = $relation->getQualifiedOwnerKeyName();
                } else {
                    $second = $relation->getQualifiedForeignKeyName();
                }

                $query->join($table, $first, $operatorOrClosure, $second, $type, $where);
            }

            // Get columns
            if (!empty($query->getQuery()->columns) && !$query->getQuery()->columns[0]) {
                $query->getQuery()->columns = null;
            } else {
                if (empty($query->getQuery()->columns)
                    || current($query->getQuery()->columns) == '*'
                    || end($query->getQuery()->columns) == '*'
                ) {
                    if(!empty($query->getQuery()->columns)) {
                        $columns = $query->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
                    }

                    if(count($query->getQuery()->columns ?? []) <= 1) {
                        $query->select($this->getTable() . '.*');
                    } else {
                        array_pop($query->getQuery()->columns);
                    }
                }

                if (!empty($columns)) {
                    foreach ($query->getConnection()->getSchemaBuilder()->getColumnListing($table) as $related_column) {
                        if($table . '.' .$related_column != $second) {
                            if (in_array($related_column, $columns)) {
                                $query->selectRaw("`$table`.`$related_column` AS `$table.$related_column`");
                            } else {
                                $columns[] = $related_column;
                                $query->selectRaw("`$table`.`$related_column`");
                            }
                        }
                    }
                }
            }
        }

        // Add the Closure instance to the query
        if (!empty($relationNameFirst[4])) {
            $model = get_class($relation->getRelated());

            $modelQuery = $model::setQuery($query->getQuery());

            call_user_func($relationNameFirst[4], $modelQuery);

            $query = $query->setQuery($modelQuery->getQUery());
        }

        // Return submodel
        if (!empty($relationNames)) {
            $submodel = get_class($relation->getRelated());

            if (empty($columns)) {
                $columns = [];
            }

            return $submodel::JoinModel(compact('query', 'relationNames', 'columns'), $operatorOrClosure, $type, $where);
        }

        return $query;
    }

    /**
     * Automatically join tables of related Eloquent models using the "join where" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param string $type
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinModelWhere($query, $relationNames, $operatorOrClosure = '=', $type = 'inner')
    {
        return $query->JoinModel($relationNames, $operatorOrClosure, $type, true);
    }

    /**
     * Automatically join tables of related Eloquent models using the "left join" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param bool $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeftJoinModel($query, $relationNames, $operatorOrClosure = '=', $where = false)
    {
        return $query->JoinModel($relationNames, $operatorOrClosure, 'left', $where);
    }

    /**
     * Automatically join tables of related Eloquent models using the "left join where" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeftJoinModelWhere($query, $relationNames, $operatorOrClosure = '=')
    {
        return $query->JoinModelWhere($relationNames, $operatorOrClosure, 'left');
    }

    /**
     * Automatically join tables of related Eloquent models using the "right Join" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @param bool $where
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRightJoinModel($query, $relationNames, $operatorOrClosure = '=', $where = false)
    {
        return $query->JoinModel($relationNames, $operatorOrClosure, 'right', $where);
    }

    /**
     * Automatically join tables of related Eloquent models using the "rightJoinWhere" model method.
     *
     * @param Model|\Illuminate\Database\Query\Builder $query
     * @param string|array $relationNames
     * @param string|callback $operatorOrClosure
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeRightJoinModelWhere($query, $relationNames, $operatorOrClosure = '=')
    {
        return $query->JoinModelWhere($relationNames, $operatorOrClosure, 'right');
    }
}