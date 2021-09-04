<?php

namespace Laravelayers\Pagination;


trait PaginatorTrait
{
    /**
     * Convert the Paginator instance in the DTO format.
     *
     * @see \Laravelayers\Foundation\Dto\Dto
     * @return array
     */
    public function toDto()
    {
        return [
            'items' => (clone $this)->setCollection(
                $this->getCollection()->toDto()
            ),
        ];
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->items = clone $this->items;
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return string
     */
    public function summary($view = null, $data = [])
    {
        $view = $view ?: static::$defaultSummaryView;

        return $view ? $this->render($view, $data) : '';
    }
}
