<?php

namespace Laravelayers\Contracts\Foundation\Models;

use Laravelayers\Contracts\Foundation\Dto\Dtoable;
use Laravelayers\Contracts\Pagination\PaginateManually;

/**
 * @see \Laravelayers\Foundation\Models\ModelTrait
 */
interface Model extends Dtoable, JoinModel, PaginateManually
{
    /**
     * Get the column listing.
     *
     * @return array
     */
    public function getColumnListing();

    /**
     * Get the column types.
     *
     * @return array
     */
    public function getColumnTypes();
}
