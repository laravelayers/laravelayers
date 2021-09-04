<?php

namespace Laravelayers\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Laravelayers\Contracts\Foundation\Dto\Dtoable;

class Paginator extends LengthAwarePaginator implements Dtoable
{
    use PaginatorTrait;

    /**
     * The default pagination view.
     *
     * @var string
     */
    public static $defaultView = 'pagination::foundation';

    /**
     * The default view of the summary number of elements per page.
     *
     * @var string
     */
    public static $defaultSummaryView = 'pagination::summary';
}
