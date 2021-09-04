<?php

namespace Laravelayers\Contracts\Form;

/**
 * @see \Laravelayers\Form\Form
 */
interface Form
{
    /**
     * Get the form element from the item.
     *
     * @param string $key
     * @param array|\Traversable $value
     * @return array|\Traversable
     */
    public function getElement($key, $value);

    /**
     * Get the form elements from the items.
     *
     * @param \Traversable|array $items
     * @return \Illuminate\Support\Collection
     */
    public function getElements($items);
}
