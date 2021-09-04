<?php

namespace Laravelayers\Contracts\Admin\Decorators;

/**
 * @see \Laravelayers\Admin\Decorators\Actions
 */
interface Actions
{
    /**
     * Checks if the current action is one of the specified.
     *
     * @param string|array $actions
     * @return bool
     */
    public function isAction($actions);

    /**
     * Get the actions.
     *
     * @return \Laravelayers\Form\Decorators\FormDecorator
     */
    public function getActions();

    /**
     * Get a translation for the given action key.
     *
     * @param string $key
     * @param array $replace
     * @param bool $empty
     * @param string $locale
     * @return string
     */
    public static function transOfAction($key = null, $replace = [], $empty = false, $locale = null);
}
