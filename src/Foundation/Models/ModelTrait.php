<?php

namespace Laravelayers\Foundation\Models;

use Illuminate\Support\Facades\DB;
use Laravelayers\Pagination\PaginateManually;

/**
 * Additional methods for the Eloquent models.
 */
trait ModelTrait
{
    use JoinModel;
    use ModelToDto;
    use PaginateManually;

    /**
     * Column listing.
     *
     * @var array
     */
    protected static $columnListing = [];

    /**
     * Get the column listing.
     *
     * @return array
     */
    public function getColumnListing()
    {
        if (!isset(static::$columnListing[$this->getTable()])) {
            $columns = $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable());

            static::$columnListing[$this->getTable()] = $columns;
        }

        return static::$columnListing[$this->getTable()];
    }

    /**
     * Get the column types.
     *
     * @return array
     */
    public function getColumnTypes()
    {
        foreach ($this->getColumnListing() as $column) {
            $columnTypes[$column] = $this->getConnection()->getDoctrineColumn($this->getTable(), $column)->getType()->getName();
        }

        return $columnTypes ?? [];
    }
}