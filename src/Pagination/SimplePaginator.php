<?php

namespace Laravelayers\Pagination;

use Illuminate\Pagination\Paginator as BasePaginator;
use Laravelayers\Contracts\Foundation\Dto\Dtoable;

class SimplePaginator extends BasePaginator implements Dtoable
{
    use PaginatorTrait;

    /**
     * The default "simple" pagination view.
     *
     * @var string
     */
    public static $defaultSimpleView = 'pagination::foundation';

    /**
     * The default view of the summary number of elements per page.
     *
     * @var string
     */
    public static $defaultSummaryView = '';
}
