<?php

namespace Laravelayers\Contracts\Admin\Decorators;

use Illuminate\Http\Request;

/**
 * @see \Laravelayers\Admin\Decorators\Elements
 */
interface Elements
{
    /**
     * Get form elements.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getElements();

    /**
     * Set a HTTP request.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * Get a translation for the given element key.
     *
     * @param string $key
     * @param array $replace
     * @param bool $empty
     * @param string $locale
     * @return string
     */
    public static function transOfElement($key = null, $replace = [], $empty = false, $locale = null);
}
