<?php

namespace Laravelayers\Contracts\Admin\Decorators;

/**
 * @see \Laravelayers\Admin\Decorators\DataDecorator
 */
interface DataDecorator extends Actions, Elements, Translator
{
    /**
     * Get default form elements.
     *
     * @return array
     */
    public function getDefaultElements();

    /**
     * Get the sort key.
     *
     * @return string
     */
    public function getSortKey();
}
