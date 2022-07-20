<?php

namespace Laravelayers\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

    /**
     * Indicates whether to use the first page number in the URL.
     *
     * @var string
     */
    public static $isFirstPage = false;

    /**
     * Get the URL for a given page number.
     *
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {
        if ($page > 1 || static::$isFirstPage) {
            return parent::url($page);
        }

        $url = $this->path();

        $parameters = Arr::except($this->query, 'page');

        if ($parameters) {
            $url .= (Str::contains($this->path(), '?') ? '&' : '?')
                . Arr::query($parameters);
        }

        return $url . $this->buildFragment();
    }
}
